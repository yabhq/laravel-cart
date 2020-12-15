<?php

namespace Yab\ShoppingCart\Http\Requests;

use Yab\ShoppingCart\Http\Requests\APIRequest;

class CheckoutItemCreateRequest extends APIRequest
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
            'purchaseable_type' => 'required',
            'purchaseable_id' => 'required',
            'qty' => 'required|integer',
        ];
    }
}
