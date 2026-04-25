<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Company\Models\Company;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Catalogue\Models\CatalogueItem;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $pendingCompanies = Company::where('status', 'pending')->count();
        $activeCompanies = Company::where('status', 'active')->count();
        $declinedCompanies = Company::where('status', 'declined')->count();

        // ── Global Market Metrics ──────────────────────────────────────────────
        $totalPOValue = (float) PurchaseOrder::sum('total_amount');
        $totalPOCount = PurchaseOrder::count();

        // Recent Global Activity (POs + PRs)
        // We'll combine them or just show latest POs as they represent realized value
        $recentActivity = PurchaseOrder::with(['buyerCompany', 'vendorCompany', 'createdBy'])
            ->latest()
            ->take(6)
            ->get();

        // Top Selling Products (Including Historical)
        // Since historical data doesn't have catalogue_item_id, we group by item_name as fallback
        $topStats = DB::table('purchase_order_items')
            ->select(
                'item_name',
                'purchase_requisition_item_id',
                DB::raw('count(*) as transaction_count'),
                DB::raw('sum(quantity_ordered) as total_sold')
            )
            ->groupBy('item_name', 'purchase_requisition_item_id')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();

        // Hydrate Products for View
        $topProducts = collect();
        foreach ($topStats as $stat) {
            $catalogueItem = null;
            
            // Try to find catalogue item via PR Item
            if ($stat->purchase_requisition_item_id) {
                $prItem = DB::table('purchase_requisition_items')->find($stat->purchase_requisition_item_id);
                if ($prItem && $prItem->catalogue_item_id) {
                    $catalogueItem = CatalogueItem::with(['category', 'primaryImage'])->find($prItem->catalogue_item_id);
                }
            }

            // Fallback: Try to find by name
            if (!$catalogueItem) {
                $catalogueItem = CatalogueItem::with(['category', 'primaryImage'])->where('name', $stat->item_name)->first();
            }

            // If still not found, create a placeholder object
            if (!$catalogueItem) {
                $product = new \stdClass();
                $product->name = $stat->item_name;
                $product->category = (object)['name' => 'Historical'];
                $product->image_url = null;
            } else {
                $product = $catalogueItem;
                $product->image_url = $catalogueItem->primaryImage ? $catalogueItem->primaryImage->image_path : null;
            }

            $product->transaction_count = $stat->transaction_count;
            $product->total_sold = $stat->total_sold;
            $topProducts->push($product);
        }

        return view('admin::dashboard', compact(
            'totalCompanies',
            'pendingCompanies',
            'activeCompanies',
            'declinedCompanies',
            'totalPOValue',
            'totalPOCount',
            'recentActivity',
            'topProducts'
        ));
    }
}
