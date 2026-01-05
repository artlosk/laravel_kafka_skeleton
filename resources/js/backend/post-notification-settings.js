import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

select2($);

function initSelect2() {
    $('.select2').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                placeholder: 'Выберите...',
                allowClear: true
            });
        }
    });
}

function destroySelect2(selectors) {
    selectors.forEach(selector => {
        const $el = $(selector);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    });
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

    initSelect2();
}

function initPostNotificationSettings() {
    if ($('#notify_type').length === 0) {
        return;
    }

    initSelect2();
    $('#notify_type').off('change', handleNotifyTypeChange).on('change', handleNotifyTypeChange);
    $('#notify_type').trigger('change');
}

$(document).ready(function() {
    initPostNotificationSettings();
});

window.initPostNotificationSettings = initPostNotificationSettings;

