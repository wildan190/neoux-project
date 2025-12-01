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
        $company->load(['user', 'documents', 'locations']);

        return view('admin.companies.show', compact('company'));
    }

    public function approve(Company $company)
    {
        $company->update(['status' => 'active']);

        return back()->with('success', 'Company has been approved successfully.');
    }

    public function decline(Company $company)
    {
        $company->update(['status' => 'declined']);

        return back()->with('success', 'Company has been declined.');
    }
}
