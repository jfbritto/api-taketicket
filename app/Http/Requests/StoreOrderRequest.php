<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'billing_type' => 'required|in:PIX,CREDIT_CARD',
            'items' => 'required|array|min:1',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.participants' => 'required|array',
            'items.*.participants.*.name' => 'required|string|max:255',
            'items.*.participants.*.email' => 'required|email',
            'items.*.participants.*.document' => 'nullable|string',
            'items.*.participants.*.phone' => 'nullable|string',
            'items.*.participants.*.birth_date' => 'nullable|date',
            'items.*.participants.*.gender' => 'nullable|in:male,female,other',
            'items.*.participants.*.custom_fields' => 'nullable|array',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('items', []) as $i => $item) {
                $participants = $item['participants'] ?? [];
                $quantity = $item['quantity'] ?? 0;
                if (count($participants) !== (int) $quantity) {
                    $validator->errors()->add(
                        "items.{$i}.participants",
                        "Participants count must match quantity ({$quantity})."
                    );
                }
            }
        });
    }
}
