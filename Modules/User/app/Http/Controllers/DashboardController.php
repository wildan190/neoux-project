<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->allCompanies();

        if ($companies->isEmpty()) {
            return redirect()->route('companies.create');
        }

        // Check if a company is already selected in session
        $selectedCompanyId = session('selected_company_id');

        if ($selectedCompanyId && $companies->contains('id', $selectedCompanyId)) {
            return redirect()->route('company.dashboard');
        }

        // If no company selected, show the selection list
        return view('user::dashboard', compact('companies'));
    }

    public function selectCompany($companyId)
    {
        $company = auth()->user()->allCompanies()->firstWhere('id', $companyId);

        if (! $company) {
            abort(404);
        }

        session(['selected_company_id' => $company->id]);

        // Set active mode based on company category for the session
        $mode = in_array($company->category, ['vendor', 'supplier']) ? 'vendor' : 'buyer';
        session(['procurement_mode' => $mode]);

        return redirect()->route('company.dashboard')
            ->with('success', 'Switched to workspace: '.$company->name . ' (Mode: ' . strtoupper($mode) . ')');
    }
}
