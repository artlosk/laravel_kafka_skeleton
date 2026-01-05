<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\HasMediaSync;

class MediaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'media' => 'array|nullable',
            'media.filepond' => 'array|nullable',
            'media.filepond.*' => 'string|nullable',
            'media.selected_media_ids' => 'string|nullable',
            'media.media_order' => 'string|nullable',
        ];
    }
}
