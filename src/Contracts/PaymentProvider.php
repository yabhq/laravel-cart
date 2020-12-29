<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Checkout;

interface PaymentProvider
{
    public static function charge(Checkout $checkout, array $chargeable) : void;
}
