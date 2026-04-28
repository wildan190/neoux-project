<?php

namespace Modules\Company\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Company\app\Http\Requests\ValidateNpwpRequest;
use Modules\Company\app\Http\Requests\StoreOnboardingRequest;
use Modules\Company\app\Services\OnboardingService;

class OnboardingController extends Controller
{
    protected $onboardingService;

    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Show the onboarding stepper page.
     */
    public function index()
    {
        // If user already has a company, redirect to dashboard
        if (Auth::user()->companies()->exists()) {
            return redirect()->route('dashboard');
        }

        return view('company::onboarding.index');
    }

    /**
     * Validate NPWP via AJAX.
     */
    public function validateNpwp(ValidateNpwpRequest $request)
    {
        $result = $this->onboardingService->validateNpwp($request->npwp);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Store the onboarding data and create the company.
     */
    public function store(StoreOnboardingRequest $request)
    {
        try {
            $company = $this->onboardingService->createCompany($request->validated());

            return redirect()->route('dashboard')->with('success', 'Congratulations! Your company has been registered and is now under verification.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create company: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
