<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local' || env('FORCE_HTTPS', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Verified::class,
            \App\Modules\Auth\Application\Listeners\SendWelcomeEmailListener::class,
        );

        \Illuminate\Support\Facades\View::composer(
            'layouts.partials.sidebar',
            \App\Http\View\Composers\SidebarComposer::class
        );

        // Register Observers
        \App\Modules\Procurement\Domain\Models\DebitNote::observe(\App\Observers\NotificationObserver::class);
        \App\Modules\Procurement\Domain\Models\GoodsReturnRequest::observe(\App\Observers\NotificationObserver::class);
        \App\Modules\Procurement\Domain\Models\ReplacementDelivery::observe(\App\Observers\NotificationObserver::class);
        \App\Modules\Procurement\Domain\Models\Invoice::observe(\App\Observers\NotificationObserver::class);
    }
}



