<?php

namespace Domdanao\ZipSdkLaravel;

use Illuminate\Support\ServiceProvider;
use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\Services\ZipCheckoutService\ZipCheckoutService;

class ZipSdkServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/zip-sdk.php', 'zip-sdk'
        );

        // Register ZipService
        $this->app->singleton('zip-service', function ($app) {
            return new ZipService();
        });

        // Register ZipCheckoutService
        $this->app->singleton('zip-checkout-service', function ($app) {
            return new ZipCheckoutService(
                $app->make('zip-service')
            );
        });
    }

    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/zip-sdk.php' => config_path('zip-sdk.php'),
        ], 'zip-sdk-config');
    }
}
