<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies()->get();

        return view('dashboard', compact('companies'));
    }

    public function selectCompany($companyId)
    {
        $company = auth()->user()->companies()->findOrFail($companyId);

        session(['selected_company_id' => $company->id]);

        return redirect()->route('company.dashboard');
    }
}
