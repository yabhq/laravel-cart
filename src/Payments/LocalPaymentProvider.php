<?php

namespace Yab\ShoppingCart\Payments;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\PaymentProvider;

class LocalPaymentProvider implements PaymentProvider
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
        $checkout->getCart()->saveReceipt($checkout, 'transaction_123456');
    }
}
