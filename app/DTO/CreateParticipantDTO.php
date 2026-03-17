<?php

namespace App\DTO;

class CreateParticipantDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $document = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $gender = null,
        public readonly array $customFields = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            document: $data['document'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            gender: $data['gender'] ?? null,
            customFields: $data['custom_fields'] ?? [],
        );
    }
}
