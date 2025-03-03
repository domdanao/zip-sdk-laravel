<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class PaymentLinkData
{
    protected $name;
    protected $description;
    protected $amount;
    protected $currency;
    protected $successUrl;
    protected $cancelUrl;
    protected $metadata;
    protected $lineItems;
    protected $active;
    protected $expiresAt;
    protected $customerEmail;
    protected $customerDetails;
    protected $shippingDetails;
    protected $billingDetails;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->currency = $data['currency'] ?? 'PHP';
        $this->successUrl = $data['success_url'] ?? null;
        $this->cancelUrl = $data['cancel_url'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
        $this->lineItems = $data['line_items'] ?? [];
        $this->active = $data['active'] ?? true;
        $this->expiresAt = $data['expires_at'] ?? null;
        $this->customerEmail = $data['customer_email'] ?? null;
        $this->customerDetails = $data['customer_details'] ?? null;
        $this->shippingDetails = $data['shipping_details'] ?? null;
        $this->billingDetails = $data['billing_details'] ?? null;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->amount !== null) {
            $data['amount'] = $this->amount;
        }

        if ($this->currency !== null) {
            $data['currency'] = $this->currency;
        }

        if ($this->successUrl !== null) {
            $data['success_url'] = $this->successUrl;
        }

        if ($this->cancelUrl !== null) {
            $data['cancel_url'] = $this->cancelUrl;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        if (!empty($this->lineItems)) {
            $data['line_items'] = $this->lineItems;
        }

        if ($this->active !== null) {
            $data['active'] = $this->active;
        }

        if ($this->expiresAt !== null) {
            $data['expires_at'] = $this->expiresAt;
        }

        if ($this->customerEmail !== null) {
            $data['customer_email'] = $this->customerEmail;
        }

        if ($this->customerDetails !== null) {
            $data['customer_details'] = $this->customerDetails;
        }

        if ($this->shippingDetails !== null) {
            $data['shipping_details'] = $this->shippingDetails;
        }

        if ($this->billingDetails !== null) {
            $data['billing_details'] = $this->billingDetails;
        }

        return $data;
    }
}
