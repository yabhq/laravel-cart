<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogistics implements TaxLogisticsInterface
{
    /**
     * Get the taxes given the subtotal (including shipping), shipping
     * costs and cart instance.
     *
     * @param float $subtotal
     * @param float $shipping
     * @param \Yab\ShoppingCart\Models\Cart $cart
     *
     * @return float
     */
    public static function getTaxes(float $subtotal, float $shipping, Cart $cart) : float
    {
        return round($subtotal * 0.18, 2);
    }
}
