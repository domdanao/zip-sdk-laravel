<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class CheckoutSessionValidator
{
    /**
     * Validate checkout session data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'currency' => 'nullable|string|size:3',
            'payment_method_types' => 'nullable|array',
            'payment_method_types.*' => 'string|in:card,gcash,maya',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'description' => 'nullable|string|max:255',
            'line_items' => 'required|array|min:1',
            'line_items.*.name' => 'required|string|max:255',
            'line_items.*.amount' => 'required|numeric|min:1',
            'line_items.*.currency' => 'nullable|string|size:3',
            'line_items.*.quantity' => 'nullable|integer|min:1',
            'line_items.*.description' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'customer_email' => 'nullable|email|max:255',
            'billing_address_collection' => 'nullable|array',
            'billing_address_collection.required' => 'nullable|boolean',
            'billing_address_collection.allowed_countries' => 'nullable|array',
            'billing_address_collection.allowed_countries.*' => 'string|size:2',
            'shipping_address_collection' => 'nullable|array',
            'shipping_address_collection.required' => 'nullable|boolean',
            'shipping_address_collection.allowed_countries' => 'nullable|array',
            'shipping_address_collection.allowed_countries.*' => 'string|size:2',
            'locale' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            throw new Exception('Checkout session validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
