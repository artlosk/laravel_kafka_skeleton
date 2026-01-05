<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\HasMediaSync;

class PostCreateRequest extends MediaRequest
{
    public function authorize()
    {
        return $this->user()->can('create-posts');
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле заголовка обязательно.',
            'title.max' => 'Заголовок не должен превышать 255 символов.',
            'content.required' => 'Поле содержимого обязательно.',
            'media.array' => 'Поле медиа должно быть массивом.',
            'media.filepond.array' => 'Поле загрузки файлов должно быть массивом.',
            'media.selected_media_ids.string' => 'Выбранные медиафайлы должны быть строкой.',
            'media.media_order.string' => 'Порядок медиафайлов должен быть строкой.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
