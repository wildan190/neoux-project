<?php

namespace Modules\Company\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            return redirect()->route('dashboard')
                ->with('error', 'Please select a company first.');
        }

        $company = Company::find($selectedCompanyId);

        if (!$company) {
            session()->forget('selected_company_id');

            return redirect()->route('dashboard')
                ->with('error', 'Selected company not found.');
        }

        // If company is NOT active
        if ($company->status !== 'active') {
            // Allow access to the pending approval page to avoid redirect loop
            if ($request->routeIs('company.pending-approval')) {
                return $next($request);
            }

            return redirect()->route('company.pending-approval');
        }

        // If company IS active but user tries to access pending page
        if ($request->routeIs('company.pending-approval')) {
            return redirect()->route('company.dashboard');
        }

        return $next($request);
    }
}
