<?php

namespace Yab\ShoppingCart\Payments;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\PaymentProvider;
use Yab\ShoppingCart\Exceptions\PaymentFailedException;

class FailedPaymentProvider implements PaymentProvider
{
    /**
     * Perform a charge based on the checkout total.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     * @param array $chargeable
     *
     * @return void
     */
    public static function charge(Checkout $checkout, array $chargeable) : void
    {
        throw new PaymentFailedException;
    }
}
