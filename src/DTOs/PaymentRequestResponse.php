<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class PaymentRequestResponse
{
    public $id;
    public $object;
    public $amount;
    public $currency;
    public $description;
    public $customerEmail;
    public $status;
    public $createdAt;
    public $expiresAt;
    public $canceledAt;
    public $successUrl;
    public $cancelUrl;
    public $paymentUrl;
    public $metadata;
    public $lineItems;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->object = $data['object'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->customerEmail = $data['customer_email'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->expiresAt = $data['expires_at'] ?? null;
        $this->canceledAt = $data['canceled_at'] ?? null;
        $this->successUrl = $data['success_url'] ?? null;
        $this->cancelUrl = $data['cancel_url'] ?? null;
        $this->paymentUrl = $data['payment_url'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
        $this->lineItems = $data['line_items'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'customer_email' => $this->customerEmail,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'expires_at' => $this->expiresAt,
            'canceled_at' => $this->canceledAt,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'payment_url' => $this->paymentUrl,
            'metadata' => $this->metadata,
            'line_items' => $this->lineItems,
        ];
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
}
