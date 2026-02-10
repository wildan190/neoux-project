<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Company\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        if (! $selectedCompanyId) {
            return redirect()->back()->with('error', 'Please select a company first.');
        }

        $warehouses = Warehouse::where('company_id', $selectedCompanyId)->get();

        return view('procurement.warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        return view('procurement.warehouse.create');
    }

    public function store(Request $request)
    {
        $selectedCompanyId = session('selected_company_id');
        if (! $selectedCompanyId) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,NULL,id,company_id,'.$selectedCompanyId,
            'address' => 'nullable|string',
        ]);

        Warehouse::create([
            'id' => Str::uuid(),
            'company_id' => $selectedCompanyId,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
        ]);

        return redirect()->route('procurement.warehouse.index')->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($warehouse->company_id != $selectedCompanyId) {
            abort(403);
        }

        return view('procurement.warehouse.edit', compact('warehouse'));
    }

    public function show(Warehouse $warehouse)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($warehouse->company_id != $selectedCompanyId) {
            abort(403);
        }

        $warehouse->load(['stocks.catalogueItem']);

        return view('procurement.warehouse.show', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($warehouse->company_id != $selectedCompanyId) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,'.$warehouse->id.',id,company_id,'.$selectedCompanyId,
            'address' => 'nullable|string',
        ]);

        $warehouse->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
        ]);

        return redirect()->route('procurement.warehouse.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($warehouse->company_id != $selectedCompanyId) {
            abort(403);
        }

        $warehouse->delete();

        return back()->with('success', 'Warehouse deleted successfully.');
    }
}
