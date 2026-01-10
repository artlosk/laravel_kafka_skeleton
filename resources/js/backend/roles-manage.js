import { initSelect2, waitForSelect2 } from './select2-utils';

function initRolesManage() {
    initSelect2({
        placeholder: 'Select options',
        allowClear: true
    });

    waitForSelect2(() => {
        const $userSelect = window.$('#user_id');
        const routeUrl = $userSelect.data('route-users') || $userSelect.data('url') || '/admin/users';

        $userSelect.on('change', function () {
            const userId = window.$(this).val();
            if (userId) {
                window.$.ajax({
                    url: routeUrl + '/' + userId + '/roles-permissions',
                    type: 'GET',
                    success: function (data) {
                        window.$('#roles').val(data.roles).trigger('change');
                        window.$('#permissions').val(data.permissions).trigger('change');
                    },
                    error: function () {
                        alert('Failed to load user roles and permissions.');
                    }
                });
            } else {
                window.$('#roles').val([]).trigger('change');
                window.$('#permissions').val([]).trigger('change');
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRolesManage);
} else {
    initRolesManage();
}
