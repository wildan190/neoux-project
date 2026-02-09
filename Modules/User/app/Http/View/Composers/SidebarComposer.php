<?php

namespace Modules\User\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisitionOffer;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\GoodsReturnRequest;
use Modules\Procurement\Models\DebitNote;

class SidebarComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        if (!$user)
            return;

        $selectedCompanyId = session('selected_company_id');

        $counts = [
            'notifications' => $user->unreadNotifications()->count(),
            // Buyer Counts
            'my_requisitions' => 0,
            'purchase_orders_buyer' => 0,
            'invoices_buyer' => 0,
            'return_requests_buyer' => 0,
            'debit_notes_buyer' => 0,
            // Vendor Counts
            'all_requests' => 0,
            'my_offers' => 0,
            'purchase_orders_vendor' => 0,
            'invoices_vendor' => 0,
            'return_requests_vendor' => 0,
            'debit_notes_vendor' => 0,
        ];

        if ($selectedCompanyId) {
            // === BUYER SIDE ===

            // My Requisitions - PRs belonging to company that are pending approval
            $counts['my_requisitions'] = PurchaseRequisition::where('company_id', $selectedCompanyId)
                ->where('approval_status', 'pending')
                ->count();

            // Buyer POs - Confirmed (Ready to receive)
            // Note: We count confirmed here as actionable for buyer to create GR
            $counts['purchase_orders_buyer'] = PurchaseOrder::whereHas('purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->where('status', 'confirmed')
                ->count();

            // Buyer Invoices - Pending Payment (Received from vendor)
            $counts['invoices_buyer'] = Invoice::whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->where('status', 'pending')
                ->count();

            // Buyer Return Requests - Pending (Sent to vendor, waiting response)
            // Maybe less actionable for buyer (waiting), but good to track
            $counts['return_requests_buyer'] = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->where('resolution_status', 'pending')
                ->count();

            // Buyer Debit Notes - Received (checking if approved?)
            // Usually buyer issues DN? No, in this system, GRR resolutions create DN.
            // If Buyer returns goods -> Vendor issues Credit Note/Debit Note? 
            // Model says "approved_by_vendor_at".
            $counts['debit_notes_buyer'] = DebitNote::whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->whereNull('approved_by_vendor_at')
                ->count();


            // === VENDOR SIDE ===

            // All Requests (Marketplace Feed) - PRs from OTHER companies
            $counts['all_requests'] = PurchaseRequisition::where('company_id', '!=', $selectedCompanyId)
                ->where('tender_status', 'open')
                ->whereDoesntHave('offers', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->count();

            // My Offers - Pending
            $counts['my_offers'] = PurchaseRequisitionOffer::where('company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->count();

            // Vendor POs - Issued (Needs Confirmation)
            $counts['purchase_orders_vendor'] = PurchaseOrder::where('vendor_company_id', $selectedCompanyId)
                ->where('status', 'issued')
                ->count();

            // Vendor Invoices - Pending (Sent to buyer)
            $counts['invoices_vendor'] = Invoice::where('vendor_company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->count();

            // Vendor Return Requests - Pending (Received from buyer, needs action)
            $counts['return_requests_vendor'] = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            })
                ->where('resolution_status', 'pending')
                ->count();

            // Vendor Debit Notes - Pending Approval
            $counts['debit_notes_vendor'] = DebitNote::where('purchase_order_id', '!=', null)
                ->whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                    $q->where('vendor_company_id', $selectedCompanyId);
                })
                ->whereNull('approved_by_vendor_at')
                ->count();
        }

        $view->with('sidebarCounts', $counts);
    }
}
