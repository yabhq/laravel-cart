<?php

namespace Yab\ShoppingCart;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Logistics\TaxLogistics;
use App\Logistics\CartLogistics;
use Yab\ShoppingCart\Models\Cart;
use App\Logistics\DiscountLogistics;
use App\Logistics\ShippingLogistics;
use Yab\ShoppingCart\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Yab\ShoppingCart\Contracts\Purchaser;
use Yab\ShoppingCart\Events\CartItemAdded;
use Yab\ShoppingCart\Contracts\Purchaseable;
use Yab\ShoppingCart\Events\CartItemDeleted;
use Yab\ShoppingCart\Events\CartItemUpdated;
use Yab\ShoppingCart\Exceptions\CheckoutNotFoundException;
use Yab\ShoppingCart\Exceptions\PurchaserInvalidException;
use Yab\ShoppingCart\Exceptions\ItemNotPurchaseableException;

class Checkout
{
    /**
     * Create a new checkout instance for a cart.
     *
     * @param \Yab\ShoppingCart\Models\Cart
     */
    public function __construct(protected Cart $cart)
    {
    }

    /**
     * Find a checkout by an existing ID.
     *
     * @param string $checkoutId
     * @param bool $withTrashed
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public static function findById(string $checkoutId, bool $withTrashed = false) : Checkout
    {
        $checkout = $withTrashed ? Cart::withTrashed()->find($checkoutId) : Cart::find($checkoutId);

        if (! $checkout) {
            throw new CheckoutNotFoundException();
        }

        return new Checkout($checkout);
    }

    /**
     * Create a fresh new checkout with a new ID.
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public static function create() : Checkout
    {
        return new Checkout(Cart::create());
    }

    /**
     * Get the UUID for this checkout.
     *
     * @return string
     */
    public function id() : string
    {
        return $this->getCart()->id;
    }

    /**
     * Destroy this checkout instance and soft delete the checkout.
     *
     * @return void
     */
    public function destroy()
    {
        $this->cart->delete();

        unset($this->cart);
    }

    /**
     * Get the underlying cart model for this checkout instance.
     *
     * @return \Yab\ShoppingCart\Models\Cart
     */
    public function getCart() : Cart
    {
        return $this->cart->fresh();
    }

    /**
     * Get the underlying builder instance for the cart.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getCartBuilder() : Builder
    {
        return Cart::whereId($this->cart->id);
    }

    /**
     * Get the purchaseable entity given the purchaseable entity type and ID.
     *
     * @param string $type
     * @param mixed $id
     *
     * @return mixed
     */
    public static function getPurchaseable(string $type, mixed $id) : mixed
    {
        return app(CartLogistics::class)->getPurchaseable($type, $id);
    }

    /**
     * Set the purchaser for the checkout.
     *
     * @param mixed $entity
     *
     * @return void
     */
    public function setPurchaser(mixed $entity)
    {
        $this->abortIfNotPurchaser($entity);

        $this->cart->purchaser_id = $entity->getIdentifier();
        $this->cart->purchaser_type = $entity->getType();

        $this->cart->save();
    }

    /**
     * Get the purchaser for the checkout.
     *
     * @return mixed
     */
    public function getPurchaser()
    {
        return $this->cart->purchaser;
    }

    /**
     * Add an item to the cart.
     *
     * @param mixed $purchaseable
     * @param int $qty
     * @param float $price - optional
     * @param array $options - optional
     *
     * @return \Yab\ShoppingCart\Models\CartItem
     */
    public function addItem(mixed $purchaseable, int $qty, ?float $price = null, ?array $options = []) : CartItem
    {
        $this->abortIfNotPurchaseable($purchaseable);
        
        app(CartLogistics::class)->beforeCartItemAdded($this, $purchaseable, $qty);

        $item = $this->cart->getItem($purchaseable);
        $item->setQty($qty)->setOptions($options)->calculatePrice($price)->save();
        
        event(new CartItemAdded($item));

        return $item;
    }

    /**
     * Update an existing item in the cart.
     *
     * @param int $cartItemId
     * @param int $qty
     * @param float $price - optional
     * @param array $options - optional
     *
     * @return \Yab\ShoppingCart\Models\CartItem
     */
    public function updateItem(int $cartItemId, int $qty, ?float $price = null, ?array $options = []) : CartItem
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->setQty($qty)->setOptions($options)->calculatePrice($price)->save();
        
