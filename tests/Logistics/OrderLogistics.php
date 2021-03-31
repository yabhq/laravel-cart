<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\OrderFacade;
use Yab\ShoppingCart\Contracts\OrderLogistics as OrderLogisticsInterface;

class OrderLogistics implements OrderLogisticsInterface
{
    /**
     * Custom logic following an order successfully being placed.
     *
     * @param \Yab\ShoppingCart\OrderFacade
     *
     * @return void
     */
    public static function afterOrderPlaced(OrderFacade $order) : void
    {
    }
}
