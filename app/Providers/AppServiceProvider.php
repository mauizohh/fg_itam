<?php

namespace App\Providers;

use App\Auth\AdmidioRoleResolver;
use App\Auth\AdmidioUserProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdmidioRoleResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::defaultView('vendor.pagination.tailwind');

        Auth::provider('admidio', function ($app, array $config): AdmidioUserProvider {
            return new AdmidioUserProvider($app->make(AdmidioRoleResolver::class));
        });
    }
}
