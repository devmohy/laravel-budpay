<?php

namespace Devmohy\Budpay;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class BudpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('laravel-budpay', function () {
            return new Budpay;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $config = realpath(__DIR__.'/../resources/config/budpay.php');

        $this->publishes([
            $config => config_path('budpay.php')
        ]);
    }
    
    /**
    * Get the services provided by the provider
    * @return array
    */
    public function provides()
    {
        return ['laravel-paystack'];
    }
}
