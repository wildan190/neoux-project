<?php

namespace App\Modules\Admin\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Domain\Models\Company;
use Illuminate\Http\Request;

class CompanyReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $companies = Company::with(['user', 'locations'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.companies.index', compact('companies', 'status'));
    }

    public function show(Company $company)
    {
        $company->load(['user', 'documents', 'locations', 'approvedBy', 'declinedBy', 'activities.admin']);

        // History Stats
        $stats = [
            'offers_submitted' => $company->offers()->count(),
            'offers_won' => $company->offers()->where('status', 'accepted')->count(),
            'total_requests' => $company->purchaseRequisitions()->count(),
            'active_requests' => $company->purchaseRequisitions()->whereIn('status', ['pending', 'open'])->count(),
        ];

        return view('admin.companies.show', compact('company', 'stats'));
    }

    public function approve(Company $company)
    {
        $company->update([
            'status' => 'active',
            'approved_by' => auth('admin')->id(),
            'approved_at' => now(),
        ]);

        // Log activity
        \App\Modules\Admin\Domain\Models\CompanyActivity::create([
            'company_id' => $company->id,
            'admin_id' => auth('admin')->id(),
            'action' => 'approved',
            'description' => 'Company application approved by ' . auth('admin')->user()->name,
        ]);

        return back()->with('success', 'Company has been approved successfully.');
    }

    public function decline(Company $company)
    {
        $company->update([
            'status' => 'declined',
            'declined_by' => auth('admin')->id(),
            'declined_at' => now(),
        ]);

        // Log activity
        \App\Modules\Admin\Domain\Models\CompanyActivity::create([
            'company_id' => $company->id,
            'admin_id' => auth('admin')->id(),
            'action' => 'declined',
            'description' => 'Company application declined by ' . auth('admin')->user()->name,
        ]);

        return back()->with('success', 'Company has been declined.');
    }
}
