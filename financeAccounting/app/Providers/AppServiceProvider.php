<?php // AppServiceProvider — the primary service provider. Used for binding services into the service container and bootstrapping application-wide settings.
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::addNamespace('account-payable', resource_path('views/AccountPayable'));
    }
}
