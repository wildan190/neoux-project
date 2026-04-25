<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('procurement::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('procurement::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('procurement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('procurement::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function guide()
    {
        return view('procurement::guide');
    }

    /**
     * Switch between Buyer and Vendor modes.
     */
    public function switchMode(Request $request)
    {
        $mode = $request->input('mode');
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            return back()->with('error', 'No company selected.');
        }

        $company = \Modules\Company\Models\Company::find($selectedCompanyId);

        if (!in_array($mode, ['buyer', 'vendor'])) {
            return back()->with('error', 'Invalid mode.');
        }

        // Approval Check
        $authorizedModes = $company->authorized_modes ?? [];
        if (!in_array($mode, $authorizedModes)) {
            return back()->with('error', 'Anda belum memiliki akses ke mode ' . strtoupper($mode) . '. Silahkan hubungi Admin Internal untuk aktivasi mode ini.');
        }

        session(['procurement_mode' => $mode]);

        // Redirect based on the selected mode
        if ($mode === 'vendor') {
            return redirect()->route('procurement.pr.public-feed')->with('success', 'Switched to Selling mode.');
        }

        return redirect()->route('procurement.pr.index')->with('success', 'Switched to Buying mode.');
    }
}
