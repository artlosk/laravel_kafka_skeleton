<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRolesPermissionsRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo('manage-roles') && $this->user()->hasPermissionTo('manage-permissions');
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('validation.user_id_required'),
            'user_id.exists' => __('validation.user_id_invalid'),
        ];
    }
}
