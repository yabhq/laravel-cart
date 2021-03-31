<?php

namespace Yab\ShoppingCart;

use App\Logistics\TaxLogistics;
use App\Logistics\CartLogistics;
use App\Logistics\OrderLogistics;
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
use Yab\ShoppingCart\Contracts\PaymentProvider;
use Yab\ShoppingCart\Payments\LocalPaymentProvider;
use Yab\ShoppingCart\Payments\FailedPaymentProvider;
use Yab\ShoppingCart\Payments\StripePaymentProvider;
use Yab\ShoppingCart\Exceptions\PaymentFailedException;
use Yab\ShoppingCart\Exceptions\CheckoutNotFoundException;
use Yab\ShoppingCart\Exceptions\PurchaserInvalidException;
use Yab\ShoppingCart\Exceptions\CheckoutMissingInfoException;
use Yab\ShoppingCart\Exceptions\ItemNotPurchaseableException;
use Yab\ShoppingCart\Exceptions\PaymentProviderInvalidException;

class Checkout
{
    /**
     * The payment provider class to use for charges.
     *
     * @var mixed
     */
    private $paymentProvider;

    /**
     * Create a new checkout instance for a cart.
     *
     * @param \Yab\ShoppingCart\Models\Cart $model
     */
    public function __construct(protected Cart $model)
    {
    }

    /**
     * Find a checkout by an existing ID.
     *
     * @param string $checkoutId
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public static function findById(string $checkoutId) : Checkout
    {
        $checkout = Cart::find($checkoutId);

        if (!$checkout) {
            throw new CheckoutNotFoundException;
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
     * Destroy this checkout instance and soft delete the checkout.
     *
     * @return void
     */
    public function destroy()
    {
        $this->model->delete();

        unset($this->model);
    }

    /**
     * Get the underlying cart model for this checkout instance.
     *
     * @return \Yab\ShoppingCart\Models\Cart
     */
    public function getModel() : Cart
    {
        return $this->model->fresh();
    }

    /**
     * Get the underlying builder instance for the cart.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() : Builder
    {
        return Cart::whereId($this->model->id);
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

        $this->model->purchaser_id = $entity->getIdentifier();
        $this->model->purchaser_type = $entity->getType();

        $this->model->save();
    }

    /**
     * Get the purchaser for the checkout.
     *
     * @return mixed
     */
    public function getPurchaser()
    {
        return $this->model->purchaser;
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

        $item = $this->model->getItem($purchaseable);
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
        $this->model->setCustomField($key, $payload);

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
        if (!$this->model->custom_fields || !isset($this->model->custom_fields[$key])) {
            return null;
        }

        return $this->model->custom_fields[$key];
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
        $this->model->discount_amount = $amount;
        $this->model->save();

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
        return round($this->getModel()->items->sum('price') + $this->getShipping(), 2);
    }

    /**
     * Get the discount amount (dollars) for the checkout.
     *
     * @return float
     */
    public function getDiscount() : float
    {
        return $this->model->discount_amount;
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
     * Set the payment provider for this checkout.
     *
     * @param string $provider
     *
     * @return \Yab\ShoppingCart\Checkout
     */
    public function setPaymentProvider(string $provider) : Checkout
    {
        $supported = self::getSupportedPaymentProviders();
        
        $class = $supported[$provider] ?? $provider;

        if(!class_exists($class) || !(new $class instanceof PaymentProvider)) {
            throw new PaymentProviderInvalidException;
        }

        $this->paymentProvider = new $class;

        return $this;
    }

    /**
     * Get the payment provider for this checkout.
     *
     * @return \Yab\ShoppingCart\Contracts\PaymentProvider
     */
    public function getPaymentProvider() : PaymentProvider
    {
        return $this->paymentProvider;
    }
    
    /**
     * Perform the actual charge for this checkout.
     *
     * @param array $chargeable
     * 
     * @return void
     */
    public function charge(array $chargeable) : void
    {
        $this->abortIfMissingTotals();

        if (is_null($this->paymentProvider)) {
            $this->setPaymentProvider(config('checkout.provider'));
        }

        try {
            $this->paymentProvider->charge($this, $chargeable);
            app(CartLogistics::class)->afterSuccessfulCheckout($this);
        }
        catch(PaymentFailedException $e) {
            app(CartLogistics::class)->afterFailedCheckout($this, $e);
            throw $e;
        }
    }

    /**
     * Convert this checkout to a full fledged order.
     *
     * @return \Yab\ShoppingCart\PurchaseOrder
     */
    public function convertToOrder() : PurchaseOrder
    {
        $order = $this->getModel()->createOrder(
            subtotal: $this->getSubtotal(),
            shipping: $this->getShipping(),
            taxes: $this->getTaxes(),
            discount: $this->getDiscount(),
            total: $this->getTotal()
        );

        $purchaseOrder = PurchaseOrder::findById($order->id);

        app(OrderLogistics::class)->afterOrderPlaced($purchaseOrder);

        return $purchaseOrder;
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
        $this->model->discount_code = $code;
        $this->model->save();

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

    /**
     * Throw an exception if the checkout is missing info needed to
     * calculate totals.
     *
     * @throws \Yab\ShoppingCart\Exceptions\CheckoutMissingInfoException
     *
     * @return void
     */
    private function abortIfMissingTotals()
    {
        if (!$this->hasInfoNeededToCalculateTotal()) {
            throw new CheckoutMissingInfoException;
        }
    }

    /**
     * Return a mapping of the supported payment providers.
     * 
     * @return array
     */
    private static function getSupportedPaymentProviders() : array
    {
        return [
            'local' => LocalPaymentProvider::class,
            'failed' => FailedPaymentProvider::class,
            'stripe' => StripePaymentProvider::class,
        ];
    }
}
