<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Permission;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo('manage-permissions');
    }

    public function rules()
    {
        /** @var Permission $permission */
        $permission = $this->route('permission');
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.permission_name_required'),
            'name.unique' => __('validation.permission_name_unique'),
        ];
    }
}
