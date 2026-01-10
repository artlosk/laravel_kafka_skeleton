import '../../js/bootstrap';
import $ from 'jquery';
import 'admin-lte';
import toastr from 'toastr';
import 'toastr/build/toastr.css';

window.$ = window.jQuery = $;

import './darkMode';
import {initDateInputs} from './dateInput';
import './users';
import './post-notification-settings';
import './roles-manage';

window.toastr = toastr;
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '5000'
};

initDateInputs();

if (typeof window.updateDarkModeButtonState === 'function') {
    if (document.readyState === 'complete') {
        window.updateDarkModeButtonState();
    } else {
        window.addEventListener('load', function () {
            window.updateDarkModeButtonState();
        });
    }
}
