<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'permission name']),
            'name.string' => __('validation.string', ['attribute' => 'permission name']),
            'name.max' => __('validation.max.string', ['attribute' => 'permission name', 'max' => 255]),
            'name.unique' => __('validation.unique', ['attribute' => 'permission name']),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
