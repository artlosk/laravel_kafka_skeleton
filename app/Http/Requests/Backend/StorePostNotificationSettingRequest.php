<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostNotificationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('access-admin-panel');
    }

    public function rules(): array
    {
        return [
            'notify_type' => [
                'required',
                'string',
                Rule::in(['role', 'user']),
                Rule::unique('post_notification_settings', 'notify_type'),
            ],
            'role_names' => 'nullable|array',
            'role_names.*' => 'exists:roles,name',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'notify_type.unique' => 'Настройка для этого типа уже существует. Пожалуйста, отредактируйте существующую.',
            'role_names.required_if' => 'Поле "Роли" обязательно для типа "По роли".',
            'user_ids.required_if' => 'Поле "Пользователи" обязательно для типа "Конкретному пользователю".',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->notify_type === 'role' && empty($this->role_names)) {
            $this->merge(['role_names' => null]);
        }
        if ($this->notify_type === 'user' && empty($this->user_ids)) {
            $this->merge(['user_ids' => null]);
        }
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}

