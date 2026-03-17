<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:1',
            'sale_start' => 'sometimes|required|date',
            'sale_end' => 'sometimes|required|date|after:sale_start',
            'max_per_user' => 'nullable|integer|min:1',
        ];
    }
}
