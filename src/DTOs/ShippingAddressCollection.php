<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class ShippingAddressCollection
{
    protected $required;
    protected $allowedCountries;

    public function __construct(array $data)
    {
        $this->required = $data['required'] ?? true;
        $this->allowedCountries = $data['allowed_countries'] ?? ['PH'];
    }

    public function toArray(): array
    {
        return [
            'required' => $this->required,
            'allowed_countries' => $this->allowedCountries,
        ];
    }
}
