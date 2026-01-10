import $ from 'jquery';

export function initializeFilePond(input) {
    try {
        const inputElement = typeof input === 'string'
            ? document.querySelector(input)
            : input;

        if (!inputElement || inputElement.tagName !== 'INPUT' || inputElement.type !== 'file') {
            return null;
        }

        const originalInputElement = inputElement;
        const widgetContainer = originalInputElement.closest('.media-gallery-widget');

        if (!widgetContainer) {
            return null;
        }

        const widgetName = widgetContainer.dataset.name;
        const previewSelector = `#${widgetName}-selectedMediaPreview`;

        const pond = window.FilePond.create(inputElement, {
            allowMultiple: true,
            maxFiles: 5,
            name: 'media[filepond][]',
            imagePreviewHeight: 0,
            imagePreviewMaxHeight: 0,
            imagePreviewMaxFileSize: '0',
            imageCropAspectRatio: null,
            imageResizeTargetWidth: null,
            imageResizeTargetHeight: null,
            imageResizeMode: null,
            imageTransformOutputFormat: null,
            imageTransformOutputQuality: null,
            server: {
                process: window.appConfig.routes.filepondUpload,
                revert: window.appConfig.routes.filepondDelete,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                onload: (response) => response,
                ondata: (formData) => formData,
                onerror: (response) => {
                    let errorMessage = 'Произошла ошибка на сервере.';
                    if (response && response.data && response.data.error) {
                        errorMessage = response.data.error;
                    } else if (response && response.data && response.data.errors) {
                        errorMessage = 'Ошибки валидации: ' + Object.values(response.data.errors).flat().join(', ');
                    }
                    if (window.toastr) {
                        window.toastr.error(errorMessage);
                    }
                    return errorMessage;
                }
            },
            labelIdle: 'Перетащите файлы сюда или <span class="filepond--label-action"> выберите </span>',
            labelInvalidField: 'Поле содержит недопустимые файлы',
            labelFileWaitingForSize: 'Ожидание размера',
            labelFileSizeNotAvailable: 'Размер недоступен',
            labelFileLoading: 'Загрузка',
            labelFileLoadError: 'Ошибка при загрузке',
            labelFileProcessing: 'Загрузка',
            labelFileProcessingComplete: 'Загрузка завершена',
            labelFileProcessingAborted: 'Загрузка отменена',
            labelFileProcessingError: 'Ошибка при загрузке',
            labelFileRemoveError: 'Ошибка при удалении',
            labelTapToCancel: 'нажмите для отмены',
            labelTapToRetry: 'нажмите для повтора',
            labelTapToUndo: 'нажмите для отмены последнего действия',
            labelButtonRemoveItem: 'Удалить',
            labelButtonAbortItemLoad: 'Отменить загрузку',
            labelButtonRetryItemLoad: 'Повторить загрузку',
            labelButtonAbortItemProcessing: 'Отменить загрузку',
            labelButtonUndoItemProcessing: 'Отменить последнее действие',
            labelButtonRetryItemProcessing: 'Повторить загрузить',
            labelButtonProcessItem: 'Загрузить',
            labelMaxFilesExceeded: 'Превышено максимальное количество файлов ({maxFiles})',
            labelFileValidateTypeLabelExpectedTypes: 'Ожидаются файлы типа {allButLastType} или {lastType}',
            labelFileValidateTypeDescription: 'Недопустимый тип файла',
            labelFileValidateSizeLabelExpectedSize: 'Ожидается размер {filesize}',
            labelFileValidateSizeLabelMaxFileSize: 'Максимальный размер файла {filesSize}',
            labelFileValidateSizeDescription: 'Файл слишком большой',
        });

        pond.on('processfile', (error, file) => {
            if (error) return;

            const temporaryFileId = file.serverId;

            if (temporaryFileId) {
                const $previewContainer = $(previewSelector);

                if (!$previewContainer.length) {
                    return;
                }

                const isImage = file.fileType && file.fileType.startsWith('image/');
                const previewUrl = isImage ? URL.createObjectURL(file.file) : null;

                $previewContainer.append(`
                    <div class="media-preview-item col-auto p-0 mr-2 mb-2" data-media-id="${temporaryFileId}">
                        <button type="button" class="remove-btn" data-media-id="${temporaryFileId}">×</button>
                        <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 80px; height: 80px; overflow: hidden;">
                            ${isImage ? `<img src="${previewUrl}" class="img-thumbnail rounded" alt="${file.file.name}">` : `<i class="fas fa-file fa-3x text-muted"></i>`}
                        </div>
                        <small class="d-block text-truncate mt-1" title="${file.file.name}">${file.file.name}</small>
                    </div>
                `);
                updateMediaOrder($previewContainer);
                
                if (previewUrl) file.ready = true;
            } else {
                if (window.toastr) {
                    window.toastr.error('Ошибка: не получен временный ID файла от сервера.');
                }
            }
        });

        pond.on('processfilerevert', (file) => {
            const temporaryFileId = file.serverId;
            if (temporaryFileId) {
                const $previewContainer = $(previewSelector);
                $(`${previewSelector} .media-preview-item[data-media-id="${temporaryFileId}"]`).remove();
                updateMediaOrder($previewContainer);
            }
        });

        $(document).off('click.mediaPreviewRemove', `${previewSelector} .remove-btn`);
        $(document).on('click.mediaPreviewRemove', `${previewSelector} .remove-btn`, function(e) {
            e.preventDefault();
            e.stopPropagation();
            const mediaId = $(this).data('media-id');
            const $previewItem = $(this).closest('.media-preview-item');
            $previewItem.remove();
            updateMediaOrder($(previewSelector));
        });

        pond.on('preparefile', (file) => {
            if (file.serverId) {
                file.status = window.FilePond.FileStatus.PROCESSING_COMPLETE;
            }
        });

        const form = inputElement.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const files = pond.getFiles();
                const unprocessedFiles = files.filter(file => file.status !== window.FilePond.FileStatus.PROCESSING_COMPLETE);

                if (unprocessedFiles.length > 0) {
                    e.preventDefault();
                    if (window.toastr) {
                        window.toastr.error('Пожалуйста, дождитесь завершения загрузки всех файлов.');
                    }
                    return false;
                }
            });
        }

        return pond;
    } catch (error) {
        if (window.toastr) {
            window.toastr.error('Ошибка инициализации FilePond: ' + error.message);
        }
        return null;
    }
}

export function updateMediaOrder($previewContainer) {
    const order = [];
    const selectedIds = [];

    $previewContainer.find('.media-preview-item, [data-media-id]').each(function() {
        const mediaId = $(this).data('media-id');
        if (mediaId) {
            order.push(mediaId);
            selectedIds.push(mediaId);
        }
    });

    const widgetContainer = $previewContainer.closest('.media-gallery-widget');
    const widgetName = widgetContainer.data('name');
    const $orderInput = $(`#${widgetName}-mediaOrder`);
    const $selectedIdsInput = $(`#${widgetName}-selectedMediaIds`);

    if ($orderInput.length) {
        $orderInput.val(order.join(','));
    }

    if ($selectedIdsInput.length) {
        const uniqueIds = [...new Set(order)];
        $selectedIdsInput.val(uniqueIds.join(','));
    }
}
