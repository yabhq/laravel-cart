<?php

namespace Yab\ShoppingCart;

use Illuminate\Support\ServiceProvider;
use Yab\ShoppingCart\Http\Resources\CheckoutResource;

class ShoppingCartServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        CheckoutResource::withoutWrapping();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'migrations');
            $this->publishes([
                __DIR__.'/../routes' => base_path('routes'),
            ], 'routes');
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('checkout.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/Logistics' => app_path('Logistics'),
            ], 'logistics');
            $this->publishes([
                __DIR__.'/Http/Controllers/Published' => app_path('Http/Controllers/Checkout'),
            ], 'controllers');
        }
    }
}
