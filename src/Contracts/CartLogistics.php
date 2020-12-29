<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Exceptions\PaymentFailedException;

interface CartLogistics
{
    public static function getPurchaseable(string $type, mixed $id) : mixed;
    public static function afterSuccessfulCheckout(Checkout $checkout) : void;
    public static function afterFailedCheckout(Checkout $checkout, PaymentFailedException $e) : void;
}
