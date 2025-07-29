<?php

namespace App\Providers;

use App\Models\Page;
use App\Observers\PageObserver;
use App\View\Composers\UserComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configure authentication redirects for new route names
        // RedirectIfAuthenticated::redirectUsing(fn () => route('dashboard'));

        Paginator::useBootstrapFive();
        View::composer('layouts.main', UserComposer::class);

        // Register model observers
        Page::observe(PageObserver::class);
    }
}
