<?php

namespace Yab\ShoppingCart\Exceptions;

use Exception;
use Illuminate\Http\Response;

class CheckoutNotFoundException extends Exception
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
            'message' => 'The specified checkout does not exist',
        ], Response::HTTP_NOT_FOUND);
    }
}
