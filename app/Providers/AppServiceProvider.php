<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
        Paginator::defaultView('vendor.pagination.gik-pagination');
        Paginator::defaultSimpleView('vendor.pagination.gik-pagination');

        // Force HTTPS untuk semua URL yang di-generate Laravel
        // Penting untuk LiteSpeed yang kadang di belakang proxy
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Register helper functions
        if (!function_exists('getPageTitle')) {
            require_once __DIR__ . '/../Helpers/AppHelper.php';
        }
    }
}

if (!function_exists('getPageTitle')) {
    function getPageTitle($page = '') {
        return \App\Helpers\AppHelper::getPageTitle($page);
    }
}
