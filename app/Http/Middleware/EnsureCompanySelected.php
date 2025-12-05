<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has selected a company
        if (!session('selected_company_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'Please select a company first to access this feature.');
        }

        return $next($request);
    }
}
