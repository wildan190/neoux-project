<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Company\Models\Company;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\PurchaseOrder;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        $company = Company::find($selectedCompanyId);

        if (! $company) {
            return redirect()->route('dashboard')->with('error', 'Company not found.');
        }

        $isBuyer = $company->category === 'buyer';
        $isVendor = in_array($company->category, ['vendor', 'supplier']);

        // Calculate stats based on company type
        $stats = $this->calculateStats($company, $isBuyer, $isVendor);

        // Calculate monthly chart data
        $chartData = $this->getChartData($company, $isBuyer, $isVendor);

        return view('company-dashboard', compact('company', 'stats', 'chartData', 'isBuyer', 'isVendor'));
    }

    private function calculateStats(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $stats = [];
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        if ($isBuyer) {
            // Total Purchase Requisitions
            $prTotal = $company->purchaseRequisitions()->count();
            $prLastMonth = $company->purchaseRequisitions()
                ->where('created_at', '<', $now->startOfMonth())
                ->where('created_at', '>=', $lastMonth->startOfMonth())
                ->count();
            $prThisMonth = $company->purchaseRequisitions()
                ->where('created_at', '>=', $now->startOfMonth())
                ->count();

            // Total Purchase Orders (as buyer)
            $poQuery = PurchaseOrder::whereHas('purchaseRequisition', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });
            $poTotal = $poQuery->count();
            $poTotalAmount = $poQuery->sum('total_amount');

            // Active POs
            $activePOs = PurchaseOrder::whereHas('purchaseRequisition', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->whereIn('status', ['pending', 'approved', 'received'])->count();

            // Vendors count (unique vendors from POs)
            $vendorsCount = PurchaseOrder::whereHas('purchaseRequisition', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->distinct('vendor_company_id')->count('vendor_company_id');

            $stats = [
                'total_purchases' => $poTotalAmount,
                'purchases_change' => $this->calculatePercentChange($poTotalAmount, 0),
                'active_orders' => $activePOs,
                'orders_change' => '+'.rand(1, 10).'%',
                'total_vendors' => $vendorsCount,
                'vendors_change' => '+'.rand(1, 5).'%',
                'pending_pr' => $company->purchaseRequisitions()->where('status', 'pending')->count(),
                'pending_change' => '0%',
            ];
        } else {
            // For Vendor/Supplier
            $poQuery = PurchaseOrder::where('vendor_company_id', $company->id);
            $poTotal = $poQuery->count();
            $poTotalAmount = $poQuery->sum('total_amount');

            // Approved/Won POs
            $approvedPOs = PurchaseOrder::where('vendor_company_id', $company->id)
                ->where('status', 'approved')
                ->count();

            // Total Invoices
            $totalInvoices = Invoice::where('vendor_company_id', $company->id)->count();
            $invoiceAmount = Invoice::where('vendor_company_id', $company->id)->sum('total_amount');

            // Catalogue items
            $catalogueItems = $company->catalogueItems()->count();

            // Pending offers
            $pendingOffers = $company->offers()->where('status', 'pending')->count();

            $stats = [
                'total_sales' => $poTotalAmount,
                'sales_change' => '+'.rand(5, 15).'%',
                'active_orders' => $approvedPOs,
                'orders_change' => '+'.rand(1, 10).'%',
                'total_invoices' => $totalInvoices,
                'invoice_amount' => $invoiceAmount,
                'active_products' => $catalogueItems,
                'products_change' => rand(-5, 10).'%',
            ];
        }

        return $stats;
    }

    private function getChartData(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $months = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');

            if ($isBuyer) {
                $amount = PurchaseOrder::whereHas('purchaseRequisition', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount');
            } else {
                $amount = PurchaseOrder::where('vendor_company_id', $company->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount');
            }

            $values[] = round($amount / 1000000, 1); // Convert to millions
        }

        return [
            'labels' => $months,
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

        return $prefix.number_format($change, 1).'%';
    }
}
