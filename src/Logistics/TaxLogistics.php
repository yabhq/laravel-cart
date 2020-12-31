<?php

namespace App\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogistics implements TaxLogisticsInterface
{
    /**
     * Get the taxes given the checkout instance.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     *
     * @return float
     */
    public static function getTaxes(Checkout $checkout) : float
    {
        // Determine the taxes as needed. Possibly helpful methods:

        // $checkout->getSubtotal()
        // $checkout->getDiscount()
        // $checkout->getCustomField('shipping_address')
        // $checkout->getCart()

        return 0;
    }
}
