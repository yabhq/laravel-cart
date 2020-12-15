<?php

namespace Yab\ShoppingCart\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

abstract class APIRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'success' => false,
            'message' => 'The provided data failed validation',
            'errors' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);

        throw new ValidationException($validator, $response);
    }
}
