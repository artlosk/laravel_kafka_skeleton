<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PostWithMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|max:10000|min:10',
            'media' => 'array|max:10',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'media_order' => 'string|nullable',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|string|max:255|min:3';
            $rules['content'] = 'sometimes|string|max:10000|min:10';
            $rules['media'] = 'sometimes|array|max:10';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Заголовок обязателен для заполнения',
            'title.string' => 'Заголовок должен быть строкой',
            'title.max' => 'Заголовок не должен превышать 255 символов',
            'title.min' => 'Заголовок должен содержать минимум 3 символа',
            'content.required' => 'Содержимое обязательно для заполнения',
            'content.string' => 'Содержимое должно быть строкой',
            'content.max' => 'Содержимое не должно превышать 10000 символов',
            'content.min' => 'Содержимое должно содержать минимум 10 символов',
            'media.array' => 'Медиафайлы должны быть переданы в виде массива',
            'media.max' => 'Максимум 10 медиафайлов на пост',
            'media.*.file' => 'Каждый элемент должен быть файлом',
            'media.*.mimes' => 'Поддерживаются только изображения: jpg, jpeg, png, gif, webp',
            'media.*.max' => 'Размер файла не должен превышать 5MB',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'заголовок',
            'content' => 'содержимое',
            'media' => 'медиафайлы',
            'media.*' => 'медиафайл',
        ];
    }
}
