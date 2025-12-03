<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->companies()->get();

        if ($companies->isEmpty()) {
            return redirect()->route('companies.create');
        }

        // Check if a company is already selected in session
        $selectedCompanyId = session('selected_company_id');

        if ($selectedCompanyId && $companies->contains('id', $selectedCompanyId)) {
            return redirect()->route('company.dashboard');
        }

        // If no company selected, show the selection list
        return view('dashboard', compact('companies'));
    }

    public function selectCompany($companyId)
    {
        $company = auth()->user()->companies()->findOrFail($companyId);

        session(['selected_company_id' => $company->id]);

        return redirect()->route('company.dashboard')
            ->with('success', 'Switched to workspace: ' . $company->name);
    }
}