        event(new CartItemUpdated($item));

        return $item;
    }

    /**
     * Remove an existing item from the cart.
     *
     * @param int $cartItemId
     *
     * @return \Yab\ShoppingCart\Models\CartItem
     */
    public function removeItem(int $cartItemId) : CartItem
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->delete();
        
        event(new CartItemDeleted($item));

        return $item;
    }

    /**
     * Set a custom field value for this cart.
     *
     * @param string $key
     * @param mixed $payload
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public function setCustomField(string $key, mixed $payload) : Checkout
    {
        $this->cart->setCustomField($key, $payload);

        return $this;
    }

    /**
     * Get the custom field value for the specified key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustomField(string $key) : mixed
    {
        if (!$this->cart->custom_fields) {
            return null;
        }

        if (Str::contains($key, '.')) {
            $flattened = Arr::dot($this->cart->custom_fields);
            return isset($flattened[$key]) ? $flattened[$key] : null;
        }

        return isset($this->cart->custom_fields[$key]) ? $this->cart->custom_fields[$key] : null;
    }

    /**
     * Apply a discount code to this checkout.
     *
     * @param string $code
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public function applyDiscountCode(string $code) : Checkout
    {
        $amount = app(DiscountLogistics::class)->getDiscountFromCode($this, $code);

        if ($amount == 0) {
            return $this;
        }

        $this->setDiscountCode($code);
        $this->setDiscountAmount($amount);

        return $this;
    }

    /**
     * Manually set the discount amount for the checkout (e.g. without
     * applying a specific code).
     *
     * @param float $amount
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public function setDiscountAmount(float $amount) : Checkout
    {
        $this->cart->discount_amount = $amount;
        $this->cart->save();

        return $this;
    }

    /**
     * Whether or not this checkout has the info needed to calculate the total.
     *
     * @return bool
     */
    public function hasInfoNeededToCalculateTotal() : bool
    {
        return app(CartLogistics::class)->hasInfoNeededToCalculateTotal($this);
    }

    /**
     * Get the shipping cost for the checkout.
     *
     * @return float
     */
    public function getShipping() : float
    {
        return round(app(ShippingLogistics::class)->getShippingCost($this), 2);
    }

    /**
     * Get the subtotal for the checkout.
     *
     * @return float
     */
    public function getSubtotal() : float
    {
        return round($this->getCart()->items->sum('price') + $this->getShipping(), 2);
    }

    /**
     * Get the discount amount (dollars) for the checkout.
     *
     * @return float
     */
    public function getDiscount() : float
    {
        return floatval($this->cart->discount_amount);
    }

    /**
     * Get the taxes for the checkout.
     *
     * @return float
     */
    public function getTaxes() : float
    {
        return round(app(TaxLogistics::class)->getTaxes($this), 2);
    }

    /**
     * Get the total for the checkout.
     *
     * @return float
     */
    public function getTotal() : float
    {
        return round($this->getSubtotal() - $this->getDiscount() + $this->getTaxes(), 2);
    }

    /**
     * Manually tag this checkout with a discount code.
     *
     * @param string $code
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    private function setDiscountCode(string $code) : Checkout
    {
        $this->cart->discount_code = $code;
        $this->cart->save();

        return $this;
    }

    /**
     * Throw an exception if the payload does not implement the purchaseable
     * interface.
     *
     * @param mixed $purchaseable
     *
     * @throws \Yab\ShoppingCart\Exceptions\ItemNotPurchaseableException
     *
     * @return void
     */
    private function abortIfNotPurchaseable(mixed $purchaseable)
    {
        if (!($purchaseable instanceof Purchaseable)) {
            throw new ItemNotPurchaseableException;
        }
    }

    /**
     * Throw an exception if the payload does not implement the purchaser
     * interface.
     *
     * @param mixed $purchaser
     *
     * @throws \Yab\ShoppingCart\Exceptions\PurchaserInvalidException
     *
     * @return void
     */
    private function abortIfNotPurchaser(mixed $purchaser)
    {
        if (!($purchaser instanceof Purchaser)) {
            throw new PurchaserInvalidException;
        }
    }
}
