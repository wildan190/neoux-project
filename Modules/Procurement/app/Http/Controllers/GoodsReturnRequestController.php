<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\GoodsReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Http\Requests\StoreGRRRequest;
use Modules\Procurement\Http\Requests\UpdateGRRResolutionRequest;
use Modules\Procurement\Http\Requests\VendorGRRResponseRequest;

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

        $query = GoodsReturnRequest::with([
            'goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.vendorCompany',
            'goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'createdBy',
        ])
            ->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                    $q2->where('company_id', $selectedCompanyId);
                })
                    ->orWhere('vendor_company_id', $selectedCompanyId);
            });

        // Apply filters
        if ($filter === 'pending') {
            $query->where('resolution_status', 'pending');
        } elseif ($filter === 'resolved') {
            $query->where('resolution_status', 'resolved');
        } elseif ($filter === 'in_progress') {
            $query->whereIn('resolution_status', ['approved_by_vendor', 'rejected_by_vendor']);
        }

        $grrList = $query->latest()->paginate(10)->appends(['filter' => $filter]);

        // Counts for badges
        $pendingCount = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
            $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                $q2->where('company_id', $selectedCompanyId);
            })->orWhere('vendor_company_id', $selectedCompanyId);
        })->where('resolution_status', 'pending')->count();

        $resolvedCount = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
            $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                $q2->where('company_id', $selectedCompanyId);
            })->orWhere('vendor_company_id', $selectedCompanyId);
        })->where('resolution_status', 'resolved')->count();

        return view('procurement.grr.index', compact('grrList', 'filter', 'pendingCount', 'resolvedCount'));
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
    public function store(StoreGRRRequest $request)
    {

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
    public function updateResolution(UpdateGRRResolutionRequest $request, GoodsReturnRequest $goodsReturnRequest)
    {

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only buyer can set resolution type
        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Only buyer can set resolution type.');
        }

        $goodsReturnRequest->update([
            'resolution_type' => $request->resolution_type,
        ]);

        return back()->with('success', 'Resolution type updated. Waiting for vendor response.');
    }

    /**
     * Vendor responds to GRR (approve/reject)
     */
    public function vendorResponse(VendorGRRResponseRequest $request, GoodsReturnRequest $goodsReturnRequest)
    {

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only vendor can respond
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Only vendor can respond to GRR.');
        }

        if ($request->action === 'approve') {
            $goodsReturnRequest->update([
                'resolution_status' => 'approved_by_vendor',
            ]);

            // If price adjustment, create debit note
            if ($goodsReturnRequest->resolution_type === 'price_adjustment') {
                return redirect()->route('procurement.debit-notes.create', $goodsReturnRequest)
                    ->with('success', 'GRR approved. Please create Debit Note for price adjustment.');
            }

            // If replacement - mark as resolved (replacement tracking to be added later)
            if ($goodsReturnRequest->resolution_type === 'replacement') {
                $goodsReturnRequest->update([
                    'resolution_status' => 'resolved',
                    'resolved_at' => now(),
                ]);

                return redirect()->route('procurement.grr.show', $goodsReturnRequest)
                    ->with('success', 'GRR approved for replacement. Please ship replacement items to buyer.');
            }

            return back()->with('success', 'GRR approved by vendor.');

        } else {
            $goodsReturnRequest->update([
                'resolution_status' => 'rejected_by_vendor',
            ]);

            return back()->with('info', 'GRR rejected by vendor. Please negotiate with vendor.');
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
