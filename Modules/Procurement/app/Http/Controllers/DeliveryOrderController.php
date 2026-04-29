<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Procurement\Models\DeliveryOrder;
use Modules\Procurement\Models\DeliveryOrderItem;
use Modules\Procurement\Models\PurchaseOrder;

class DeliveryOrderController extends Controller
{
    /**
     * Show form to create DO from PO
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Only vendor can create DO
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create Delivery Order for this PO.');
        }

        if (!in_array($purchaseOrder->status, ['issued', 'confirmed', 'partial_delivery'])) {
            return back()->with('error', 'You can only create a Delivery Order for issued, confirmed, or partially delivered Purchase Orders.');
        }

        // Ensure payment has been made (escrow deposited)
        if ($purchaseOrder->escrow_status !== 'paid' && $purchaseOrder->escrow_status !== 'released') {
            return back()->with('error', 'Dana Escrow belum dideposit oleh Buyer. Pengiriman hanya dapat diatur setelah pembayaran masuk ke Escrow.');
        }

        // Check if anything is left to ship
        $purchaseOrder->load('items.deliveryOrderItems.deliveryOrder');
        $hasRemaining = false;
        foreach ($purchaseOrder->items as $item) {
            if ($item->quantity_shipped < $item->quantity_ordered) {
                $hasRemaining = true;
                break;
            }
        }

        if (! $hasRemaining) {
            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('error', 'All items for this Purchase Order have already been arranged for delivery.');
        }

        return view('procurement::vendor.do.create', compact('purchaseOrder'));
    }

    /**
     * Store new DO
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized.');
        }

        // Ensure payment has been made (escrow deposited)
        if ($purchaseOrder->escrow_status !== 'paid' && $purchaseOrder->escrow_status !== 'released') {
            return back()->with('error', 'Dana Escrow belum dideposit oleh Buyer. Pengiriman hanya dapat diatur setelah pembayaran masuk ke Escrow.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_shipped' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items.deliveryOrderItems.deliveryOrder');
            $doNumber = 'DO-'.date('Y').'-'.strtoupper(Str::random(6));

            $deliveryOrder = DeliveryOrder::create([
                'purchase_order_id' => $purchaseOrder->id,
                'do_number' => $doNumber,
                'status' => 'pending',
                'created_by_user_id' => Auth::id(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $item = $purchaseOrder->items->find($itemData['purchase_order_item_id']);
                $qtyShipped = $itemData['quantity_shipped'];

                if ($qtyShipped > 0) {
                    $remaining = $item->quantity_ordered - $item->quantity_shipped;
                    if ($qtyShipped > $remaining) {
                        throw new \Exception("Quantity to ship ({$qtyShipped}) exceeds remaining quantity ({$remaining}) for item ".$item->purchaseRequisitionItem->catalogueItem->name);
                    }

                    DeliveryOrderItem::create([
                        'delivery_order_id' => $deliveryOrder->id,
                        'purchase_order_item_id' => $item->id,
                        'quantity_shipped' => $qtyShipped,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Delivery Order '.$doNumber.' created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create Delivery Order: '.$e->getMessage());
        }
    }

    /**
     * Mark DO as shipped
     */
    public function markAsShipped(Request $request, DeliveryOrder $deliveryOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if ($deliveryOrder->purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $deliveryOrder->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $request->tracking_number,
        ]);

        // Update PO status to shipping per user flow
        $deliveryOrder->purchaseOrder->update(['status' => 'shipping']);

        // Notify Buyer (PO Creator)
        try {
            if ($deliveryOrder->purchaseOrder->createdBy) {
                $deliveryOrder->purchaseOrder->createdBy->notify(new \Modules\Procurement\Notifications\DeliveryOrderShipped($deliveryOrder));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send DO shipping notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Delivery Order marked as shipped with Tracking Number: '.$request->tracking_number);
    }
    /**
     * Mark DO as delivered (Signed by Buyer)
     */
    public function markAsDelivered(DeliveryOrder $deliveryOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Only Buyer can sign
        $isBuyer = ($deliveryOrder->purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($deliveryOrder->purchaseOrder->company_id == $selectedCompanyId);
        if (!$isBuyer) {
            abort(403, 'Unauthorized. Only the buyer can sign the delivery order.');
        }

        if ($deliveryOrder->status !== 'shipped') {
            return back()->with('error', 'Delivery Order must be in shipped status to be signed.');
        }

        $deliveryOrder->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return back()->with('success', 'Delivery Order ' . $deliveryOrder->do_number . ' has been signed and confirmed received.');
    }

    /**
     * Print DO
     */
    public function print(DeliveryOrder $deliveryOrder)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $deliveryOrder->purchaseOrder;

        // Authorization: Buyer or Vendor
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to view this Delivery Order.');
        }

        $deliveryOrder->load([
            'items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'purchaseOrder.vendorCompany',
            'purchaseOrder.purchaseRequisition.company',
            'purchaseOrder.createdBy'
        ]);

        return view('procurement::do.print', compact('deliveryOrder', 'purchaseOrder'));
    }
}
