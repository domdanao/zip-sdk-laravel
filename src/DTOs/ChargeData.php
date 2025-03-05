<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class ChargeData
{
    protected $amount;
    protected $currency;
    protected $source;
    protected $description;
    protected $statementDescriptor;
    protected $capture;
    protected $cvc;
    protected $requireAuth;
    protected $customerId;
    protected $redirectUrl;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->amount = $data['amount'];
        $this->currency = $data['currency'];
        $this->source = $data['source'];
        $this->description = $data['description'];
        $this->statementDescriptor = $data['statement_descriptor'];
        $this->capture = $data['capture'] ?? true;
        $this->cvc = $data['cvc'] ?? null;
        $this->requireAuth = $data['require_auth'] ?? true;
        $this->customerId = $data['customer_id'] ?? null;
        $this->redirectUrl = $data['redirect_url'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'source' => $this->source,
            'description' => $this->description,
            'statement_descriptor' => $this->statementDescriptor,
            'capture' => $this->capture,
        ];

        if ($this->cvc) {
            $data['cvc'] = $this->cvc;
        }

        if ($this->requireAuth !== true) {
            $data['require_auth'] = $this->requireAuth;
        }

        if ($this->customerId) {
            $data['customer_id'] = $this->customerId;
        }

        if ($this->redirectUrl) {
            $data['redirect_url'] = $this->redirectUrl;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
