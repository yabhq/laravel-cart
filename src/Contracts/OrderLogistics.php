<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\PurchaseOrder;

interface OrderLogistics
{
    public static function afterOrderPlaced(PurchaseOrder $order) : void;
}
