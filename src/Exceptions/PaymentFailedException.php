<?php

namespace Yab\ShoppingCart\Exceptions;

use Exception;
use Illuminate\Http\Response;

class PaymentFailedException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'message' => 'There was a problem processing the payment',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
