<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'document_type' => ['nullable', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'cell_number' => ['nullable', 'string', 'max:20'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'contact' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'contact_cell' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'observations' => ['nullable', 'string'],
            'photo_data' => ['nullable', 'string'], // base64
        ];
    }
}
