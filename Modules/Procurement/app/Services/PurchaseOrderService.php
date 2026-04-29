<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\InvoiceItem;
use Modules\Procurement\Emails\PurchaseOrderSent;
use Modules\Procurement\Notifications\PurchaseOrderReceived;
use Modules\Procurement\Notifications\PurchaseOrderConfirmed;
use Modules\Procurement\Notifications\PaymentReceived;
use Modules\Procurement\Jobs\ProcessDisbursementJob;
use Modules\Procurement\Services\ThreeWayMatchingService;

class PurchaseOrderService
{
    /**
     * Store a new Purchase Order
     */
    public function createPurchaseOrder(array $data, $createdByUserId)
    {
        return DB::transaction(function () use ($data, $createdByUserId) {
            $purchaseOrder = PurchaseOrder::create(array_merge($data, [
                'created_by_user_id' => $createdByUserId,
                'status' => 'pending_vendor_acceptance',
            ]));

            // Notify Vendor
            try {
                if ($purchaseOrder->offer && $purchaseOrder->offer->user) {
                    $vendorUser = $purchaseOrder->offer->user;
                    \Illuminate\Support\Facades\Mail::to($vendorUser->email)->send(new PurchaseOrderSent($purchaseOrder));
                    $vendorUser->notify(new PurchaseOrderReceived($purchaseOrder));
                }
            } catch (\Exception $e) {
                Log::error('Failed to notify vendor on PO creation: ' . $e->getMessage());
            }

            return $purchaseOrder;
        });
    }

    /**
     * Vendor confirms the PO and generates Proforma Invoice
     */
    public function confirmPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        return DB::transaction(function () use ($purchaseOrder) {
            if ($purchaseOrder->status !== 'issued') {
                throw new \Exception('Purchase Order is already ' . $purchaseOrder->status);
            }

            $purchaseOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Generate Proforma Invoice
            $invoiceNumber = 'PRO-' . date('Y') . '-' . strtoupper(Str::random(6));
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'purchase_order_id' => $purchaseOrder->id,
                'vendor_company_id' => $purchaseOrder->vendor_company_id,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'total_amount' => $purchaseOrder->total_amount,
                'status' => 'proforma',
            ]);

