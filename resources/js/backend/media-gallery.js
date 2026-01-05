import * as FilePond from 'filepond/dist/filepond.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import FilePondPluginFileValidateType
    from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginFileValidateSize
    from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
import Sortable from 'sortablejs';
import {
    initializeFilePond,
    loadMediaLibraryModal,
    attachSelectedMedia,
    updateSelectedMediaPreview,
    initMainSortable,
    initModalSortable
} from '/resources/js/mediaUtils.js';

export function initializeMediaGallery(containerSelector) {
    const $container = $(containerSelector);
    const name = $container.data('name');
    const modalSelector = `#${name}-mediaLibraryModal`;
    const contentSelector = `#${name}-mediaItemsList`;
    const attachButtonSelector = `#${name}-attachSelectedMedia`;
    const previewSelector = `#${name}-selectedMediaPreview`;
    const selectedMediaIdsSelector = `#${name}-selectedMediaIds`;
    const mediaOrderSelector = `#${name}-mediaOrder`;

    const fileInput = $container[0].querySelector('input[type="file"].filepond');
    if (fileInput) {
        initializeFilePond(fileInput);
    }

    loadMediaLibraryModal(modalSelector, contentSelector);
    attachSelectedMedia(attachButtonSelector, modalSelector);
    initMainSortable(previewSelector);
    initModalSortable(contentSelector);

    const selectedMediaIds = document.querySelector(selectedMediaIdsSelector)?.value;
    if (selectedMediaIds) {
        const initialMediaIds = selectedMediaIds.split(',').map(id => parseInt(id)).filter(id => !isNaN(id));
        if (initialMediaIds.length > 0) {
            updateSelectedMediaPreview(initialMediaIds, previewSelector);
        }
    }
}
