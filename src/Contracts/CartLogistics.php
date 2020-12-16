<?php

namespace Yab\ShoppingCart\Contracts;

interface CartLogistics
{
    public static function getPurchaseable(string $type, mixed $id) : mixed;
}
