<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\DebitNote;
use Modules\Procurement\Models\GoodsReturnRequest;

class ProcurementApprovalController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        $user = Auth::user();

        if (!$selectedCompanyId) {
            return redirect()->back()->with('error', 'Please select a company first.');
        }

        // --- BUYER SIDE APPROVALS ---

        // 1. Purchase Requisitions (Pending Approval)
        $pendingPRs = collect();
        if ($user->hasCompanyPermission($selectedCompanyId, 'approve pr')) {
            $pendingPRs = PurchaseRequisition::with(['user', 'items'])
                ->where('company_id', $selectedCompanyId)
                ->where(function ($q) {
                    $q->where('approval_status', 'like', 'pending%')
                        ->orWhere('tender_status', 'pending_winner_approval');
                })
                ->latest()
                ->get();
        }

        // 2. Invoices (Awaiting Purchasing/Finance Approval)
        $pendingInvoices = collect();
        if ($user->hasCompanyPermission($selectedCompanyId, 'approve invoice')) {
            $pendingInvoices = Invoice::with(['purchaseOrder.vendorCompany'])
                ->whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->whereIn('status', ['vendor_approved', 'purchasing_approved', 'matched'])
                ->latest()
                ->get()
                ->filter(function ($invoice) use ($user, $selectedCompanyId) {
                    // Filter based on specific invoice stage vs user permission/role if needed
                    // For now, if they can approve invoices, show them what needs action in their company
                    if ($invoice->status === 'vendor_approved')
                        return true; // Needs Purchasing
                    if ($invoice->status === 'purchasing_approved')
                        return true; // Needs Finance
                    if ($invoice->status === 'matched')
                        return true; // Needs Initial Check
                    return false;
                });
        }

        // 3. Debit Notes (Awaiting Buyer Confirmation)
        $pendingDebitNotes = collect();
        if ($user->hasCompanyPermission($selectedCompanyId, 'approve debit notes')) {
            $pendingDebitNotes = DebitNote::with(['purchaseOrder.vendorCompany'])
                ->whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        // 4. GRRs (Resolution set by Buyer, or awaiting final confirmation)
        $pendingGRRs = collect();
        if ($user->hasCompanyPermission($selectedCompanyId, 'approve goods return')) {
            $pendingGRRs = GoodsReturnRequest::with(['goodsReceiptItem.purchaseOrderItem', 'creator'])
                ->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->where(function ($q) {
                    $q->where('resolution_status', 'pending') // Needs resolution choice
                        ->orWhere('resolution_status', 'replacement_shipped'); // Needs receipt confirmation
                })
                ->latest()
                ->get();
        }

        // --- VENDOR SIDE APPROVALS ---

        // 5. POs (Pending Vendor Acceptance)
        $pendingPOs = PurchaseOrder::with(['buyerCompany', 'purchaseRequisition'])
            ->where('vendor_company_id', $selectedCompanyId)
            ->where('status', 'pending_vendor_acceptance')
            ->latest()
            ->get();

        // 6. Invoices (Awaiting Vendor Head Approval)
        $vendorHeadInvoices = collect();
        if ($user->hasCompanyPermission($selectedCompanyId, 'approve invoice')) {
            $vendorHeadInvoices = Invoice::with(['purchaseOrder.buyerCompany'])
                ->where('vendor_company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        return view('procurement.approvals.index', compact(
            'pendingPRs',
            'pendingInvoices',
            'pendingDebitNotes',
            'pendingGRRs',
            'pendingPOs',
            'vendorHeadInvoices'
        ));
    }
}
