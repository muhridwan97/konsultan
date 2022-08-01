$(function () {
    $('#table-synchronize').on('click', '.btn-synchronize', function (e) {
        e.preventDefault();

        var idSynchronize = $(this).data('id');
        var labelSynchronize = $(this).data('label');
        var urlSynchronize = $(this).attr('href');

        var modalSynchronize = $('#modal-synchronize');
        modalSynchronize.find('form').attr('action', urlSynchronize);
        modalSynchronize.find('input[id]').val(idSynchronize);
        modalSynchronize.find('#synchronize-title').text(labelSynchronize);

        modalSynchronize.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});