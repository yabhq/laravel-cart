<?php

namespace Yab\ShoppingCart\Models;

use Yab\Mint\Casts\Money;
use Yab\Mint\Traits\UuidModel;
use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes, UuidModel;

    /**
     * The name of the primary key field.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Whether or not the primary key should be incremented.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The relationships which should be eagerly loaded.
     *
     * @var array
     */
    protected $with = [
        'items',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'subtotal' => Money::class,
        'shipping' => Money::class,
        'taxes' => Money::class,
        'total' => Money::class,
        'discount_amount' => Money::class,
        'custom_fields' => 'array',
    ];

    /**
     * An order may have originated from a cart checkout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart() : BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * The purchaser entity for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchaser() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * A order may have many line items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
