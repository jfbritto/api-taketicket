<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomFieldRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,textarea,select,checkbox,radio,number,date',
            'required' => 'nullable|boolean',
            'options' => 'nullable|array',
            'position' => 'nullable|integer|min:0',
        ];
    }
}
