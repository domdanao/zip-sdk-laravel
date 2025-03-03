<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class SourceData
{
    protected $type;
    protected $customerId;
    protected $token;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->customerId = $data['customer_id'];
        $this->token = $data['token'];
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'customer_id' => $this->customerId,
            'token' => $this->token,
        ];

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
