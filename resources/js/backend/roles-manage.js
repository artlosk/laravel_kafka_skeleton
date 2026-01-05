$(document).ready(function () {
    $('.select2').select2({
        placeholder: 'Select options',
        allowClear: true
    });

    $('#user_id').on('change', function () {
        const userId = $(this).val();
        // Use data-url attribute or fallback to hardcoded path if needed, 
        // but robust implementation suggests data attribute.
        // Assuming the blade template will provide data-url on the select element.
        const baseUrl = $(this).data('url') || '/admin/users';
        
        if (userId) {
            $.ajax({
                url: `${baseUrl}/${userId}/roles-permissions`,
                type: 'GET',
                success: function (data) {
                    $('#roles').val(data.roles).trigger('change');
                    $('#permissions').val(data.permissions).trigger('change');
                },
                error: function () {
                    alert('Failed to load user roles and permissions.');
                }
            });
        } else {
            $('#roles').val([]).trigger('change');
            $('#permissions').val([]).trigger('change');
        }
    });
});
