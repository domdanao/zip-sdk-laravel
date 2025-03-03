<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class CustomerValidator
{
    /**
     * Validate customer data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new Exception('Customer validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
