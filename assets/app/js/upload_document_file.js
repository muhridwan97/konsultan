$(function () {
    $('#table-file').on('click', '.btn-delete-file', function (e) {
        e.preventDefault();

        var idFile = $(this).data('id');
        var idDocument = $(this).data('id-document');
        var labelFile = $(this).data('label');
        var urlFile = $(this).attr('href');

        var modalDeleteFile = $('#modal-delete-file');
        modalDeleteFile.find('form').attr('action', urlFile);
        modalDeleteFile.find('input[name=id]').val(idFile);
        modalDeleteFile.find('input[name=id_upload_document]').val(idDocument);
        modalDeleteFile.find('#file-title').text(labelFile);

        modalDeleteFile.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});