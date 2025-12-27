<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use App\Modules\Procurement\Domain\Models\PurchaseOrderItem;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use App\Notifications\PurchaseOrderConfirmed;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        $currentView = request('view', 'buyer');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            } else {
                return redirect()->back()->with('error', 'Please select a company first.');
            }
        }

        // Separate POs by role
        // Buyer POs: where I'm the buyer (can receive goods)
        $buyerPOs = PurchaseOrder::with(['purchaseRequisition', 'vendorCompany', 'createdBy'])
            ->whereHas('purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
            ->latest()
            ->paginate(10, ['*'], 'buyer_page');

        // Vendor POs: where I'm the vendor (can create invoice)
        $vendorPOs = PurchaseOrder::with(['purchaseRequisition', 'vendorCompany', 'createdBy'])
            ->where('vendor_company_id', $selectedCompanyId)
            ->latest()
            ->paginate(10, ['*'], 'vendor_page');

        return view('procurement.po.index', compact('buyerPOs', 'vendorPOs', 'selectedCompanyId', 'currentView'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to view this Purchase Order.');
        }

        $purchaseOrder->load([
            'items.purchaseRequisitionItem.catalogueItem',
            'items.goodsReceiptItems.goodsReturnRequest',
            'vendorCompany',
            'createdBy',
            'goodsReceipts.items.goodsReturnRequest',
            'goodsReceipts.receivedBy',
            'invoices',
        ]);

        return view('procurement.po.show', compact('purchaseOrder', 'isBuyer', 'isVendor'));
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only Vendor can confirm
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to confirm this Purchase Order.');
        }

        if ($purchaseOrder->status !== 'issued') {
            return back()->with('error', 'Purchase Order is already ' . $purchaseOrder->status);
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            DB::commit();

            // Notify Buyer (the user who created the PO)
            if ($purchaseOrder->createdBy) {
                $purchaseOrder->createdBy->notify(new PurchaseOrderConfirmed($purchaseOrder));
                \Illuminate\Support\Facades\Log::info('PO Confirmation notification sent to: ' . $purchaseOrder->createdBy->email);
            }

            return redirect()->back()->with('success', 'Purchase Order confirmed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to confirm Purchase Order: ' . $e->getMessage());
        }
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to print this Purchase Order.');
        }

        $purchaseOrder->load(['items.purchaseRequisitionItem.catalogueItem', 'vendorCompany', 'createdBy', 'purchaseRequisition.company']);

        return view('procurement.po.print', compact('purchaseOrder'));
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to download this Purchase Order.');
        }

        $purchaseOrder->load(['items.purchaseRequisitionItem.catalogueItem', 'vendorCompany', 'createdBy', 'purchaseRequisition.company']);

        $pdf = Pdf::loadView('procurement.po.pdf', compact('purchaseOrder'));

        return $pdf->download('PO-' . $purchaseOrder->po_number . '.pdf');
    }

    public function generate(PurchaseRequisition $purchaseRequisition)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only PR owner can generate PO
        if ($purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to generate PO for this requisition.');
        }

        if (!$purchaseRequisition->winning_offer_id) {
            return back()->with('error', 'No winning offer selected for this requisition.');
        }

        if ($purchaseRequisition->purchaseOrder) {
            return back()->with('error', 'Purchase Order already exists for this requisition.');
        }

        $offer = PurchaseRequisitionOffer::with('items')->findOrFail($purchaseRequisition->winning_offer_id);

        DB::beginTransaction();
        try {
            // Generate PO Number (PO-YYYY-RANDOM)
            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(6));

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'purchase_requisition_id' => $purchaseRequisition->id,
                'offer_id' => $offer->id,
                'vendor_company_id' => $offer->company_id,
                'created_by_user_id' => Auth::id(),
                'total_amount' => $offer->total_price,
                'status' => 'issued',
            ]);

            foreach ($offer->items as $offerItem) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_requisition_item_id' => $offerItem->purchase_requisition_item_id,
                    'quantity_ordered' => $offerItem->quantity_offered,
                    'quantity_received' => 0,
                    'unit_price' => $offerItem->unit_price,
                    'subtotal' => $offerItem->subtotal,
                ]);
            }

            $purchaseRequisition->update([
                'po_generated_at' => now(),
                'status' => 'ordered', // PO has been generated
            ]);

            DB::commit();

            // Send Email Notification to Vendor
            try {
                // Find contact person to email: The user who created the winning offer
                $vendorUser = $offer->user;
                if ($vendorUser) {
                    \Illuminate\Support\Facades\Mail::to($vendorUser->email)
                        ->send(new \App\Mail\PurchaseOrderSent($purchaseOrder));
                }
            } catch (\Exception $e) {
                // Don't rollback if email fails, just log it
                \Illuminate\Support\Facades\Log::error('Failed to send PO email to vendor: ' . $e->getMessage());
            }

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Purchase Order generated successfully! Notification has been sent to the vendor.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to generate Purchase Order: ' . $e->getMessage());
        }
    }
}
