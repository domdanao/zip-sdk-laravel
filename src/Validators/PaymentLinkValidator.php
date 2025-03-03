<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class PaymentLinkValidator
{
    /**
     * Validate payment link data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'metadata' => 'nullable|array',
            'line_items' => 'nullable|array',
            'line_items.*.name' => 'required|string|max:255',
            'line_items.*.amount' => 'required|numeric|min:1',
            'line_items.*.currency' => 'nullable|string|size:3',
            'line_items.*.quantity' => 'nullable|integer|min:1',
            'line_items.*.description' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
            'customer_email' => 'nullable|email|max:255',
            'customer_details' => 'nullable|array',
            'customer_details.name' => 'nullable|string|max:255',
            'customer_details.phone' => 'nullable|string|max:20',
            'shipping_details' => 'nullable|array',
            'shipping_details.address.line1' => 'nullable|string|max:255',
            'shipping_details.address.line2' => 'nullable|string|max:255',
            'shipping_details.address.city' => 'nullable|string|max:255',
            'shipping_details.address.state' => 'nullable|string|max:255',
            'shipping_details.address.postal_code' => 'nullable|string|max:20',
            'shipping_details.address.country' => 'nullable|string|size:2',
            'billing_details' => 'nullable|array',
            'billing_details.address.line1' => 'nullable|string|max:255',
            'billing_details.address.line2' => 'nullable|string|max:255',
            'billing_details.address.city' => 'nullable|string|max:255',
            'billing_details.address.state' => 'nullable|string|max:255',
            'billing_details.address.postal_code' => 'nullable|string|max:20',
            'billing_details.address.country' => 'nullable|string|size:2',
        ]);

        if ($validator->fails()) {
            throw new Exception('Payment link validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }

    /**
     * Validate payment link update data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validateUpdate(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
            'metadata' => 'nullable|array',
            'line_items' => 'nullable|array',
            'line_items.*.name' => 'required|string|max:255',
            'line_items.*.amount' => 'required|numeric|min:1',
            'line_items.*.currency' => 'nullable|string|size:3',
            'line_items.*.quantity' => 'nullable|integer|min:1',
            'line_items.*.description' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
            'customer_email' => 'nullable|email|max:255',
            'customer_details' => 'nullable|array',
            'customer_details.name' => 'nullable|string|max:255',
            'customer_details.phone' => 'nullable|string|max:20',
            'shipping_details' => 'nullable|array',
            'shipping_details.address.line1' => 'nullable|string|max:255',
            'shipping_details.address.line2' => 'nullable|string|max:255',
            'shipping_details.address.city' => 'nullable|string|max:255',
            'shipping_details.address.state' => 'nullable|string|max:255',
            'shipping_details.address.postal_code' => 'nullable|string|max:20',
            'shipping_details.address.country' => 'nullable|string|size:2',
            'billing_details' => 'nullable|array',
            'billing_details.address.line1' => 'nullable|string|max:255',
            'billing_details.address.line2' => 'nullable|string|max:255',
            'billing_details.address.city' => 'nullable|string|max:255',
            'billing_details.address.state' => 'nullable|string|max:255',
            'billing_details.address.postal_code' => 'nullable|string|max:20',
            'billing_details.address.country' => 'nullable|string|size:2',
        ]);

        if ($validator->fails()) {
            throw new Exception('Payment link update validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
