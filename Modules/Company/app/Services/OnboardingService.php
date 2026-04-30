<?php

namespace Modules\Company\Services;

use Modules\Company\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OnboardingService
{
    /**
     * Validate NPWP against the mock database or live API.
     */
    public function validateNpwp(string $npwp): array
    {
        $cleanNpwp = preg_replace('/[^0-9]/', '', $npwp);

        // Check uniqueness
        if (Company::where('npwp', $cleanNpwp)->exists()) {
            return [
                'success' => false,
                'message' => 'This NPWP is already registered in our system. Please contact support if you believe this is an error.'
            ];
        }

        // Mock validation for local/staging
        if (app()->environment('local', 'staging')) {
            $mockPath = base_path('Modules/Company/resources/data/npwp_mock.json');
            
            if (File::exists($mockPath)) {
                $mockData = json_decode(File::get($mockPath), true);
                
                if (isset($mockData[$cleanNpwp])) {
                    return [
                        'success' => true,
                        'message' => 'NPWP Validated successfully.',
                        'data' => $mockData[$cleanNpwp]
                    ];
                }
            }

            // Fallback for any 15-digit NPWP in local/test
            if (strlen($cleanNpwp) >= 15) {
                return [
                    'success' => true,
                    'message' => 'NPWP Validated (Local Test Mode).',
                    'data' => [
                        'name' => 'TEST COMPANY ' . strtoupper(str_shuffle('huntr')),
                        'address' => 'Sample Address for Testing Purposes',
                        'status' => 'Active',
                        'type' => 'Corporate Entity',
                    ]
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'NPWP not found in our taxation database.'
        ];
    }

    /**
     * Store a new company and attach the user as owner.
     */
    public function createCompany(array $data): Company
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['registered_date'] = now();
        $data['npwp'] = preg_replace('/[^0-9]/', '', $data['npwp']);

        if (isset($data['historical_data'])) {
            $file = $data['historical_data'];
            $fileName = 'onboarding_' . $data['npwp'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('onboarding_imports', $fileName, 'public');
            $data['onboarding_file_path'] = $path;
            unset($data['historical_data']);
        }

        $company = Company::create($data);

        // Dispatch Import Job if file exists
        if ($company->onboarding_file_path) {
            if ($company->category === 'buyer') {
                \Modules\Procurement\Jobs\ProcessPurchaseOrderImport::dispatch(
                    $company->onboarding_file_path,
                    Auth::id(),
                    $company->id,
                    'buyer'
                );
            } else {
                $importJob = \Modules\Catalogue\Models\ImportJob::create([
                    'user_id' => Auth::id(),
                    'company_id' => $company->id,
                    'type' => 'catalogue',
                    'status' => 'pending',
                    'file_name' => basename($company->onboarding_file_path),
                ]);

                \Modules\Catalogue\Jobs\ImportCatalogueItemsJob::dispatch(
                    $company->onboarding_file_path,
                    $company->id,
                    $importJob->id
                );
            }
        }

        // Attach user as owner in members table
        $company->members()->attach(Auth::id(), ['role' => 'owner']);

        // Set sessions
        session(['selected_company_id' => $company->id]);
        session(['procurement_mode' => $company->category === 'vendor' ? 'vendor' : 'buyer']);
        session()->forget('needs_onboarding');

        return $company;
    }
}
