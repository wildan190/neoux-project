<?php

namespace Modules\User\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Procurement\Models\DebitNote;
use Modules\Procurement\Models\GoodsReturnRequest;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionOffer;

class SidebarComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

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
            'pending_prs' => 0,
            'pending_invoices' => 0,
        ];

        if ($selectedCompanyId) {
            // OPTIMIZATION:
            // We removed the heavy queries here because they are already being fetched
            // via AJAX by window.updateUnreadCount() in app.blade.php calling NotificationController@getUnreadCount.
            // This saves ~14 DB queries on every page load.
            // The badges will simply pop-in via JS after page load.
        }

        $view->with('sidebarCounts', $counts);
    }
}
