<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class PaymentRequestData
{
    protected $amount;
    protected $currency;
    protected $description;
    protected $customerEmail;
    protected $successUrl;
    protected $cancelUrl;
    protected $metadata;
    protected $lineItems;
    protected $expiresAt;

    public function __construct(array $data)
    {
        $this->amount = $data['amount'];
        $this->currency = $data['currency'] ?? 'PHP';
        $this->description = $data['description'];
        $this->customerEmail = $data['customer_email'];
        $this->successUrl = $data['success_url'];
        $this->cancelUrl = $data['cancel_url'];
        $this->metadata = $data['metadata'] ?? [];
        $this->lineItems = $data['line_items'] ?? [];
        $this->expiresAt = $data['expires_at'] ?? null;
    }

    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'customer_email' => $this->customerEmail,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
        ];

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        if (!empty($this->lineItems)) {
            $data['line_items'] = $this->lineItems;
        }

        if ($this->expiresAt) {
            $data['expires_at'] = $this->expiresAt;
        }

        return $data;
    }
}
