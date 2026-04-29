<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/dashboard/select/*',
            '/logout',
            '/api/midtrans/callback'
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\OnboardingMiddleware::class,
        ]);

        $middleware->alias([
            'admin' => \Modules\Admin\Http\Middleware\AdminMiddleware::class,
            'guest.admin' => \Modules\Admin\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'company.selected' => \Modules\Company\Http\Middleware\EnsureCompanySelected::class,
            'company.active' => \Modules\Company\Http\Middleware\EnsureCompanyIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()
                ->back()
                ->withInput($request->except('password', '_token'))
                ->withErrors(['email' => 'Sesi login telah kadaluarsa karena terlalu lama tidak ada aktivitas. Silakan coba lagi.']);
        });
    })->create();
