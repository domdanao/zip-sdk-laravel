<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class CustomerData
{
    protected $email;
    protected $mobileNumber;
    protected $description;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->mobileNumber = $data['mobile_number'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'email' => $this->email,
        ];

        if ($this->mobileNumber) {
            $data['mobile_number'] = $this->mobileNumber;
        }

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
