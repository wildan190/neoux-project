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

        if (!$selectedCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        $company = Company::find($selectedCompanyId);

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Company not found.');
        }

        $isBuyer = $company->category === 'buyer';
        $isVendor = in_array($company->category, ['vendor', 'supplier']);

        // Calculate stats based on company type
        $stats = $this->calculateStats($company, $isBuyer, $isVendor);

        // Calculate monthly chart data
        $chartData = $this->getChartData($company, $isBuyer, $isVendor);

        // Fetch Tasks
        $tasks = $this->getTasks($company, $isBuyer, $isVendor);

        return view('company-dashboard', compact('company', 'stats', 'chartData', 'isBuyer', 'isVendor', 'tasks'));
    }

    private function getTasks(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $tasks = [];
        $user = auth()->user();
        $companyId = $company->id;

        if ($isBuyer) {
            // 1. Purchasing Manager Tasks
            if ($user->hasCompanyPermission($companyId, 'approve pr')) {
                // Pending PRs
                $pendingPRs = $company->purchaseRequisitions()
                    ->where('approval_status', 'pending')
                    ->get();
                foreach ($pendingPRs as $pr) {
                    $tasks[] = [
                        'type' => 'pr_approval',
                        'title' => 'PR Approval Required',
                        'description' => "Requisition #{$pr->pr_number} needs your approval.",
                        'url' => route('procurement.pr.show', $pr),
                        'priority' => 'high',
                    ];
                }

                // Offers needing winner approval
                $pendingWinners = $company->purchaseRequisitions()
                    ->where('tender_status', 'pending_winner_approval')
                    ->with('winningOffer')
                    ->get();
                foreach ($pendingWinners as $pr) {
                    $tasks[] = [
                        'type' => 'winner_approval',
                        'title' => 'Bid Winner Approval',
                        'description' => "Winner for #{$pr->pr_number} needs final approval.",
                        'url' => route('procurement.offers.show', $pr->winning_offer_id),
                        'priority' => 'high',
                    ];
                }

                // Invoices needing purchasing approval
                $invoicesPurchasing = Invoice::whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                    ->where('status', 'vendor_approved')
                    ->get();
                foreach ($invoicesPurchasing as $inv) {
                    $tasks[] = [
                        'type' => 'invoice_purchasing',
                        'title' => 'Invoice Validation',
                        'description' => "Invoice #{$inv->invoice_number} needs purchasing validation.",
                        'url' => route('procurement.invoices.show', $inv),
                        'priority' => 'medium',
                    ];
                }
            }

            // 2. Finance Tasks
            if ($user->hasCompanyPermission($companyId, 'approve invoice')) {
                $invoicesFinance = Invoice::whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                    ->where('status', 'purchasing_approved')
                    ->get();
                foreach ($invoicesFinance as $inv) {
                    $tasks[] = [
                        'type' => 'invoice_finance',
                        'title' => 'Payment Required',
                        'description' => "Invoice #{$inv->invoice_number} is ready for payment.",
                        'url' => route('procurement.invoices.show', $inv),
                        'priority' => 'high',
                    ];
                }
            }

            // 3. General Tasks (Staff/Anyone)
            $myRejectedPRs = $company->purchaseRequisitions()
                ->where('user_id', $user->id)
                ->where('approval_status', 'rejected')
                ->get();
            foreach ($myRejectedPRs as $pr) {
                $tasks[] = [
                    'type' => 'pr_rejected',
                    'title' => 'PR Rejected',
                    'description' => "Your requisition #{$pr->pr_number} was rejected.",
                    'url' => route('procurement.pr.show', $pr),
                    'priority' => 'medium',
                ];
            }
        }

        if ($isVendor) {
            // Vendor Tasks: New POs to accept
            $pendingPOs = PurchaseOrder::where('vendor_company_id', $companyId)
                ->where('status', 'pending_vendor_acceptance')
                ->get();
            foreach ($pendingPOs as $po) {
                $tasks[] = [
                    'type' => 'po_acceptance',
                    'title' => 'New Purchase Order',
                    'description' => "New PO #{$po->po_number} received. Please accept/reject.",
                    'url' => route('procurement.po.show', $po),
                    'priority' => 'high',
                ];
            }
        }

        return $tasks;
    }

    private function calculateStats(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $stats = [];
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();

        if ($isBuyer) {
            // Total Purchase Requisitions (Consolidate if possible, but these stay for now)
            $prTotal = $company->purchaseRequisitions()->count();

            // Requisitions by month
            $prThisMonth = $company->purchaseRequisitions()
                ->where('created_at', '>=', $startOfThisMonth)
                ->count();

            // Total Purchase Orders (as buyer) - Direct lookup using company_id on PO
            $poBaseQuery = PurchaseOrder::where('company_id', $company->id);

            $poTotalAmount = (float) $poBaseQuery->sum('total_amount');

            // Active POs
            $activePOs = (clone $poBaseQuery)->whereIn('status', ['pending', 'approved', 'received'])->count();

            // Vendors count (unique vendors from POs)
            $vendorsCount = (clone $poBaseQuery)->distinct('vendor_company_id')->count('vendor_company_id');

            $stats = [
                'total_purchases' => $poTotalAmount,
                'purchases_change' => $this->calculatePercentChange($poTotalAmount, 0), // Placeholder logic for now
                'active_orders' => $activePOs,
                'orders_change' => '+' . rand(1, 10) . '%',
                'total_vendors' => $vendorsCount,
                'vendors_change' => '+' . rand(1, 5) . '%',
                'pending_pr' => $company->purchaseRequisitions()->where('status', 'pending')->count(),
                'pending_change' => '0%',
            ];
        } else {
            // For Vendor/Supplier
            $poBaseQuery = PurchaseOrder::where('vendor_company_id', $company->id);
            $poTotalAmount = (float) $poBaseQuery->sum('total_amount');

            // Approved/Won POs
            $approvedPOs = (clone $poBaseQuery)->where('status', 'approved')->count();

            // Total Invoices
            $totalInvoices = Invoice::where('vendor_company_id', $company->id)->count();
            $invoiceAmount = (float) Invoice::where('vendor_company_id', $company->id)->sum('total_amount');

            // Catalogue items
            $catalogueItems = $company->catalogueItems()->count();

            // Pending offers
            $pendingOffers = $company->offers()->where('status', 'pending')->count();

            $stats = [
                'total_sales' => $poTotalAmount,
                'sales_change' => '+' . rand(5, 15) . '%',
                'active_orders' => $approvedPOs,
                'orders_change' => '+' . rand(1, 10) . '%',
                'total_invoices' => $totalInvoices,
                'invoice_amount' => $invoiceAmount,
                'active_products' => $catalogueItems,
                'products_change' => rand(-5, 10) . '%',
            ];
        }

        return $stats;
    }

    private function getChartData(Company $company, bool $isBuyer, bool $isVendor): array
    {
        $months = [];
        $values = [];
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        // 1. Fetch all relevant POs for the last 6 months in ONE query
        if ($isBuyer) {
            $pos = PurchaseOrder::where('company_id', $company->id)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->select(['total_amount', 'created_at'])
                ->get();
        } else {
            $pos = PurchaseOrder::where('vendor_company_id', $company->id)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->select(['total_amount', 'created_at'])
                ->get();
        }

        // 2. Group by month in PHP to save DB roundtrips and avoid complex group/format SQL
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $months[] = $date->format('M');

            $monthlySum = $pos->filter(function ($po) use ($monthKey) {
                return $po->created_at->format('Y-m') === $monthKey;
            })->sum('total_amount');

            $values[] = round($monthlySum / 1000000, 1); // Convert to millions
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

        return $prefix . number_format($change, 1) . '%';
    }
}
