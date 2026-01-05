<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasMediaSync
{
    public const MEDIA_FIELDS = [
        'filepond' => 'array|nullable',
        'filepond.*' => 'string|nullable',
        'selected_media_ids' => 'string|nullable',
        'media_order' => 'string|nullable',
    ];

    public function syncMedia(array $data): void;

    public function getMediaOrder(): array;

    public function relatedMedia(): MorphToMany;
}
