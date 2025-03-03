<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class SourceData
{
    protected $type;
    protected $customerId;
    protected $token;
    protected $card;
    protected $bankAccount;
    protected $owner;
    protected $redirect;
    protected $metadata;

    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->customerId = $data['customer_id'] ?? null;
        $this->token = $data['token'] ?? null;
        $this->card = $data['card'] ?? null;
        $this->bankAccount = $data['bank_account'] ?? null;
        $this->owner = $data['owner'] ?? null;
        $this->redirect = $data['redirect'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
        ];

        // Add customer_id if provided
        if ($this->customerId) {
            $data['customer_id'] = $this->customerId;
        }

        // Add token if provided
        if ($this->token) {
            $data['token'] = $this->token;
        }

        // Add card details if provided
        if ($this->card) {
            $data['card'] = $this->card;
        }

        // Add bank account details if provided
        if ($this->bankAccount) {
            $data['bank_account'] = $this->bankAccount;
        }

        // Add owner details if provided
        if ($this->owner) {
            $data['owner'] = $this->owner;
        }

        // Add redirect URLs if provided
        if ($this->redirect) {
            $data['redirect'] = $this->redirect;
        }

        // Add metadata if provided
        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
