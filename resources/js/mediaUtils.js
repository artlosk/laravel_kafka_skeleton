import toastr from 'toastr';

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000"
};

if (typeof window !== 'undefined' && window.FilePond) {
    window.FilePond.registerPlugin(
        window.FilePondPluginImagePreview,
        window.FilePondPluginFileValidateType,
        window.FilePondPluginFileValidateSize
    );
}

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
                    toastr.error(errorMessage);
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
                toastr.error('Ошибка: не получен временный ID файла от сервера.');
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
                    toastr.error('Пожалуйста, дождитесь завершения загрузки всех файлов.');
                    return false;
                }
            });
        }

        return pond;
    } catch (error) {
        toastr.error('Ошибка инициализации FilePond: ' + error.message);
        return null;
    }
}

export function loadMediaLibraryModal(modalSelector, contentSelector) {
    $(document).on('click', `[data-target="${modalSelector}"]`, function (e) {
        e.preventDefault();

        if (!window.appConfig?.routes?.mediaIndex) {
            return;
        }

        loadMediaContent(window.appConfig.routes.mediaIndex);
    });

    function loadMediaContent(url) {
        $.get(url, function (data) {
            $(contentSelector).html(data);
            addMediaItemClickHandlers(contentSelector);
            restoreSelectedMediaInModal(modalSelector, contentSelector);
            initModalSortable(contentSelector);
            initMediaDeletion(contentSelector);
            $(contentSelector).find('.pagination a').on('click', function (e) {
                e.preventDefault();
                loadMediaContent($(this).attr('href'));
            });
        }).fail(function (xhr, status, error) {
            $(contentSelector).html('<p class="text-center text-danger">Не удалось загрузить медиафайлы.</p>');
            toastr.error('Не удалось загрузить галерею медиа.');
        });
    }
}

export function addMediaItemClickHandlers(containerSelector) {
    $(containerSelector).off('click', '.media-item');
    $(containerSelector).on('click', '.media-item', function (e) {
        if ($(e.target).hasClass('delete-media-btn') || $(e.target).closest('.delete-media-btn').length) {
            return;
        }
        const $item = $(this);
        $item.toggleClass('selected');
    });
}

export function restoreSelectedMediaInModal(modalSelector, contentSelector) {
    const modal = document.querySelector(modalSelector);
    if (!modal) {
        return;
    }

    const widgetContainer = modal.closest('.media-gallery-widget');
    if (!widgetContainer) {
        return;
    }

    const name = widgetContainer.dataset.name;
    const selectedMediaIdsInput = document.querySelector(`#${name}-selectedMediaIds`);
    const selectedDbIdsString = selectedMediaIdsInput ? selectedMediaIdsInput.value : '';
    const selectedDbIds = selectedDbIdsString ? selectedDbIdsString.split(',').map(id => parseInt(id)).filter(id => !isNaN(id)) : [];

    $(`${contentSelector} .media-item`).each(function () {
        const mediaId = $(this).data('media-id');
        $(this).toggleClass('selected', selectedDbIds.includes(mediaId));
    });
}

export function attachSelectedMedia(buttonSelector, modalSelector) {
    $(buttonSelector).on('click', function () {
        const modal = document.querySelector(modalSelector);
        if (!modal) {
            return;
        }

        const widgetContainer = modal.closest('.media-gallery-widget');
        if (!widgetContainer) {
            return;
        }

        const name = widgetContainer.dataset.name;
        const selectedIds = $(`#${name}-mediaItemsList .media-item.selected`).map(function () {
            return $(this).data('media-id');
        }).get();

        const existingMediaIds = $(`#${name}-selectedMediaPreview .media-preview-item`).filter(function () {
            const id = $(this).data('media-id');
            return typeof id === 'number' || (typeof id === 'string' && !id.startsWith('filepond-tmp/'));
        }).map(function () {
            return $(this).data('media-id');
        }).get();

        const currentFilepondTempIds = $(`#${name}-selectedMediaPreview .media-preview-item`).filter(function () {
            const id = $(this).data('media-id');
            return typeof id === 'string' && id.startsWith('filepond-tmp/');
        }).map(function () {
            return $(this).data('media-id');
        }).get();

        const allSelectedIds = [...existingMediaIds, ...selectedIds, ...currentFilepondTempIds];
        const uniqueIds = [...new Set(allSelectedIds)];


        $(`#${name}-selectedMediaIds`).val(uniqueIds.join(','));
        $(`#${name}-mediaOrder`).val(uniqueIds.join(','));
        updateSelectedMediaPreview(uniqueIds, `#${name}-selectedMediaPreview`);

        const modalElement = document.querySelector(modalSelector);
        if (modalElement && typeof $(modalElement).modal === 'function') {
            $(modalElement).modal('hide');
        } else if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }

        toastr.success('Медиафайлы прикреплены к посту.');
    });
}

