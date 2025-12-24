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
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share Pending Payouts Count to Admin Layout sidebar
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $view->with('pendingPayoutsCount', \App\Models\PayoutRequest::where('status', 'requested')->count());
        });
    }
}
