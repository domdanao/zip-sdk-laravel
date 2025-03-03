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
            'mobile_number' => 'nullable|string|min:5|max:25',
            'description' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new Exception('Customer validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
