<?php

namespace Yab\ShoppingCart\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Http\Controllers\Controller;
use Yab\ShoppingCart\Http\Requests\CheckoutItemCreateRequest;
use Yab\ShoppingCart\Http\Requests\CheckoutItemUpdateRequest;
use Yab\ShoppingCart\Exceptions\PurchaseableNotFoundException;

class CheckoutItemController extends Controller
{
    /**
     * Create a new item in the cart.
     *
     * @param \Yab\ShoppingCart\Http\Requests\CheckoutItemCreateRequest  $request
     * @param string $checkoutId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutItemCreateRequest $request, string $checkoutId)
    {
        $checkout = Checkout::findById($checkoutId);

        $purchaseable = Checkout::getPurchaseable(
            $request->purchaseable_type,
            $request->purchaseable_id,
        );

        throw_if(!$purchaseable, PurchaseableNotFoundException::class);

        return $checkout->addItem(
            purchaseable: $purchaseable,
            qty: $request->qty,
            options: $request->options ?? [],
        );
    }

    /**
     * Update an existing item in the cart.
     *
     * @param \Yab\ShoppingCart\Http\Requests\CheckoutItemUpdateRequest  $request
     * @param string $checkoutId
     * @param int $itemId
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CheckoutItemUpdateRequest $request, string $checkoutId, int $itemId)
    {
        $checkout = Checkout::findById($checkoutId);

        return $checkout->updateItem(
            cartItemId: $itemId,
            qty: $request->qty,
            options: $request->options ?? [],
        );
    }

    /**
     * Remove an existing item from the cart.
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $checkoutId
     * @param int $itemId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, string $checkoutId, int $itemId)
    {
        $checkout = Checkout::findById($checkoutId);

        return $checkout->removeItem($itemId);
    }
}
