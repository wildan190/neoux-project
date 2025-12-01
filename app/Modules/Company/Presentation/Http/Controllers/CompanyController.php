<?php

namespace App\Modules\Company\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Domain\Models\Company;
use App\Modules\Company\Presentation\Http\Requests\StoreCompanyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())->get();
        return view('company.index', compact('companies'));
    }

    public function create()
    {
        return view('company.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['registered_date'] = now();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('company_logos', 'public');
        }

        $company = Company::create($data);

        if ($request->has('locations')) {
            foreach ($request->locations as $location) {
                if (!empty($location)) {
                    $company->locations()->create(['address' => $location]);
                }
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('company_documents', 'public');
                $company->documents()->create([
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->load(['documents', 'locations']);
        return view('company.show', compact('company'));
    }
}
