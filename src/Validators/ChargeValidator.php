<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class ChargeValidator
{
    /**
     * Validate charge data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:255',
            'customer_id' => 'required|string',
            'source_id' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new Exception('Charge validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
