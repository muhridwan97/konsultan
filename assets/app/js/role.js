$(function () {

    $('#form-role').find('.check_all').on('ifChanged', function () {
        var module = $(this).val();
        if ($(this).is(":checked")) {
            $('.' + module).iCheck('check');
        }
        else {
            $('.' + module).iCheck('uncheck');
        }
    });

    $('#form-role.edit').find('.check_all').each(function () {
        var module = $(this).val();
        var isCheckedAll = true;
        $('.' + module).each(function () {
            if (!$(this).is(":checked")) {
                isCheckedAll = false;
            }
        });

        if (isCheckedAll) {
            $(this).iCheck('check');
        }
    });
});