<?php

namespace App\Logistics;

use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogistics implements TaxLogisticsInterface
{
    /**
     * Get the tax rate given the subtotal (including shipping), shipping
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
        // You may use $cart->shipping_address here if needed
        
        // Subtotal already includes shipping costs, but shipping cost is included here
        // separately in case it is needed for whatever reason
        return 0;
    }
}
