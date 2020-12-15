<?php

namespace Yab\ShoppingCart\Tests;

use Illuminate\Support\ServiceProvider;

class ShoppingCartTestProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/checkout.php');
    }
}
