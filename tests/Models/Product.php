<?php

namespace Yab\ShoppingCart\Tests\Models;

use Yab\Mint\Casts\Money;
use Illuminate\Database\Eloquent\Model;
use Yab\ShoppingCart\Traits\Purchaseable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yab\ShoppingCart\Contracts\Purchaseable as PurchaseableInterface;

class Product extends Model implements PurchaseableInterface
{
    use Purchaseable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => Money::class,
    ];
}
