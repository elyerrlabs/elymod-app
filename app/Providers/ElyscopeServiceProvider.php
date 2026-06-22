<?php

namespace ElymodApp\App\Providers;

use Illuminate\Support\ServiceProvider;

class ElyscopeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        require_once __DIR__ . "/../../vendor-build/autoload.php";
    }
}
