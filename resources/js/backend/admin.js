import '../../js/bootstrap';
import 'bootstrap';
import 'admin-lte';

import './darkMode';
import {initDateInputs} from './dateInput';
import './users';

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
