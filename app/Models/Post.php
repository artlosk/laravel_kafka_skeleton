<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Contracts\HasMediaSync;
use Illuminate\Support\Facades\Auth;

class Post extends Model implements HasMedia, HasMediaSync
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['title', 'content', 'user_id'];

    public function relatedMedia(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'entity', 'media_relation_entity')
            ->withPivot('order_column', 'created_at', 'updated_at')
            ->orderBy('media_relation_entity.order_column');
    }

    public function savePost(array $data): void
    {
        $this->fill($data);

        $this->user_id = Auth::user()->id;

        $this->save();

        if (isset($data['media'])) {
            $this->syncMedia($data['media']);
        }
    }

    public function syncMedia(array $data): void
    {
        $filepondFiles = $data['filepond'] ?? [];
        $validFilepondFiles = array_filter($filepondFiles, fn($path) => $path !== null && $path !== '');

        $newMediaIds = [];
        $filepondIdMap = [];

        foreach ($validFilepondFiles as $filepondPath) {
            if (Storage::disk('public')->exists($filepondPath)) {
                try {
                    $mediaItem = $this->addMediaFromDisk($filepondPath, 'public')->toMediaCollection('images');
                    $newMediaIds[] = $mediaItem->id;
                    $filepondIdMap[$filepondPath] = $mediaItem->id;
                } catch (\Exception $e) {
                    Log::error('Error adding media from filepond: ' . $e->getMessage());
                }
            }
        }

        $receivedMediaOrderString = $data['media_order'] ?? $data['selected_media_ids'] ?? '';
        $receivedMediaOrder = array_filter(array_map('trim', explode(',', $receivedMediaOrderString)));

        $syncData = [];
        $order = 1;
        $processedMediaIds = [];

        foreach ($receivedMediaOrder as $mediaIdentifier) {
            $mediaId = null;
            if (is_numeric($mediaIdentifier)) {
                $mediaId = (int)$mediaIdentifier;
            } elseif (is_string($mediaIdentifier) && Str::startsWith($mediaIdentifier, 'filepond-tmp/')) {
                if (isset($filepondIdMap[$mediaIdentifier])) {
                    $mediaId = $filepondIdMap[$mediaIdentifier];
                } else {
                    continue;
                }
            } else {
                continue;
            }

            if ($mediaId !== null && !in_array($mediaId, $processedMediaIds)) {
                $syncData[$mediaId] = ['order_column' => $order];
                $processedMediaIds[] = $mediaId;
                $order++;
            }
        }

        try {
            $this->relatedMedia()->detach();

            if (!empty($syncData)) {
                $this->relatedMedia()->attach($syncData);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing media: ' . $e->getMessage());
            throw new \Exception('Не удалось синхронизировать медиа: ' . $e->getMessage());
        }

        $this->unsetRelation('relatedMedia');
        $this->load(['relatedMedia' => fn($query) => $query->withPivot('order_column')]);
    }

    public function getMediaOrder(): array
    {
        return $this->relatedMedia->pluck('id')->toArray();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
