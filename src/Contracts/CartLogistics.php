<?php

namespace Yab\ShoppingCart\Contracts;

interface CartLogistics
{
    public static function getTaxRate(mixed $purchaseable, array $address) : int;
    public static function getPurchaseable(string $type, mixed $id) : mixed;
}
