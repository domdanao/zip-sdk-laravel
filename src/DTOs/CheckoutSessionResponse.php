<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class CheckoutSessionResponse
{
    public $id;
    public $object;
    public $status;
    public $paymentUrl;
    public $currency;
    public $paymentMethodTypes;
    public $successUrl;
    public $cancelUrl;
    public $description;
    public $lineItems;
    public $metadata;
    public $customerEmail;
    public $billingAddressCollection;
    public $shippingAddressCollection;
    public $locale;
    public $createdAt;
    public $expiresAt;
    public $amountCaptured;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->object = $data['object'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->paymentUrl = $data['payment_url'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->paymentMethodTypes = $data['payment_method_types'] ?? [];
        $this->successUrl = $data['success_url'] ?? null;
        $this->cancelUrl = $data['cancel_url'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->lineItems = $data['line_items'] ?? [];
        $this->metadata = $data['metadata'] ?? [];
        $this->customerEmail = $data['customer_email'] ?? null;
        $this->billingAddressCollection = $data['billing_address_collection'] ?? null;
        $this->shippingAddressCollection = $data['shipping_address_collection'] ?? null;
        $this->locale = $data['locale'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->expiresAt = $data['expires_at'] ?? null;
        $this->amountCaptured = $data['amount_captured'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'status' => $this->status,
            'payment_url' => $this->paymentUrl,
            'currency' => $this->currency,
            'payment_method_types' => $this->paymentMethodTypes,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'description' => $this->description,
            'line_items' => $this->lineItems,
            'metadata' => $this->metadata,
            'customer_email' => $this->customerEmail,
            'billing_address_collection' => $this->billingAddressCollection,
            'shipping_address_collection' => $this->shippingAddressCollection,
            'locale' => $this->locale,
            'created_at' => $this->createdAt,
            'expires_at' => $this->expiresAt,
            'amount_captured' => $this->amountCaptured,
        ];
    }
}
