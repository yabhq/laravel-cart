<?php

namespace Yab\ShoppingCart\Contracts;

interface Purchaser
{
    public function getIdentifier() : mixed;
    public function getType() : string;
}
