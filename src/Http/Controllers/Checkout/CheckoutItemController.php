<?php

namespace Yab\ShoppingCart\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Http\Controllers\Controller;
use Yab\ShoppingCart\Http\Requests\CheckoutItemCreateRequest;
use Yab\ShoppingCart\Http\Requests\CheckoutItemUpdateRequest;

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

        return $checkout->addItem($purchaseable, $request->qty);
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

        return $checkout->updateItem($itemId, $request->qty);
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
