<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\GoodsReturnRequest;
use Modules\Procurement\Models\ReplacementDelivery;
use Illuminate\Support\Str;

class GoodsReturnRequestController extends Controller
{
    /**
     * Display a listing of GRRs
     */
    public function index(Request $request)
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        $filter = $request->get('filter', 'all');
        $view = $request->get('view', 'buyer'); // Default to buyer view

        $query = GoodsReturnRequest::with([
            'goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.vendorCompany',
            'goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'createdBy',
        ]);

        if ($view === 'vendor') {
            $query->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            });
        } else {
            $query->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }

        // Apply filters
        if ($filter === 'pending') {
            $query->where('resolution_status', 'pending');
        } elseif ($filter === 'resolved') {
            $query->where('resolution_status', 'resolved');
        } elseif ($filter === 'in_progress') {
            $query->whereIn('resolution_status', ['approved_by_vendor', 'rejected_by_vendor']);
        }

        $grrList = $query->latest()->paginate(10)->appends(['filter' => $filter, 'view' => $view]);

        // Counts for badges (also filtered by view)
        $pendingQuery = GoodsReturnRequest::where('resolution_status', 'pending');
        $resolvedQuery = GoodsReturnRequest::where('resolution_status', 'resolved');

        if ($view === 'vendor') {
            $pendingQuery->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            });
            $resolvedQuery->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            });
        } else {
            $pendingQuery->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
            $resolvedQuery->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }

        $pendingCount = $pendingQuery->count();
        $resolvedCount = $resolvedQuery->count();

        return view('procurement.grr.index', compact('grrList', 'filter', 'pendingCount', 'resolvedCount', 'view'));
    }

    /**
     * Display the specified GRR
     */
    public function show(GoodsReturnRequest $goodsReturnRequest)
    {
        $selectedCompanyId = session('selected_company_id');

        $goodsReturnRequest->load([
            'goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.vendorCompany',
            'goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'createdBy',
            'debitNote',
            'replacementDelivery',
        ]);

        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Check if user is buyer or vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to view this GRR.');
        }

        return view('procurement.grr.show', compact('goodsReturnRequest', 'isBuyer', 'isVendor'));
    }

    /**
     * Store a newly created GRR
     */
    public function store(Request $request)
    {
        $request->validate([
            'goods_receipt_item_id' => 'required|exists:goods_receipt_items,id',
            'issue_type' => 'required|in:damaged,rejected,wrong_item',
            'quantity_affected' => 'required|integer|min:1',
            'issue_description' => 'nullable|string|max:1000',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
        ]);

        $grItem = GoodsReceiptItem::with('goodsReceipt.purchaseOrder.purchaseRequisition')->findOrFail($request->goods_receipt_item_id);

        // Validate quantity
        if ($request->quantity_affected > $grItem->quantity_received) {
            return back()->with('error', 'Quantity affected cannot exceed quantity received.');
        }

        $selectedCompanyId = session('selected_company_id');
        if ($grItem->goodsReceipt->purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create GRR.');
        }

        // Handle photo uploads
        $photoEvidence = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('grr-evidence', 'public');
                $photoEvidence[] = $path;
            }
        }

        DB::beginTransaction();
        try {
            $grr = GoodsReturnRequest::create([
                'goods_receipt_item_id' => $request->goods_receipt_item_id,
                'issue_type' => $request->issue_type,
                'quantity_affected' => $request->quantity_affected,
                'issue_description' => $request->issue_description,
                'photo_evidence' => $photoEvidence,
                'created_by' => Auth::id(),
            ]);

            // Update GR Item status
            $grItem->update([
                'item_status' => $request->issue_type == 'damaged' ? 'damaged' : 'rejected',
                'has_issue' => true,
            ]);

            DB::commit();

            // TODO: Send notification to vendor

            return redirect()->route('procurement.grr.show', $grr)
                ->with('success', 'Goods Return Request created successfully. Vendor will be notified.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create GRR: ' . $e->getMessage());
        }
    }

    /**
     * Update resolution type for a GRR
     */
    public function updateResolution(Request $request, GoodsReturnRequest $goodsReturnRequest)
    {
        $request->validate([
            'resolution_type' => 'required|in:price_adjustment,replacement,return_refund',
        ]);

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only buyer with 'approve goods return' permission can set resolution type
        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId || !Auth::user()->hasCompanyPermission($selectedCompanyId, 'approve goods return')) {
            abort(403, 'Only authorized buyers can set resolution type.');
        }

        $goodsReturnRequest->update([
            'resolution_type' => $request->resolution_type,
        ]);

        return back()->with('success', 'Resolution type updated. Waiting for vendor response.');
    }

    /**
     * Vendor responds to GRR (approve/reject)
     */
    public function vendorResponse(Request $request, GoodsReturnRequest $goodsReturnRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'vendor_notes' => 'nullable|string|max:500',
        ]);

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only vendor can respond
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Only vendor can respond to GRR.');
        }

        if ($request->action === 'approve') {
            $goodsReturnRequest->update([
                'resolution_status' => 'approved_by_vendor',
                'vendor_notes' => $request->vendor_notes,
            ]);

            // If price adjustment, create debit note
            if ($goodsReturnRequest->resolution_type === 'price_adjustment') {
                return redirect()->route('procurement.debit-notes.create', $goodsReturnRequest)
                    ->with('success', 'GRR approved. Please create Debit Note for price adjustment.');
            }

            // If replacement - move to awaiting shipping
            if ($goodsReturnRequest->resolution_type === 'replacement') {
                $goodsReturnRequest->update([
                    'resolution_status' => 'awaiting_replacement_shipping',
                ]);

                return redirect()->route('procurement.grr.show', $goodsReturnRequest)
                    ->with('success', 'GRR approved for replacement. Please ship the replacement items and provide tracking information.');
            }

            return back()->with('success', 'GRR approved by vendor.');

        } else {
            $goodsReturnRequest->update([
                'resolution_status' => 'rejected_by_vendor',
                'vendor_notes' => $request->vendor_notes,
            ]);

            return back()->with('info', 'GRR rejected by vendor. Please negotiate with vendor.');
        }
    }

    /**
     * Vendor ships the replacement items
     */
    public function shipReplacement(Request $request, GoodsReturnRequest $goodsReturnRequest)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:100',
            'expected_delivery_date' => 'nullable|date',
        ]);

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Only vendor can ship replacements.');
        }

        DB::beginTransaction();
        try {
            // Create or update ReplacementDelivery
            ReplacementDelivery::updateOrCreate(
                ['goods_return_request_id' => $goodsReturnRequest->id],
                [
                    'rd_number' => 'RD-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                    'original_goods_receipt_id' => $goodsReturnRequest->goodsReceiptItem->goods_receipt_id,
                    'expected_delivery_date' => $request->expected_delivery_date,
                    'tracking_number' => $request->tracking_number,
                    'status' => 'shipped',
                ]
            );

            $goodsReturnRequest->update([
                'resolution_status' => 'replacement_shipped',
            ]);

            DB::commit();

            return back()->with('success', 'Replacement items marked as shipped.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update shipping info: ' . $e->getMessage());
        }
    }

    /**
     * Buyer confirms receipt of replacement items
     */
    public function confirmReplacementReceipt(GoodsReturnRequest $goodsReturnRequest)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only buyer with 'access goods receipt' permission can confirm receipt
        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId || !Auth::user()->hasCompanyPermission($selectedCompanyId, 'access goods receipt')) {
            abort(403, 'Only authorized buyers can confirm receipt.');
        }

        DB::beginTransaction();
        try {
            if ($goodsReturnRequest->replacementDelivery) {
                $goodsReturnRequest->replacementDelivery->update([
                    'status' => 'received',
                    'actual_delivery_date' => now(),
                    'received_by' => Auth::id(),
                ]);
            }

            $goodsReturnRequest->update([
                'resolution_status' => 'resolved',
                'resolved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Replacement receipt confirmed. GRR is now resolved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to confirm receipt: ' . $e->getMessage());
        }
    }

    /**
     * Mark GRR as resolved
     */
    public function resolve(GoodsReturnRequest $goodsReturnRequest)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Both buyer and vendor can mark as resolved
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized.');
        }

        $goodsReturnRequest->update([
            'resolution_status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'GRR marked as resolved.');
    }
}
