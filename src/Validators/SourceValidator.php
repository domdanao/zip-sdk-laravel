<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class SourceValidator
{
    /**
     * Validate source data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'type' => 'required|string|in:card,gcash,maya',
            'customer_id' => 'required|string',
            'token' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new Exception('Source validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
