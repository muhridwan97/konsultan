$(function () {

    $('#form-synchronize select#module').on('change', function () {
        if ($(this).val()) {
            $('#module-synchronize-wrapper').show();
            $('#module-content-wrapper').empty();
            htmlTable = '';

            // reset
            $('#form-synchronize').find('input[name*=table_target]').val('');
            $('#form-synchronize').find('input[name*=field_target]').val('');
            $('#form-synchronize').find('.target-result').html('');
        }
    });

    var modalBrowseModule = $('#modal-browse-module');
    var htmlTable = '';
    var buttonTarget = null;

    $('#form-synchronize').on('click', '.btn-select-target', function (e) {
        e.preventDefault();

        buttonTarget = $(this);
        $('.btn-table-list').hide();

        var idModule = $('#form-synchronize').find('select#module').val();
        var labelModule = $('#form-synchronize').find('select#module option:selected').text();

        if (htmlTable != '') {
            $('#module-content-wrapper').html(htmlTable);
        } else {
            $.ajax({
                type: "GET",
                url: baseUrl + "module/ajax_get_module_table",
                data: {
                    id_module: idModule
                },
                cache: true,
                success: function (data) {
                    $('#module-content-wrapper').html(data);
                    htmlTable = data;
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText + ' ' + status + ' ' + error);
                }
            });
        }

        modalBrowseModule.find('#module-name').text(labelModule);
        modalBrowseModule.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalBrowseModule.on('click', '.btn-browse-table', function () {
        $.ajax({
            type: "GET",
            url: baseUrl + "module/ajax_get_module_table_field",
            data: {
                id_module: $(this).data('module'),
                table_name: $(this).data('table')
            },
            cache: true,
            success: function (data) {
                $('#module-content-wrapper').html(data);
                $('.btn-table-list').show();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

    modalBrowseModule.on('click', '.btn-select-field', function () {
        var table = $(this).data('table');
        var field = $(this).data('field');

        buttonTarget.closest('tr').find('input[name*=table_target]').val(table);
        buttonTarget.closest('tr').find('input[name*=field_target]').val(field);
        buttonTarget.closest('tr').find('.target-result').html('Table <strong>' + table + '</strong> field <strong>' + field + '</strong>');

        modalBrowseModule.modal('hide');
    });

    modalBrowseModule.on('click', '.btn-set-blank-field', function () {
        buttonTarget.closest('tr').find('input[name*=table_target]').val('');
        buttonTarget.closest('tr').find('input[name*=field_target]').val('');
        buttonTarget.closest('tr').find('.target-result').html('');
        modalBrowseModule.modal('hide');
        $('.btn-table-list').hide();
    });

    modalBrowseModule.on('click', '.btn-table-list', function () {
        $('#module-content-wrapper').html(htmlTable);
        $(this).hide();
    });

});