<?php

namespace Yab\ShoppingCart\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Http\Controllers\Controller;
use Yab\ShoppingCart\Http\Resources\CheckoutResource;

class CheckoutController extends Controller
{
    /**
     * Create a new cart instance.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkout = Checkout::create();

        return new CheckoutResource($checkout);
    }

    /**
     * Fetch the details for a particular checkout ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $checkoutId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $checkoutId)
    {
        $checkout = Checkout::findById($checkoutId);

        return new CheckoutResource($checkout);
    }

    /**
     * Delete the contents for a particular checkout ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $checkoutId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, string $checkoutId)
    {
        $checkout = Checkout::findById($checkoutId);

        $checkout->destroy();

        return response()->make('', Response::HTTP_NO_CONTENT);
    }
}
