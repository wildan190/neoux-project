<?php

namespace App\Modules\Company\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Domain\Models\Company;
use App\Modules\Procurement\Domain\Models\Invoice;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use Carbon\Carbon;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        $company = Company::find($selectedCompanyId);

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Company not found.');
        }

        // Determine available roles
        $canBeBuyer = $company->category === 'buyer' || $company->category === 'both';
        $canBeVendor = in_array($company->category, ['vendor', 'supplier', 'both']);

        // Selected view mode (defaults to buyer if available, else vendor)
        $currentView = request('view', ($canBeBuyer ? 'buyer' : 'vendor'));

        // Force correct view if requested view is not available for this company
        if ($currentView === 'buyer' && !$canBeBuyer)
            $currentView = 'vendor';
        if ($currentView === 'vendor' && !$canBeVendor)
            $currentView = 'buyer';

        $isBuyer = $currentView === 'buyer';
        $isVendor = $currentView === 'vendor';

        // Calculate stats based on company type and selected view
        $stats = $this->calculateStats($company, $isBuyer, $isVendor);

        // Calculate monthly chart data
        $chartData = $this->getChartData($company, $isBuyer, $isVendor);

        // Get Tasklist
        $tasks = $this->getTasklist($company, $isBuyer, $isVendor);

        return view('company-dashboard', compact(
            'company',
            'stats',
            'chartData',
            'isBuyer',
            'isVendor',
            'canBeBuyer',
            'canBeVendor',
            'currentView',
            'tasks'
        ));
    }

    private function getTasklist(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $id = $company->id;
        $tasks = [];

        if ($isBuyer) {
            // Consolidated count for Buyer POs
            $poCounts = PurchaseOrder::where(function ($q) use ($id) {
                $q->whereHas('purchaseRequisition', fn($q2) => $q2->where('company_id', $id))
                    ->orWhere('company_id', $id);
            })
                ->selectRaw("
                SUM(CASE WHEN status = 'pending_vendor_acceptance' THEN 1 ELSE 0 END) as pending_acceptance,
                SUM(CASE WHEN status IN ('issued', 'partial_delivery') THEN 1 ELSE 0 END) as ready_to_receive
            ")
                ->first();

            $tasks = [
                [
                    'title' => 'PR Pending Approval',
                    'count' => PurchaseRequisition::where('company_id', $id)->where('status', 'pending')->count(),
                    'route' => route('procurement.pr.index', ['status' => 'pending']),
                    'icon' => 'clock',
                    'color' => 'amber'
                ],
                [
                    'title' => 'Waiting Vendor Acceptance',
                    'count' => (int) ($poCounts->pending_acceptance ?? 0),
                    'route' => route('procurement.po.index', ['view' => 'buyer', 'status' => 'pending_vendor_acceptance']),
                    'icon' => 'user-check',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Ready to Receive',
                    'count' => (int) ($poCounts->ready_to_receive ?? 0),
                    'route' => route('procurement.po.index', ['view' => 'buyer', 'status' => 'issued']),
                    'icon' => 'download',
                    'color' => 'green'
                ]
            ];
        } else {
            // Consolidated count for Vendor POs
            $poCounts = PurchaseOrder::where('vendor_company_id', $id)
                ->selectRaw("
                SUM(CASE WHEN status = 'pending_vendor_acceptance' THEN 1 ELSE 0 END) as new_orders,
                SUM(CASE WHEN status IN ('issued', 'partial_delivery') THEN 1 ELSE 0 END) as to_ship,
                SUM(CASE WHEN status = 'full_delivery' AND NOT EXISTS (SELECT 1 FROM invoices WHERE invoices.purchase_order_id = purchase_orders.id) THEN 1 ELSE 0 END) as need_invoice
            ")
                ->first();

            $tasks = [
                [
                    'title' => 'New Orders to Accept',
                    'count' => (int) ($poCounts->new_orders ?? 0),
                    'route' => route('procurement.po.index', ['view' => 'vendor', 'status' => 'pending_vendor_acceptance']),
                    'icon' => 'plus-circle',
                    'color' => 'primary'
                ],
                [
                    'title' => 'Orders to Ship / Deliver',
                    'count' => (int) ($poCounts->to_ship ?? 0),
                    'route' => route('procurement.po.index', ['view' => 'vendor', 'status' => 'issued']),
                    'icon' => 'truck',
                    'color' => 'emerald'
                ],
                [
                    'title' => 'Pending Offers',
                    'count' => PurchaseRequisitionOffer::where('company_id', $id)->where('status', 'pending')->count(),
                    'route' => route('procurement.pr.public-feed'),
                    'icon' => 'file-text',
                    'color' => 'amber'
                ],
                [
                    'title' => 'Fulfilled Orders to Invoice',
                    'count' => (int) ($poCounts->need_invoice ?? 0),
                    'route' => route('procurement.po.index', ['view' => 'vendor', 'status' => 'full_delivery', 'filter' => 'need_invoice']),
                    'icon' => 'dollar-sign',
                    'color' => 'indigo'
                ]
            ];
        }

        return array_filter($tasks, fn($task) => $task['count'] > 0);
    }


    private function calculateStats(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $id = $company->id;

        if ($isBuyer) {
            $poStats = PurchaseOrder::where(function ($q) use ($id) {
                $q->whereHas('purchaseRequisition', fn($q2) => $q2->where('company_id', $id))
                    ->orWhere('company_id', $id);
            })
                ->selectRaw("
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status IN ('pending', 'approved', 'received') THEN 1 ELSE 0 END) as active_count,
                COUNT(DISTINCT vendor_company_id) as vendors_count
            ")
                ->first();

            $pendingPRCount = PurchaseRequisition::where('company_id', $id)->where('status', 'pending')->count();

            return [
                'total_purchases' => $poStats->total_amount ?? 0,
                'purchases_change' => '+0%', // Mocked or calculated if needed
                'active_orders' => (int) $poStats->active_count,
                'orders_change' => '+0%',
                'total_vendors' => (int) $poStats->vendors_count,
                'vendors_change' => '+0%',
                'pending_pr' => $pendingPRCount,
                'pending_change' => '0%',
            ];
        } else {
            $poStats = PurchaseOrder::where('vendor_company_id', $id)
                ->selectRaw("
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as active_count
            ")
                ->first();

            $invoiceStats = Invoice::where('vendor_company_id', $id)
                ->selectRaw("COUNT(*) as count, SUM(total_amount) as total")
                ->first();

            $catalogueCount = $company->catalogueItems()->count();

            return [
                'total_sales' => $poStats->total_amount ?? 0,
                'sales_change' => '+0%',
                'active_orders' => (int) $poStats->active_count,
                'orders_change' => '+0%',
                'total_invoices' => (int) $invoiceStats->count,
                'invoice_amount' => $invoiceStats->total ?? 0,
                'active_products' => $catalogueCount,
                'products_change' => '0%',
            ];
        }
    }

    private function getChartData(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $id = $company->id;
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $query = PurchaseOrder::where('created_at', '>=', $sixMonthsAgo);

        if ($isBuyer) {
            $query->where(function ($q) use ($id) {
                $q->whereHas('purchaseRequisition', fn($q2) => $q2->where('company_id', $id))
                    ->orWhere('company_id', $id);
            });
        } else {
            $query->where('vendor_company_id', $id);
        }

        $monthlyData = $query->selectRaw("
                TO_CHAR(created_at, 'MM') as month, 
                TO_CHAR(created_at, 'YYYY') as year,
                SUM(total_amount) as amount
            ")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            $match = $monthlyData->first(function ($item) use ($date) {
                return (int) $item->month === (int) $date->month && (int) $item->year === (int) $date->year;
            });

            $values[] = round(($match->amount ?? 0) / 1000000, 1);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function calculatePercentChange($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        $prefix = $change >= 0 ? '+' : '';

        return $prefix . number_format($change, 1) . '%';
    }
}
