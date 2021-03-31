<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Order;
use Yab\ShoppingCart\Contracts\OrderLogistics as OrderLogisticsInterface;

class OrderLogistics implements OrderLogisticsInterface
{
    /**
     * Custom logic following an order successfully being placed.
     *
     * @param \Yab\ShoppingCart\Order
     *
     * @return void
     */
    public static function afterOrderPlaced(Order $order) : void
    {
    }
}
