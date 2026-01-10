import '../../js/bootstrap';
import $ from 'jquery';
import * as FilePond from 'filepond/dist/filepond.esm.js';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
import Sortable from 'sortablejs';
import 'filepond/dist/filepond.min.css';

import { MediaGallery } from './MediaGallery.js';
import { initializeFilePond } from './mediaUtils.js';

window.$ = window.jQuery = $;
window.FilePond = FilePond;
window.FilePondPluginFileValidateType = FilePondPluginFileValidateType;
window.FilePondPluginFileValidateSize = FilePondPluginFileValidateSize;
window.Sortable = Sortable;

FilePond.registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
);

/*$(document).ajaxSend(function(event, xhr, settings) {
    if (settings.headers && settings.headers['X-AdminLTE-Skip-Preloader'] === 'true') {
        $('.preloader').hide();
    }
});*/

/*$(document).ajaxComplete(function(event, xhr, settings) {
    if (settings.headers && settings.headers['X-AdminLTE-Skip-Preloader'] === 'true') {
        $('.preloader').hide();
    }
});*/


document.addEventListener('DOMContentLoaded', () => {
    const widgets = document.querySelectorAll('.media-gallery-widget');
    
    widgets.forEach((container) => {
        try {
            const name = container.dataset.name;
            if (!name) {
                console.warn('MediaGallery: container missing data-name attribute');
                return;
            }

            const fileInput = container.querySelector('input[type="file"].filepond');
            if (fileInput) {
                initializeFilePond(fileInput);
            }

            const gallery = new MediaGallery(container);
            container._mediaGallery = gallery;
        } catch (error) {
            console.error('Error initializing MediaGallery:', error);
        }
    });
});
