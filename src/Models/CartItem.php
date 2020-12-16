<?php

namespace Yab\ShoppingCart\Models;

use Yab\Mint\Casts\Money;
use App\Logistics\TaxLogistics;
use Yab\ShoppingCart\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
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
        'configuration',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'qty' => 'integer',
        'price' => Money::class,
        'configuration' => 'array',
    ];

    /**
     * A cart item belongs to a cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart() : BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * The actual item (e.g. product) that was purchased.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchaseable() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Increment the quantity for this item.
     *
     * @param integer $qty
     *
     * @return \Yab\ShoppingCart\Models\CartItem
     */
    public function setQty(int $qty) : CartItem
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Calculate the price for this line item based on the quantity.
     *
     * @return \Yab\ShoppingCart\Models\CartItem
     */
    public function calculatePrice() : CartItem
    {
        $this->price = $this->purchaseable->getRetailPrice() * $this->qty;

        return $this;
    }
    
    /**
     * Get the taxes applicable to this cart item.
     *
     * @return float
     */
    public function getTaxes() : float
    {
        $multiplier = app(TaxLogistics::class)->getTaxRate(
            $this->purchaseable,
            $this
        ) * 0.01;

        return $this->price * $multiplier;
    }
}
