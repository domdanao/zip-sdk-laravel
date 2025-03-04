<?php

namespace Domdanao\ZipSdkLaravel\DTOs;

class SourceResponseData
{
    protected $object;
    protected $id;
    protected $type;
    protected $card;
    protected $bankAccount;
    protected $redirect;
    protected $owner;
    protected $vaulted;
    protected $used;
    protected $createdAt;
    protected $updatedAt;
    protected $metadata;

    /**
     * Create a new SourceResponseData instance from the API response
     *
     * @param array $data The raw API response data
     */
    public function __construct(array $data)
    {
        $this->object = $data['object'] ?? null;
        $this->id = $data['id'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->card = $data['card'] ?? null;
        $this->bankAccount = $data['bank_account'] ?? null;
        $this->redirect = $data['redirect'] ?? null;
        $this->owner = $data['owner'] ?? null;
        $this->vaulted = $data['vaulted'] ?? false;
        $this->used = $data['used'] ?? false;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->metadata = $data['metadata'] ?? null;
    }

    /**
     * Get the object type
     *
     * @return string|null
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * Get the source ID
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the source type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Get the card details (if available)
     *
     * @return array|null
     */
    public function getCard(): ?array
    {
        return $this->card;
    }

    /**
     * Get the bank account details (if available)
     *
     * @return array|null
     */
    public function getBankAccount(): ?array
    {
        return $this->bankAccount;
    }

    /**
     * Get the redirect URLs (if available)
     *
     * @return array|null
     */
    public function getRedirect(): ?array
    {
        return $this->redirect;
    }

    /**
     * Get the owner details (if available)
     *
     * @return array|null
     */
    public function getOwner(): ?array
    {
        return $this->owner;
    }

    /**
     * Check if the source is vaulted (securely saved for later reuse)
     *
     * @return bool
     */
    public function isVaulted(): bool
    {
        return $this->vaulted;
    }

    /**
     * Check if the source has been used
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * Get the creation timestamp
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Get the last update timestamp
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * Get the metadata (if available)
     *
     * @return array|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * Convert the DTO to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'object' => $this->object,
            'id' => $this->id,
            'type' => $this->type,
            'vaulted' => $this->vaulted,
            'used' => $this->used,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];

        // Add optional fields if they exist
        if ($this->card) {
            $data['card'] = $this->card;
        }

        if ($this->bankAccount) {
            $data['bank_account'] = $this->bankAccount;
        }

        if ($this->redirect) {
            $data['redirect'] = $this->redirect;
        }

        if ($this->owner) {
            $data['owner'] = $this->owner;
        }

        if ($this->metadata) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}
