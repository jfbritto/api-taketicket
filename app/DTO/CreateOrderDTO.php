<?php

namespace App\DTO;

use App\Enums\BillingType;

class CreateOrderDTO
{
    public function __construct(
        public readonly int $eventId,
        public readonly BillingType $billingType,
        public readonly array $items,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            eventId: $data['event_id'],
            billingType: BillingType::from($data['billing_type']),
            items: $data['items'],
        );
    }
}
