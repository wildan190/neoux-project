<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\Procurement\Models\DebitNote;
use Modules\Procurement\Models\GoodsReturnRequest;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionOffer;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        $url = $this->sanitizeUrl($notification->data['url'] ?? route('notifications.index'));

        return redirect($url);
    }

    private function sanitizeUrl($url)
    {
        if (empty($url)) {
            return route('notifications.index');
        }

        // If it starts with http://localhost:8000, replace with current APP_URL
        $localhost = 'http://localhost:8000';
        if (str_contains($url, $localhost)) {
            return str_replace($localhost, config('app.url'), $url);
        }

        return $url;
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $selectedCompanyId = session('selected_company_id');

        $counts = [
            'notifications' => $user->unreadNotifications()->count(),
            // Buyer Counts
            'my_requisitions' => 0,
            'purchase_orders_buyer' => 0,
            'invoices_buyer' => 0,
            'return_requests_buyer' => 0,
            'debit_notes_buyer' => 0,
            'quick_approvals_buyer' => 0,
            'quick_approvals_vendor' => 0,
            // Vendor Counts
            'all_requests' => 0,
            'my_offers' => 0,
            'purchase_orders_vendor' => 0,
            'invoices_vendor' => 0,
            'return_requests_vendor' => 0,
            'debit_notes_vendor' => 0,
        ];

        if ($selectedCompanyId) {
            // My Requisitions - Pending Approvals for this company
            $counts['my_requisitions'] = PurchaseRequisition::where('company_id', $selectedCompanyId)
                ->where('approval_status', 'pending')
                ->count();

            // Buyer POs - Direct lookup using company_id on PO
            $counts['purchase_orders_buyer'] = PurchaseOrder::where('company_id', $selectedCompanyId)
                ->where('status', 'confirmed')
                ->count();

            // Buyer Invoices - Join via PurchaseOrder (which has company_id)
            $counts['invoices_buyer'] = Invoice::whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->where('status', 'pending')
                ->count();

            // Buyer Return Requests - Deep join simplified to join via PurchaseOrder
            $counts['return_requests_buyer'] = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->where('resolution_status', 'pending')
                ->count();

            // Buyer Debit Notes - Join via PurchaseOrder
            $counts['debit_notes_buyer'] = DebitNote::whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
                ->whereNull('approved_by_vendor_at')
                ->count();

            // Vendor Section: All Requests (Tenders from other companies)
            // Note: This is still a bit heavy, but indexed status 'open' helps.
            $counts['all_requests'] = PurchaseRequisition::where('company_id', '!=', $selectedCompanyId)
                ->where('tender_status', 'open')
                ->whereDoesntHave('offers', function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->count();

            // My Offers
            $counts['my_offers'] = PurchaseRequisitionOffer::where('company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->count();

            // Vendor POs - Direct lookup using vendor_company_id on PO
            $counts['purchase_orders_vendor'] = PurchaseOrder::where('vendor_company_id', $selectedCompanyId)
                ->where('status', 'pending_vendor_acceptance')
                ->count();

            // Vendor Invoices - Direct lookup using vendor_company_id on Invoice
            $counts['invoices_vendor'] = Invoice::where('vendor_company_id', $selectedCompanyId)
                ->where('status', 'pending')
                ->count();

            // Vendor Return Requests - Simplified join via PurchaseOrder
            $counts['return_requests_vendor'] = GoodsReturnRequest::whereHas('goodsReceiptItem.goodsReceipt.purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            })
                ->where('resolution_status', 'pending')
                ->count();

            // Vendor Debit Notes - Simplified join via PurchaseOrder
            $counts['debit_notes_vendor'] = DebitNote::whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            })
                ->whereNull('approved_by_vendor_at')
                ->count();

            // Unified Quick Approval Counts
            $counts['quick_approvals_buyer'] = $counts['my_requisitions'] + $counts['invoices_buyer'] + $counts['return_requests_buyer'] + $counts['debit_notes_buyer'];
            $counts['quick_approvals_vendor'] = $counts['purchase_orders_vendor'] + $counts['invoices_vendor'];
        }

        return response()->json($counts);
    }

    public function getLatestNotifications()
    {
        // Get last 5 notifications (mix of read and unread)
        $notifications = Auth::user()->notifications()->take(5)->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }
}
