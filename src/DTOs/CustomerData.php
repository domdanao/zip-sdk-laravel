<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class CustomerData
{
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->phone = $data['phone'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
        ];

        if ($this->phone) {
            $data['phone'] = $this->phone;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
