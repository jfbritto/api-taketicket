<?php

namespace App\Services;

use App\DTO\CreateParticipantDTO;
use App\Models\Participant;
use App\Models\Ticket;

class ParticipantService
{
    public function createParticipant(Ticket $ticket, CreateParticipantDTO $dto): Participant
    {
        $participant = Participant::create([
            'ticket_id' => $ticket->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'document' => $dto->document,
            'birth_date' => $dto->birthDate,
            'gender' => $dto->gender,
        ]);

        foreach ($dto->customFields as $fieldId => $value) {
            $participant->fieldValues()->create([
                'custom_field_id' => $fieldId,
                'value' => $value,
            ]);
        }

        return $participant;
    }
}
