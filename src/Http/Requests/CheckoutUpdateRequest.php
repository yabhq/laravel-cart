<?php

namespace Yab\ShoppingCart\Http\Requests;

use Yab\ShoppingCart\Http\Requests\APIRequest;

class CheckoutUpdateRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_info' => 'sometimes|array',
            'shipping_address' => 'sometimes|array',
            'billing_address' => 'sometimes|array',
        ];
    }
}