            foreach ($purchaseOrder->items as $poItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'purchase_order_item_id' => $poItem->id,
                    'quantity_invoiced' => $poItem->quantity_ordered,
                    'unit_price' => $poItem->unit_price,
                    'subtotal' => $poItem->subtotal,
                ]);
            }

            // Notify Buyer
            if ($purchaseOrder->createdBy) {
                $purchaseOrder->createdBy->notify(new PurchaseOrderConfirmed($purchaseOrder));
            }

            return $purchaseOrder;
        });
    }

    /**
     * Verify payment status with Midtrans and trigger next steps
     */
    public function verifyPayment(PurchaseOrder $purchaseOrder, $orderId)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

        $status = \Midtrans\Transaction::status($orderId);
        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus = $status->fraud_status ?? null;

        $isPaid = ($transactionStatus === 'settlement') ||
                  ($transactionStatus === 'capture' && $fraudStatus === 'accept');

        if ($isPaid && $purchaseOrder->escrow_status !== 'paid') {
            $purchaseOrder->update([
                'escrow_status'    => 'paid',
                'escrow_paid_at'   => now(),
                'escrow_reference' => $orderId,
            ]);

            // Notify Vendor
            $this->notifyVendorOfPayment($purchaseOrder);

            // Immediate Disbursement Check
            if (in_array($purchaseOrder->status, ['full_delivery', 'received'])) {
                $matchingService = new ThreeWayMatchingService();
                $matchResult = $matchingService->matchEscrow($purchaseOrder);

                if (($matchResult['status'] ?? null) === 'matched') {
                    ProcessDisbursementJob::dispatch($purchaseOrder);
                    return ['status' => 'paid', 'immediate_disbursement' => true];
                }
            }

            return ['status' => 'paid', 'immediate_disbursement' => false];
        }

        return ['status' => $transactionStatus, 'immediate_disbursement' => false];
    }

    /**
     * Release Escrow funds to Vendor bank account
     */
    public function releaseEscrow(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->escrow_status !== 'paid') {
            throw new \Exception('Escrow must be in "paid" status to release.');
        }

        $matchingService = new ThreeWayMatchingService();
        $result = $matchingService->matchEscrow($purchaseOrder);

        if ($result['status'] === 'matched') {
            ProcessDisbursementJob::dispatch($purchaseOrder);
            return true;
        }

        throw new \Exception('3-Way Matching failed: ' . implode(', ', $result['variances'] ?? ['Unknown error']));
    }

    /**
     * Generate PO from Purchase Requisition and its winning offer
     */
    public function generateFromRequisition(\Modules\Procurement\Models\PurchaseRequisition $purchaseRequisition, $authUserId)
    {
        return DB::transaction(function () use ($purchaseRequisition, $authUserId) {
            if (!$purchaseRequisition->winning_offer_id) {
                throw new \Exception('No winning offer selected for this requisition.');
            }

            $offer = \Modules\Procurement\Models\PurchaseRequisitionOffer::with('items')->findOrFail($purchaseRequisition->winning_offer_id);

            if ($offer->status !== 'accepted') {
                throw new \Exception('The selected winner has not been approved yet.');
            }

            if ($purchaseRequisition->purchaseOrder) {
                throw new \Exception('Purchase Order already exists for this requisition.');
            }

            // Generate PO Number
            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(6));

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'company_id' => $purchaseRequisition->company_id,
                'purchase_requisition_id' => $purchaseRequisition->id,
                'offer_id' => $offer->id,
                'vendor_company_id' => $offer->company_id,
                'created_by_user_id' => $authUserId,
                'approved_by_user_id' => $authUserId,
                'total_amount' => $offer->total_price,
                'status' => 'pending_vendor_acceptance',
                'purchase_type' => 'tender',
                'month' => date('F'),
                'currency' => 'IDR',
            ]);

            // Negotiation Ratio
            $originalTotal = $offer->items()->sum('subtotal');
            $negotiatedTotal = $offer->total_price;
            $ratio = ($originalTotal > 0) ? ($negotiatedTotal / $originalTotal) : 1;

            foreach ($offer->items as $offerItem) {
                \Modules\Procurement\Models\PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_requisition_item_id' => $offerItem->purchase_requisition_item_id,
                    'quantity_ordered' => $offerItem->quantity_offered,
                    'quantity_received' => 0,
                    'unit_price' => $offerItem->unit_price * $ratio,
                    'subtotal' => $offerItem->subtotal * $ratio,
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'total_inc_tax' => $offerItem->subtotal * $ratio,
                    'price_idr' => $offerItem->unit_price * $ratio,
                    'price_original' => $offerItem->unit_price * $ratio,
                ]);
            }

            $purchaseRequisition->update([
                'po_generated_at' => now(),
                'status' => 'ordered',
            ]);

            // Notify Vendor
            $this->notifyVendorOfNewPO($purchaseOrder, $offer);

            return $purchaseOrder;
        });
    }

    /**
     * Vendor accepts or rejects the PO
     */
    public function updateVendorAcceptance(PurchaseOrder $purchaseOrder, $status, $notes = null)
    {
        if ($purchaseOrder->status !== 'pending_vendor_acceptance') {
            throw new \Exception('This PO is not pending acceptance.');
        }

        $purchaseOrder->update([
            'status' => $status,
            $status === 'issued' ? 'vendor_accepted_at' : 'vendor_rejected_at' => now(),
            'vendor_notes' => $notes,
        ]);

        if ($status === 'issued' && $purchaseOrder->createdBy) {
            $purchaseOrder->createdBy->notify(new \Modules\Procurement\Notifications\PurchaseOrderConfirmed($purchaseOrder));
        }

        return $purchaseOrder;
    }

    /**
     * Create a Repeat Order based on a previous PO
     */
    public function createRepeatOrder(PurchaseOrder $purchaseOrder, $companyId, $authUserId)
    {
        return DB::transaction(function () use ($purchaseOrder, $companyId, $authUserId) {
            // Create a DIRECT Purchase Requisition
            $prNumber = 'PR-RO-' . date('Y') . '-' . strtoupper(Str::random(6));
            $requisition = \Modules\Procurement\Models\PurchaseRequisition::create([
                'pr_number' => $prNumber,
                'company_id' => $companyId,
                'user_id' => $authUserId,
                'title' => 'Repeat Order: ' . $purchaseOrder->po_number,
                'description' => 'Manual repeat order based on previous transaction ' . $purchaseOrder->po_number,
                'status' => 'ordered',
                'approval_status' => 'approved',
                'tender_status' => 'draft',
                'type' => 'direct',
                'source_po_id' => $purchaseOrder->id,
            ]);

            foreach ($purchaseOrder->items as $item) {
                \Modules\Procurement\Models\PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $requisition->id,
                    'catalogue_item_id' => $item->purchaseRequisitionItem->catalogue_item_id,
                    'quantity' => $item->quantity_ordered,
                    'price' => $item->unit_price,
                ]);
            }

            // Generate New PO
            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(6));
            $newPo = PurchaseOrder::create([
                'po_number' => $poNumber,
                'company_id' => $companyId,
                'purchase_requisition_id' => $requisition->id,
                'vendor_company_id' => $purchaseOrder->vendor_company_id,
                'created_by_user_id' => $authUserId,
                'approved_by_user_id' => $authUserId,
                'total_amount' => $purchaseOrder->total_amount,
                'status' => 'issued',
                'vendor_accepted_at' => now(),
                'purchase_type' => 'direct',
                'month' => date('F'),
                'currency' => 'IDR',
            ]);

            foreach ($requisition->items as $prItem) {
                \Modules\Procurement\Models\PurchaseOrderItem::create([
                    'purchase_order_id' => $newPo->id,
                    'purchase_requisition_item_id' => $prItem->id,
                    'quantity_ordered' => $prItem->quantity,
                    'quantity_received' => 0,
                    'unit_price' => $prItem->price,
                    'subtotal' => $prItem->quantity * $prItem->price,
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'total_inc_tax' => $prItem->quantity * $prItem->price,
                    'price_idr' => $prItem->price,
                    'price_original' => $prItem->price,
                ]);
            }

            return $newPo;
        });
    }

    protected function notifyVendorOfNewPO(PurchaseOrder $purchaseOrder, $offer)
    {
        try {
            $vendorUser = $offer->user;
            if ($vendorUser) {
                \Illuminate\Support\Facades\Mail::to($vendorUser->email)->send(new PurchaseOrderSent($purchaseOrder));
                $vendorUser->notify(new PurchaseOrderReceived($purchaseOrder));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify vendor on PO generation: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Notify vendor of payment received
     */
    protected function notifyVendorOfPayment(PurchaseOrder $purchaseOrder)
    {
        try {
            $recipients = collect();
            if ($purchaseOrder->vendorCompany && $purchaseOrder->vendorCompany->user) {
                $recipients->push($purchaseOrder->vendorCompany->user);
            }
            if ($purchaseOrder->offer && $purchaseOrder->offer->user) {
                $recipients->push($purchaseOrder->offer->user);
            }
            $recipients->unique('id')->each(function ($user) use ($purchaseOrder) {
                $user->notify(new PaymentReceived($purchaseOrder));
            });
        } catch (\Exception $e) {
            Log::error('Payment notify error: ' . $e->getMessage());
        }
    }
}
