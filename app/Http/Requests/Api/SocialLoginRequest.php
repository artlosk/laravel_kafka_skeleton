<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:50',
            'uid' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Email должен быть корректным адресом электронной почты.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'provider.string' => 'Провайдер должен быть строкой.',
            'provider.max' => 'Провайдер не должен превышать 50 символов.',
            'uid.string' => 'UID должен быть строкой.',
            'uid.max' => 'UID не должен превышать 255 символов.',
        ];
    }
}

