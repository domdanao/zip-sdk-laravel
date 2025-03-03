<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class ChargeData
{
    protected $amount;
    protected $currency;
    protected $description;
    protected $customerId;
    protected $sourceId;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->amount = $data['amount'];
        $this->currency = $data['currency'];
        $this->description = $data['description'] ?? null;
        $this->customerId = $data['customer_id'];
        $this->sourceId = $data['source_id'];
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'customer_id' => $this->customerId,
            'source_id' => $this->sourceId,
        ];

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