export function updateSelectedMediaPreview(selectedDbIds, previewSelector = null) {
    if (!previewSelector) {
        const activeWidget = document.querySelector('.media-gallery-widget');
        if (!activeWidget) {
            return;
        }
        const name = activeWidget.dataset.name;
        previewSelector = `#${name}-selectedMediaPreview`;
    }

    const $previewContainer = $(previewSelector);
    if (!$previewContainer.length) {
        return;
    }

    if (!selectedDbIds || selectedDbIds.length === 0) {
        $previewContainer.empty();
        return;
    }

    if (!window.appConfig?.routes?.mediaGetByIds) {
        return;
    }

    const requestData = {ids: selectedDbIds.join(',')};
    $.get(window.appConfig.routes.mediaGetByIds, requestData, function (data) {
        $previewContainer.empty();

        const mediaArray = Array.isArray(data) ? data : Object.values(data);

        if (mediaArray && mediaArray.length > 0) {
            mediaArray.forEach(media => {
                const isImage = media.mime_type && media.mime_type.startsWith('image/');
                const previewUrl = isImage ? media.url : null;

                $previewContainer.append(`
                    <div class="media-preview-item col-auto p-0 mr-2 mb-2" data-media-id="${media.id}">
                        <button type="button" class="remove-btn" data-media-id="${media.id}">×</button>
                        <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 80px; height: 80px; overflow: hidden;">
                            ${isImage ? `<img src="${previewUrl}" class="img-thumbnail rounded" alt="${media.name}">` : `<i class="fas fa-file fa-3x text-muted"></i>`}
                        </div>
                        <small class="d-block text-truncate mt-1" title="${media.name}">${media.name}</small>
                    </div>
                `);
            });
        }

        initMainSortable(previewSelector);
        initMediaDeletion(previewSelector);
    }).fail(function (xhr, status, error) {
        toastr.error('Не удалось загрузить превью медиафайлов.');
    });
}

export function updateMediaOrder($previewContainer) {
    if (!$previewContainer || !$previewContainer.length) {
        return;
    }

    const container = $previewContainer[0];
    if (!container.sortableInstance) {
        return;
    }

    const mediaIds = [];
    $previewContainer.find('.media-preview-item').each(function () {
        const mediaId = $(this).data('media-id');
        if (mediaId) {
            mediaIds.push(mediaId);
        }
    });

    const widgetName = $previewContainer.closest('.media-gallery-widget').data('name');
    const mediaOrderSelector = `#${widgetName}-mediaOrder`;
    const mediaOrderInput = $(mediaOrderSelector);

    mediaOrderInput.val(mediaIds.join(','));
}

export function initMainSortable(containerSelector) {
    const previewContainer = document.querySelector(containerSelector);
    if (previewContainer && !previewContainer.sortableInstance) {
        const sortable = window.Sortable.create(previewContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            draggable: '.media-preview-item',
            onEnd: () => updateMediaOrder($(containerSelector))
        });
        previewContainer.sortableInstance = sortable;
    }
}

export function initModalSortable(containerSelector) {
    $(document).on('shown.bs.modal', '#mediaLibraryModal', function () {
        const modalContainer = document.querySelector(containerSelector);
        if (modalContainer && !modalContainer.sortableInstance) {
            const sortable = window.Sortable.create(modalContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                draggable: '.media-item'
            });
            modalContainer.sortableInstance = sortable;
        }
    });
}

export function initMediaDeletion(containerSelector) {
    $(containerSelector).off('click', '.remove-btn');
    $(containerSelector).on('click', '.remove-btn', function (e) {
        e.stopPropagation();
        const mediaId = $(this).data('media-id');
        const $previewItem = $(this).closest('.media-preview-item');

        $previewItem.remove();

        const widgetContainer = $(containerSelector).closest('.media-gallery-widget');
        const widgetName = widgetContainer.data('name');
        const selectedMediaIdsSelector = `#${widgetName}-selectedMediaIds`;

        const currentSelectedIdsString = $(selectedMediaIdsSelector).val();
        const currentSelectedIds = currentSelectedIdsString ? currentSelectedIdsString.split(',').map(id => parseInt(id)).filter(id => !isNaN(id)) : [];
        const updatedSelectedIds = currentSelectedIds.filter(id => id !== mediaId);
        $(selectedMediaIdsSelector).val(updatedSelectedIds.join(','));

        updateMediaOrder($(containerSelector));

        toastr.success('Медиафайл удален из поста.');
    });

    $(containerSelector).off('click', '.delete-media-btn');
    $(containerSelector).on('click', '.delete-media-btn', function (e) {
        e.stopPropagation();
        const mediaId = $(this).data('media-id');

        if (confirm('Вы уверены, что хотите полностью удалить этот медиафайл? Это действие необратимо и удалит файл из всех связанных сущностей.')) {
            $.ajax({
                url: `${window.appConfig.routes.mediaIndex}/${mediaId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function (response) {
                    $(`.media-item[data-media-id="${mediaId}"]`).remove();

                    const widgetContainer = $(containerSelector).closest('.media-gallery-widget');
                    const widgetName = widgetContainer.data('name');
                    const selectedMediaIdsSelector = `#${widgetName}-selectedMediaIds`;

                    const currentSelectedIdsString = $(selectedMediaIdsSelector).val();
                    const currentSelectedIds = currentSelectedIdsString ? currentSelectedIdsString.split(',').map(id => parseInt(id)).filter(id => !isNaN(id)) : [];
                    const updatedSelectedIds = currentSelectedIds.filter(id => id !== mediaId);
                    $(selectedMediaIdsSelector).val(updatedSelectedIds.join(','));

                    updateSelectedMediaPreview(updatedSelectedIds, containerSelector);

                    if ($('#mediaItemsList .media-item').length === 0) {
                        $('#mediaItemsList').html('<div class="col-12"><p class="text-center">Медиафайлы не найдены.</p></div>');
                    }

                    toastr.success(response.message || 'Медиафайл удален.');
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Ошибка при удалении медиафайла.';
                    toastr.error(errorMessage);
                }
            });
        }
    });
}
