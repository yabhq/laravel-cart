<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Order;

interface OrderLogistics
{
    public static function afterOrderPlaced(Order $order) : void;
}
