<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\DebitNote;
use App\Modules\Procurement\Domain\Models\GoodsReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
{
    /**
     * Display a listing of Debit Notes
     */
    public function index(Request $request)
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        $debitNotes = DebitNote::with([
            'goodsReturnRequest.goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
            'purchaseOrder.vendorCompany',
        ])
            ->whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                    $q2->where('company_id', $selectedCompanyId);
                })
                    ->orWhere('vendor_company_id', $selectedCompanyId);
            })
            ->latest()
            ->paginate(10);

        return view('procurement.debit-notes.index', compact('debitNotes'));
    }

    /**
     * Show the form for creating a new Debit Note
     */
    public function create(GoodsReturnRequest $goodsReturnRequest)
    {
        $selectedCompanyId = session('selected_company_id');

        $goodsReturnRequest->load([
            'goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.vendorCompany',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
        ]);

        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        // Only vendor can create debit note
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Only vendor can create Debit Note.');
        }

        // Calculate original amount for affected items
        $poItem = $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem;
        $originalAmount = $poItem->unit_price * $goodsReturnRequest->quantity_affected;

        return view('procurement.debit-notes.create', compact('goodsReturnRequest', 'originalAmount'));
    }

    /**
     * Store a newly created Debit Note
     */
    public function store(Request $request, GoodsReturnRequest $goodsReturnRequest)
    {
        $request->validate([
            'deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'deduction_amount' => 'required_without:deduction_percentage|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder;

        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Only vendor can create Debit Note.');
        }

        $poItem = $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem;
        $originalAmount = $poItem->unit_price * $goodsReturnRequest->quantity_affected;

        // Calculate deduction
        if ($request->deduction_percentage) {
            $deductionAmount = ($originalAmount * $request->deduction_percentage) / 100;
        } else {
            $deductionAmount = $request->deduction_amount;
        }

        $adjustedAmount = $originalAmount - $deductionAmount;

        DB::beginTransaction();
        try {
            $debitNote = DebitNote::create([
                'goods_return_request_id' => $goodsReturnRequest->id,
                'purchase_order_id' => $purchaseOrder->id,
                'original_amount' => $originalAmount,
                'adjusted_amount' => $adjustedAmount,
                'deduction_amount' => $deductionAmount,
                'reason' => $request->reason,
            ]);

            // Update GRR status
            $goodsReturnRequest->update([
                'resolution_status' => 'resolved',
                'resolved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('procurement.debit-notes.show', $debitNote)
                ->with('success', 'Debit Note created successfully. GRR has been resolved.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create Debit Note: '.$e->getMessage());
        }
    }

    /**
     * Display the specified Debit Note
     */
    public function show(DebitNote $debitNote)
    {
        $selectedCompanyId = session('selected_company_id');

        $debitNote->load([
            'goodsReturnRequest.goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'goodsReturnRequest.goodsReceiptItem.goodsReceipt',
            'purchaseOrder.purchaseRequisition.company',
            'purchaseOrder.vendorCompany.user',
        ]);

        $purchaseOrder = $debitNote->purchaseOrder;

        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (! $isBuyer && ! $isVendor) {
            abort(403, 'Unauthorized to view this Debit Note.');
        }

        return view('procurement.debit-notes.show', compact('debitNote', 'isBuyer', 'isVendor'));
    }

    /**
     * Print Debit Note
     */
    public function print(DebitNote $debitNote)
    {
        $selectedCompanyId = session('selected_company_id');

        $debitNote->load([
            'goodsReturnRequest.goodsReceiptItem.purchaseOrderItem.purchaseRequisitionItem.catalogueItem',
            'goodsReturnRequest.createdBy',
            'purchaseOrder.purchaseRequisition.company.user',
            'purchaseOrder.purchaseRequisition.user',
            'purchaseOrder.vendorCompany.user',
        ]);

        $purchaseOrder = $debitNote->purchaseOrder;

        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (! $isBuyer && ! $isVendor) {
            abort(403, 'Unauthorized to print this Debit Note.');
        }

        return view('procurement.debit-notes.print', compact('debitNote'));
    }

    /**
     * Buyer approves Debit Note
     */
    public function approve(DebitNote $debitNote)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $debitNote->purchaseOrder;

        // Only buyer can approve
        if ($purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Only buyer can approve Debit Note.');
        }

        $debitNote->update([
            'approved_by_vendor_at' => now(),
        ]);

        return back()->with('success', 'Debit Note approved. Invoice will be adjusted accordingly.');
    }
}
