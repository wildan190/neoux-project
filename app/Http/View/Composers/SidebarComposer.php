<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use App\Modules\Procurement\Domain\Models\Invoice;
use App\Modules\Procurement\Domain\Models\GoodsReturnRequest;
use App\Modules\Procurement\Domain\Models\DebitNote;

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
            'my_requisitions' => 0,
            'purchase_orders' => 0,
            'invoices' => 0,
            'my_offers' => 0,
            'return_requests' => 0,
            'debit_notes' => 0,
            'all_requests' => 0,
        ];

        if ($selectedCompanyId) {
            // My Requisitions - PRs belonging to company that are pending approval
            $counts['my_requisitions'] = PurchaseRequisition::where('company_id', $selectedCompanyId)
                ->where('approval_status', 'pending')
                ->count();

            // Purchase Orders - Actionable for both buyer and vendor
            // Buyer: Status issued (waiting for confirmation)
            // Vendor: Status issued (needs to confirm)
            $counts['purchase_orders'] = PurchaseOrder::where(function ($query) use ($selectedCompanyId) {
                $query->whereHas('purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId); // Current company is buyer
                })->orWhere('vendor_company_id', $selectedCompanyId); // Current company is vendor
            })
                ->where('status', 'issued')
                ->count();

            // Invoices - Pending for both sides
            $counts['invoices'] = Invoice::where(function ($query) use ($selectedCompanyId) {
                $query->whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId); // Buyer side
                })->orWhere('vendor_company_id', $selectedCompanyId); // Vendor side
            })
                ->where('status', 'pending')
                ->count();

            // My Offers - Sent by company that are still pending
            $counts['my_offers'] = PurchaseRequisitionOffer::where('company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->count();

            // Return Requests - Pending resolution
            $counts['return_requests'] = GoodsReturnRequest::where(function ($query) use ($selectedCompanyId) {
                $query->whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId); // Buyer side
                })->orWhereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                    $q->where('vendor_company_id', $selectedCompanyId); // Vendor side
                });
            })
                ->where('resolution_status', 'pending')
                ->count();

            // Debit Notes - Pending approval
            $counts['debit_notes'] = DebitNote::where(function ($query) use ($selectedCompanyId) {
                $query->where('purchase_order_id', '!=', null)
                    ->whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                        $q->where('vendor_company_id', $selectedCompanyId); // Vendor needs to approve
                    });
            })
                ->whereNull('approved_by_vendor_at')
                ->count();

            // All Requests (Marketplace Feed) - PRs from OTHER companies that are open for bids
            // and current company hasn't bid on yet
            $counts['all_requests'] = PurchaseRequisition::where('company_id', '!=', $selectedCompanyId)
                ->where('tender_status', 'open')
                ->whereDoesntHave('offers', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->count();
        }

        $view->with('sidebarCounts', $counts);
    }
}
