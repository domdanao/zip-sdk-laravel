<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class PaymentRequestValidator
{
    /**
     * Validate payment request data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'description' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'metadata' => 'nullable|array',
            'line_items' => 'nullable|array',
            'line_items.*.name' => 'required|string|max:255',
            'line_items.*.amount' => 'required|numeric|min:1',
            'line_items.*.currency' => 'nullable|string|size:3',
            'line_items.*.quantity' => 'nullable|integer|min:1',
            'line_items.*.description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            throw new Exception('Payment request validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
