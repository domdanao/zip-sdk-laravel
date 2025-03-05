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
            'amount' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'source' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:1|max:255',
            'statement_descriptor' => 'required|string|min:3|max:100',
            'capture' => 'nullable|boolean',
            'cvc' => 'nullable|string|min:3|max:4',
            'require_auth' => 'nullable|boolean',
            'customer_id' => 'nullable|string',
            'redirect_url' => 'nullable|url',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new Exception('Charge validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
