<!-- resources/views/backend/partials/media_library_modal.blade.php -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" role="dialog" aria-labelledby="mediaLibraryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaLibraryModalLabel">Выбрать изображение из галереи</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="mediaItemsList">
                    <p class="text-center">Загрузка медиафайлов...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="attachSelectedMedia">Прикрепить выбранные</button>
            </div>
        </div>
    </div>
</div>
