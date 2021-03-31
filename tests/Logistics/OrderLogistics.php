<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\PurchaseOrder;
use Yab\ShoppingCart\Contracts\OrderLogistics as OrderLogisticsInterface;

class OrderLogistics implements OrderLogisticsInterface
{
    /**
     * Custom logic following an order successfully being placed.
     *
     * @param \Yab\ShoppingCart\PurchaseOrder
     *
     * @return void
     */
    public static function afterOrderPlaced(PurchaseOrder $order) : void
    {
    }
}
