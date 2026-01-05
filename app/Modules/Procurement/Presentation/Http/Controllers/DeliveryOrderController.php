<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\DeliveryOrder;
use App\Modules\Procurement\Domain\Models\DeliveryOrderItem;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        if ($purchaseOrder->status !== 'issued' && $purchaseOrder->status !== 'partial_delivery') {
            return back()->with('error', 'You can only create a Delivery Order for issued or partially delivered Purchase Orders.');
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

        if (!$hasRemaining) {
            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('error', 'All items for this Purchase Order have already been arranged for delivery.');
        }

        return view('procurement.do.create', compact('purchaseOrder'));
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

        $request->validate([
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_shipped' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items.deliveryOrderItems.deliveryOrder');
            $doNumber = 'DO-' . date('Y') . '-' . strtoupper(Str::random(6));

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
                        throw new \Exception("Quantity to ship ({$qtyShipped}) exceeds remaining quantity ({$remaining}) for item " . $item->purchaseRequisitionItem->catalogueItem->name);
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
                ->with('success', 'Delivery Order ' . $doNumber . ' created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create Delivery Order: ' . $e->getMessage());
        }
    }

    /**
     * Mark DO as shipped
     */
    public function markAsShipped(DeliveryOrder $deliveryOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if ($deliveryOrder->purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized.');
        }

        $deliveryOrder->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return back()->with('success', 'Delivery Order marked as shipped.');
    }
}
