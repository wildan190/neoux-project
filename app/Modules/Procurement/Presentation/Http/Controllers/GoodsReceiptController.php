<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\GoodsReceipt;
use App\Modules\Procurement\Domain\Models\GoodsReceiptItem;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use App\Notifications\GoodsReceiptCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoodsReceiptController extends Controller
{
    public function create(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        // Only Buyer can create GR
        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create Goods Receipt.');
        }

        // Check if PO is already fully received
        $purchaseOrder->load([
            'items.purchaseRequisitionItem.catalogueItem',
            'items.goodsReceiptItems.goodsReturnRequest',
            'goodsReceipts.items.goodsReturnRequest',
        ]);

        $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
        $totalReceived = $purchaseOrder->items->sum('quantity_received');

        // Check if there are any pending replacements (allows GR even if "fully received")
        $hasReplacementPending = false;
        $replacementItems = [];
        foreach ($purchaseOrder->goodsReceipts as $gr) {
            foreach ($gr->items as $grItem) {
                if (
                    $grItem->goodsReturnRequest &&
                    $grItem->goodsReturnRequest->resolution_type === 'replacement' &&
                    $grItem->goodsReturnRequest->resolution_status === 'resolved'
                ) {
                    $hasReplacementPending = true;
                    $replacementItems[] = [
                        'grr' => $grItem->goodsReturnRequest,
                        'item' => $grItem,
                    ];
                }
            }
        }

        // Block if fully received AND no replacement pending
        if ($totalReceived >= $totalOrdered && ! $hasReplacementPending) {
            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('error', 'All items have been fully received. No more goods receipt can be created.');
        }

        $isReplacement = $hasReplacementPending && $totalReceived >= $totalOrdered;

        return view('procurement.gr.create', compact('purchaseOrder', 'isReplacement', 'replacementItems'));
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create Goods Receipt.');
        }

        $request->validate([
            'received_at' => 'required|date',
            'delivery_note' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.condition' => 'nullable|string|max:255',
        ]);

        // Additional validation: Check if receiving more than ordered (skip for replacement mode)
        $isReplacement = $request->has('is_replacement') && $request->is_replacement == '1';

        $purchaseOrder->load([
            'items.purchaseRequisitionItem.catalogueItem',
            'items.goodsReceiptItems.goodsReturnRequest',
        ]);

        foreach ($request->items as $itemData) {
            $poItem = $purchaseOrder->items->where('id', $itemData['po_item_id'])->first();
            if (! $poItem) {
                return back()->with('error', 'Invalid purchase order item.');
            }

            $nowReceiving = $itemData['quantity_received'];

            // Skip validation if no items to receive
            if ($nowReceiving == 0) {
                continue;
            }

            if ($isReplacement) {
                // For replacement mode, validate against replacement quantity from GRR
                $maxReplacementQty = 0;
                foreach ($poItem->goodsReceiptItems ?? [] as $grItem) {
                    if (
                        $grItem->goodsReturnRequest &&
                        $grItem->goodsReturnRequest->resolution_type === 'replacement' &&
                        $grItem->goodsReturnRequest->resolution_status === 'resolved'
                    ) {
                        $maxReplacementQty += $grItem->goodsReturnRequest->quantity_affected;
                    }
                }

                if ($nowReceiving > $maxReplacementQty) {
                    return back()->with('error', "Cannot receive {$nowReceiving} replacement units of '{$poItem->purchaseRequisitionItem->catalogueItem->name}'. Only {$maxReplacementQty} units pending replacement.");
                }
            } else {
                // Normal mode: check against remaining quantity
                $alreadyReceived = $poItem->quantity_received ?? 0;
                $totalWillBeReceived = $alreadyReceived + $nowReceiving;

                if ($totalWillBeReceived > $poItem->quantity_ordered) {
                    return back()->with('error', "Cannot receive {$nowReceiving} units of '{$poItem->purchaseRequisitionItem->catalogueItem->name}'. Only ".($poItem->quantity_ordered - $alreadyReceived).' units remaining.');
                }
            }
        }

        DB::beginTransaction();
        try {
            // Generate GR Number
            $grNumber = 'GR-'.date('Y').'-'.strtoupper(Str::random(6));

            $goodsReceipt = GoodsReceipt::create([
                'gr_number' => $grNumber,
                'purchase_order_id' => $purchaseOrder->id,
                'received_by_user_id' => Auth::id(),
                'received_at' => $request->received_at,
                'delivery_note_number' => $request->delivery_note,
                'notes' => $request->notes,
            ]);

            $allReceived = true;
            $anyReceived = false;
            $issueCount = 0;

            foreach ($request->items as $itemData) {
                if ($itemData['quantity_received'] > 0) {
                    $itemStatus = $itemData['item_status'] ?? 'good';
                    $hasIssue = in_array($itemStatus, ['damaged', 'rejected']);

                    $grItem = GoodsReceiptItem::create([
                        'goods_receipt_id' => $goodsReceipt->id,
                        'purchase_order_item_id' => $itemData['po_item_id'],
                        'quantity_received' => $itemData['quantity_received'],
                        'condition_notes' => $itemData['condition'] ?? null,
                        'item_status' => $itemStatus,
                        'has_issue' => $hasIssue,
                    ]);

                    // If item has issue, auto-create GRR
                    if ($hasIssue) {
                        $issueType = $itemStatus === 'damaged' ? 'damaged' : 'rejected';

                        \App\Modules\Procurement\Domain\Models\GoodsReturnRequest::create([
                            'goods_receipt_item_id' => $grItem->id,
                            'issue_type' => $issueType,
                            'quantity_affected' => $itemData['quantity_received'],
                            'issue_description' => $itemData['condition'] ?? null,
                            'created_by' => Auth::id(),
                        ]);

                        $issueCount++;
                    }

                    // Update PO Item quantity received
                    $poItem = $purchaseOrder->items()->find($itemData['po_item_id']);
                    $poItem->increment('quantity_received', $itemData['quantity_received']);

                    // Automatic Stock Update (Disabled)
                    // The stock update now happens only via Warehouse Scan for security.
                    // if (!$hasIssue && $itemStatus === 'good') {
                    //    // Stock decrement/increment logic removed
                    // }

                    $anyReceived = true;
                }
            }

            // Check overall PO status
            $purchaseOrder->refresh();
            $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
            $totalReceived = $purchaseOrder->items->sum('quantity_received');

            if ($totalReceived >= $totalOrdered) {
                $purchaseOrder->update(['status' => 'full_delivery']);
            } elseif ($totalReceived > 0) {
                $purchaseOrder->update(['status' => 'partial_delivery']);
            }

            // Notify Vendor
            if ($purchaseOrder->vendorCompany && $purchaseOrder->vendorCompany->user) {
                $purchaseOrder->vendorCompany->user->notify(new GoodsReceiptCreated($goodsReceipt));
            }

            DB::commit();

            $message = 'Goods Receipt created successfully!';
            if ($issueCount > 0) {
                $message .= " {$issueCount} item(s) reported with issues - GRR created automatically.";
            }

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create Goods Receipt: '.$e->getMessage());
        }
    }

    public function print($id)
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        $goodsReceipt = \App\Modules\Procurement\Domain\Models\GoodsReceipt::findOrFail($id);
        $purchaseOrder = $goodsReceipt->purchaseOrder;

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (! $isBuyer && ! $isVendor) {
            abort(403, 'Unauthorized to print this Delivery Order.');
        }

        $goodsReceipt->load([
            'items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'purchaseOrder.vendorCompany',
            'purchaseOrder.purchaseRequisition.company',
            'receivedBy',
        ]);

        return view('procurement.gr.print', compact('goodsReceipt'));
    }

    public function downloadPdf($id)
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        $goodsReceipt = \App\Modules\Procurement\Domain\Models\GoodsReceipt::findOrFail($id);
        $purchaseOrder = $goodsReceipt->purchaseOrder;

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (! $isBuyer && ! $isVendor) {
            abort(403, 'Unauthorized to download this Delivery Order.');
        }

        $goodsReceipt->load([
            'items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'purchaseOrder.vendorCompany',
            'purchaseOrder.purchaseRequisition.company',
            'receivedBy',
        ]);

        $pdf = \PDF::loadView('procurement.gr.pdf', compact('goodsReceipt'));

        return $pdf->download('DO-'.$goodsReceipt->gr_number.'.pdf');
    }
}
