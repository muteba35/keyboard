<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?![\d]+$)(?!.*[\s])(?=.{2,})(?=(?:.*[a-zA-Z]){2,})^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'required',
                'digits:9',
                'regex:/^(?!0)\d{9}$/',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Le nom doit contenir au moins 2 lettres sans espace.',
            'phone.digits' => 'Le numéro doit contenir exactement 9 chiffres.',
            'phone.regex' => 'Le numéro ne doit pas commencer par 0.',
            'phone.unique' => 'Ce numéro est déjà utilisé.',
        ];
    }
}