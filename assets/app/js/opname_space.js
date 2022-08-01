$(function () {
    $('#table-opname-space').on('click', '.btn-delete-opname-space', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var url = $(this).attr('href');

        var modalDelete = $('#modal-delete-opname');
        modalDelete.find('form').attr('action', url);
        modalDelete.find('input[name=id]').val(id);
        modalDelete.find('#opname-title').text(label);

        modalDelete.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-opname-space').on('click', '.btn-validate-opname-space', function (e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlValidate = $(this).attr('href');

        var modalValidateBooking = $('#modal-validate-opname');
        modalValidateBooking.find('form').attr('action', urlValidate);
        modalValidateBooking.find('input[name=id]').val(id);
        modalValidateBooking.find('#opname-title').text(label);

        modalValidateBooking.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});