<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Company\Models\Company;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $pendingCompanies = Company::where('status', 'pending')->count();
        $activeCompanies = Company::where('status', 'active')->count();
        $declinedCompanies = Company::where('status', 'declined')->count();

        // Recent Tender Activity (Awarded/Ordered PRs)
        $recentTenders = \Modules\Procurement\Models\PurchaseRequisition::whereIn('status', ['awarded', 'ordered'])
            ->with(['company', 'winningOffer.company', 'winningOffer.user', 'purchaseOrder'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Top Selling Products (Based on PO Items)
        // 1. Get stats via DB Query
        $topStats = \Illuminate\Support\Facades\DB::table('purchase_order_items')
            ->join('purchase_requisition_items', 'purchase_order_items.purchase_requisition_item_id', '=', 'purchase_requisition_items.id')
            ->select(
                'purchase_requisition_items.catalogue_item_id',
                \Illuminate\Support\Facades\DB::raw('count(*) as transaction_count'),
                \Illuminate\Support\Facades\DB::raw('sum(purchase_order_items.quantity_ordered) as total_sold')
            )
            ->groupBy('purchase_requisition_items.catalogue_item_id')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();

        // 2. Hydrate Models
        $topProducts = collect();
        if ($topStats->isNotEmpty()) {
            $items = \Modules\Catalogue\Models\CatalogueItem::with(['category', 'primaryImage'])
                ->whereIn('id', $topStats->pluck('catalogue_item_id'))
                ->get()
                ->keyBy('id');

            foreach ($topStats as $stat) {
                if ($item = $items->get($stat->catalogue_item_id)) {
                    // Attach stats to model instance for the view
                    $item->transaction_count = $stat->transaction_count;
                    $item->total_sold = $stat->total_sold;
                    // Helper property for the view
                    $item->image_url = $item->primaryImage ? $item->primaryImage->image_path : null;
                    $topProducts->push($item);
                }
            }
        }

        return view('admin.dashboard', compact(
            'totalCompanies',
            'pendingCompanies',
            'activeCompanies',
            'declinedCompanies',
            'recentTenders',
            'topProducts'
        ));
    }
}
