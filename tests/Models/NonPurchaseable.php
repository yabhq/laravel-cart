<?php

namespace Yab\ShoppingCart\Tests\Models;

use Yab\Mint\Casts\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonPurchaseable extends Model
{
    use SoftDeletes;

    /**
     * The database table name to use.
     *
     * @var string
     */
    protected $table = 'products';

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
