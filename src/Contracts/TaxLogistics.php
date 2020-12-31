<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Checkout;

interface TaxLogistics
{
    public static function getTaxes(Checkout $checkout) : float;
}
