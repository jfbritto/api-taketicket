<?php

namespace App\DTO;

class CreateEventDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?string $location = null,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly string $startDate = '',
        public readonly ?string $endDate = null,
        public readonly ?string $banner = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            location: $data['location'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            startDate: $data['start_date'],
            endDate: $data['end_date'] ?? null,
            banner: $data['banner'] ?? null,
        );
    }
}
