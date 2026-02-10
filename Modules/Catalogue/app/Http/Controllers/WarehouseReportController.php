<?php

namespace Modules\Catalogue\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Catalogue\Models\StockMovement;

class WarehouseReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('selected_company_id');

        // Recent Movements
        $movements = StockMovement::where('company_id', $companyId)
            ->with(['item.product', 'user'])
            ->latest()
            ->paginate(20);

        // Stats Today
        $todayIn = StockMovement::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->where('type', 'in')
            ->sum('quantity');

        $todayOut = StockMovement::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->where('type', 'out')
            ->sum('quantity');

        // Top Moved Items (All time)
        $topItems = StockMovement::where('company_id', $companyId)
            ->select('catalogue_item_id', DB::raw('count(*) as count'), DB::raw('sum(quantity) as total_qty'))
            ->groupBy('catalogue_item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('item.product')
            ->get();

        return view('warehouse.report.index', compact('movements', 'todayIn', 'todayOut', 'topItems'));
    }
}
