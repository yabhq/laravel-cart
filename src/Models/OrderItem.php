<?php

namespace Yab\ShoppingCart\Models;

use Yab\Mint\Casts\Money;
use Yab\ShoppingCart\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchaseable_id',
        'purchaseable_type',
        'qty',
        'custom_fields',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'qty' => 'integer',
        'unit_price' => Money::class,
        'price' => Money::class,
        'custom_fields' => 'array',
    ];

    /**
     * A order item belongs to a order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The actual item (e.g. product) that was ordered.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchaseable() : MorphTo
    {
        return $this->morphTo();
    }
}
