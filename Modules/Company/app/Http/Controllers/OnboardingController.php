<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Company\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index()
    {
        // If user already has a company, redirect to dashboard
        if (Auth::user()->companies()->exists()) {
            return redirect()->route('dashboard');
        }

        return view('company::onboarding.index');
    }

    public function validateNpwp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npwp' => 'required|string|min:15|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format NPWP tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $npwp = preg_replace('/[^0-9]/', '', $request->npwp);

        // Check if NPWP already exists in database
        $exists = Company::where('npwp', $npwp)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This NPWP is already registered in our system. Please contact support if you believe this is an error.'
            ], 422);
        }

        // Mock validation for local/staging
        if (app()->environment('local', 'staging')) {
            // Simulated NPWP Database for Indonesia
            $mockData = [
                '013456789012000' => [
                    'name' => 'PT GLOBAL TEKNOLOGI INDONESIA',
                    'address' => 'Jl. Sudirman No. 45, South Jakarta',
                    'status' => 'Active',
                    'type' => 'Corporate Entity',
                ],
                '024567890123000' => [
                    'name' => 'CV MAJU JAYA BERSAMA',
                    'address' => 'Jababeka Industrial Area Block C-12, Bekasi',
                    'status' => 'Active',
                    'type' => 'Corporate Entity',
                ],
                '098765432109000' => [
                    'name' => 'WILDAN BELFIORE PERSONAL',
                    'address' => 'Komp. Melati No. 7, Bandung',
                    'status' => 'Active',
                    'type' => 'Individual',
                ],
            ];

            if (isset($mockData[$npwp])) {
                return response()->json([
                    'success' => true,
                    'message' => 'NPWP Validated successfully.',
                    'data' => $mockData[$npwp]
                ]);
            }

            // If not in mock, return a generic success for any 15-digit NPWP to facilitate testing
            if (strlen($npwp) >= 15) {
                 return response()->json([
                    'success' => true,
                    'message' => 'NPWP Validated (Local Test Mode).',
                    'data' => [
                        'name' => 'TEST COMPANY ' . Str::upper(Str::random(5)),
                        'address' => 'Sample Address for Testing Purposes',
                        'status' => 'Active',
                        'type' => 'Corporate Entity',
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'NPWP not found in our taxation database.'
        ], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:buyer,vendor,supplier',
            'business_category' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'npwp' => 'required|string|unique:companies,npwp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['registered_date'] = now();

        $company = Company::create($data);

        // Attach user as owner in members table
        $company->members()->attach(Auth::id(), ['role' => 'owner']);

        // Set as selected company in session
        session(['selected_company_id' => $company->id]);
        session(['procurement_mode' => $company->category === 'vendor' ? 'vendor' : 'buyer']);

        return redirect()->route('dashboard')->with('success', 'Congratulations! Your company has been registered and is now under verification.');
    }
}
