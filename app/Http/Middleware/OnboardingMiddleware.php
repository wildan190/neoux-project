<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Allow access to onboarding routes, logout, and static assets
            if ($request->is('onboarding*') || $request->is('logout') || $request->is('api/*')) {
                return $next($request);
            }

            // If user has no company, redirect to onboarding
            if (!$user->companies()->exists()) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}
