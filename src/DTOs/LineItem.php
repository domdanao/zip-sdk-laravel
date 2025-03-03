<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class LineItem
{
    protected $name;
    protected $amount;
    protected $currency;
    protected $quantity;
    protected $description;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->amount = $data['amount'];
        $this->currency = $data['currency'] ?? config('zip-sdk.defaults.currency');
        $this->quantity = $data['quantity'] ?? 1;
        $this->description = $data['description'] ?? null;
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
        ];

        if ($this->description) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
