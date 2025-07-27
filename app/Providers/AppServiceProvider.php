<?php

namespace App\Providers;

// use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        // Configure authentication redirects for new route names
        // RedirectIfAuthenticated::redirectUsing(fn () => route('dashboard'));
    }
}
