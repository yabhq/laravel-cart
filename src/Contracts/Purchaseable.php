<?php

namespace Yab\ShoppingCart\Contracts;

interface Purchaseable
{
    public function getIdentifier() : mixed;
    public function getType() : string;
    public function getRetailPrice() : float;
}
