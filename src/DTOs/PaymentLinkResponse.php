<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class PaymentLinkResponse
{
    public $id;
    public $object;
    public $name;
    public $description;
    public $amount;
    public $currency;
    public $active;
    public $url;
    public $successUrl;
    public $cancelUrl;
    public $createdAt;
    public $expiresAt;
    public $metadata;
    public $lineItems;
    public $customerEmail;
    public $customerDetails;
    public $shippingDetails;
    public $billingDetails;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->object = $data['object'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->active = $data['active'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->successUrl = $data['success_url'] ?? null;
        $this->cancelUrl = $data['cancel_url'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->expiresAt = $data['expires_at'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
        $this->lineItems = $data['line_items'] ?? [];
        $this->customerEmail = $data['customer_email'] ?? null;
        $this->customerDetails = $data['customer_details'] ?? null;
        $this->shippingDetails = $data['shipping_details'] ?? null;
        $this->billingDetails = $data['billing_details'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'active' => $this->active,
            'url' => $this->url,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'created_at' => $this->createdAt,
            'expires_at' => $this->expiresAt,
            'metadata' => $this->metadata,
            'line_items' => $this->lineItems,
            'customer_email' => $this->customerEmail,
            'customer_details' => $this->customerDetails,
            'shipping_details' => $this->shippingDetails,
            'billing_details' => $this->billingDetails,
        ];
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }

        $expiresAt = new \DateTime($this->expiresAt);
        $now = new \DateTime();

        return $expiresAt < $now;
    }
}
