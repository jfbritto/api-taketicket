<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_code' => 'required_without:qr_code_payload|string',
            'qr_code_payload' => 'required_without:ticket_code|string',
            'device' => 'nullable|string',
        ];
    }
}
