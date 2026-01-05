<!-- resources/views/components/media-gallery.blade.php -->
@props([
    'name' => 'media',
    'initialMedia' => [],
])

<div class="media-gallery-widget" data-name="{{ $name }}">
    <div class="form-group">
        <label for="{{ $name }}-filepond">Загрузить файлы:</label>
        <input type="file" class="filepond" multiple data-max-files="5">
    </div>
    <input type="hidden" name="{{ $name }}[selected_media_ids]" id="{{ $name }}-selectedMediaIds"
           value="{{ collect($initialMedia)->pluck('id')->join(',') }}">
    <input type="hidden" name="{{ $name }}[media_order]" id="{{ $name }}-mediaOrder"
           value="{{ collect($initialMedia)->pluck('id')->join(',') }}">
    <button type="button" class="btn btn-secondary mb-3" data-toggle="modal"
            data-target="#{{ $name }}-mediaLibraryModal">
        Выбрать из галереи
    </button>
    <div id="{{ $name }}-selectedMediaPreview" class="mb-3 row"></div>

    <div class="modal fade" id="{{ $name }}-mediaLibraryModal" tabindex="-1" role="dialog"
         aria-labelledby="{{ $name }}-mediaLibraryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $name }}-mediaLibraryModalLabel">Галерея медиафайлов</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="{{ $name }}-mediaItemsList">
                        <p class="text-center">Загрузка медиафайлов...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="{{ $name }}-attachSelectedMedia">Прикрепить
                        выбранные
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@vite(['resources/css/backend/media-gallery.css', 'resources/js/backend/media-gallery.js', 'resources/js/backend/admin-media.js'])
