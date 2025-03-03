<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class CheckoutSessionData
{
    protected $currency;
    protected $paymentMethodTypes;
    protected $successUrl;
    protected $cancelUrl;
    protected $description;
    protected $lineItems;
    protected $metadata;
    protected $customerEmail;
    protected $billingAddressCollection;
    protected $shippingAddressCollection;
    protected $locale;

    public function __construct(array $data)
    {
        $this->currency = $data['currency'] ?? config('zip-sdk.defaults.currency');
        $this->paymentMethodTypes = $data['payment_method_types'] ?? config('zip-sdk.defaults.payment_methods');
        $this->successUrl = $data['success_url'];
        $this->cancelUrl = $data['cancel_url'];
        $this->description = $data['description'] ?? null;
        $this->lineItems = $data['line_items'] ?? [];
        $this->metadata = $data['metadata'] ?? [];
        $this->customerEmail = $data['customer_email'] ?? null;
        $this->billingAddressCollection = $data['billing_address_collection'] ?? null;
        $this->shippingAddressCollection = $data['shipping_address_collection'] ?? null;
        $this->locale = $data['locale'] ?? config('zip-sdk.defaults.locale');
    }

    public function toArray(): array
    {
        $data = [
            'currency' => $this->currency,
            'payment_method_types' => $this->paymentMethodTypes,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'line_items' => $this->lineItems,
            'locale' => $this->locale,
        ];

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        if ($this->customerEmail) {
            $data['customer_email'] = $this->customerEmail;
        }

        if ($this->billingAddressCollection) {
            $data['billing_address_collection'] = $this->billingAddressCollection;
        }

        if ($this->shippingAddressCollection) {
            $data['shipping_address_collection'] = $this->shippingAddressCollection;
        }

        return $data;
    }
}
