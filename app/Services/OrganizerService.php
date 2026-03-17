<?php

namespace App\Services;

use App\DTO\CreateOrganizerDTO;
use App\Models\Organizer;
use App\Models\User;

class OrganizerService
{
    public function __construct(private readonly AsaasService $asaasService) {}

    public function createOrganizer(User $user, CreateOrganizerDTO $dto): Organizer
    {
        return Organizer::create([
            'user_id' => $user->id,
            'name' => $dto->name,
            'description' => $dto->description,
            'logo' => $dto->logo,
            'document' => $dto->document,
            'phone' => $dto->phone,
            'address' => $dto->address,
            'city' => $dto->city,
            'state' => $dto->state,
            'postal_code' => $dto->postalCode,
        ]);
    }

    public function updateOrganizer(Organizer $organizer, CreateOrganizerDTO $dto): Organizer
    {
        $organizer->update(array_filter([
            'name' => $dto->name,
            'description' => $dto->description,
            'logo' => $dto->logo,
            'document' => $dto->document,
            'phone' => $dto->phone,
            'address' => $dto->address,
            'city' => $dto->city,
            'state' => $dto->state,
            'postal_code' => $dto->postalCode,
        ], fn ($v) => $v !== null));

        return $organizer->fresh();
    }

    public function ensureAsaasAccount(Organizer $organizer): void
    {
        if ($organizer->asaas_account_id) {
            return;
        }

        $result = $this->asaasService->createSubAccount($organizer);
        $organizer->update(['asaas_account_id' => $result['id']]);
    }
}
