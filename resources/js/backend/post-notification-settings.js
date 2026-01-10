import { initSelect2, destroySelect2 as destroySelect2Util } from './select2-utils';

function initSelect2Local() {
    initSelect2({
        placeholder: 'Выберите...',
        allowClear: true
    });
}

function destroySelect2(selectors) {
    destroySelect2Util(selectors);
}

function handleNotifyTypeChange() {
    const type = $('#notify_type').val();
    destroySelect2(['#role_names', '#user_ids']);
    
    if (type === 'role') {
        $('#role_field').show();
        $('#user_field').hide();
        $('#role_names').prop('required', true);
        $('#user_ids').prop('required', false);
    } else if (type === 'user') {
        $('#role_field').hide();
        $('#user_field').show();
        $('#role_names').prop('required', false);
        $('#user_ids').prop('required', true);
    } else {
        $('#role_field').hide();
        $('#user_field').hide();
        $('#role_names').prop('required', false);
        $('#user_ids').prop('required', false);
    }

    initSelect2Local();
}

function initPostNotificationSettings() {
    if ($('#notify_type').length === 0) {
        return;
    }

    initSelect2Local();
    $('#notify_type').off('change', handleNotifyTypeChange).on('change', handleNotifyTypeChange);
    $('#notify_type').trigger('change');
}

$(document).ready(function() {
    initPostNotificationSettings();
});

window.initPostNotificationSettings = initPostNotificationSettings;

