$(function () {
      
    
    $('#table-document').on('click', '.btn-delete-document', function (e) {
        e.preventDefault();

        var idDocument = $(this).closest('.row-upload-document').data('id');
        var idUpload = $(this).closest('.row-upload-document').data('id-upload');
        var labelDocument = $(this).closest('.row-upload-document').data('label');
        var urlDocument = $(this).attr('href');

        var modalDeleteDocument = $('#modal-delete-document');
        modalDeleteDocument.find('form').attr('action', urlDocument);
        modalDeleteDocument.find('input[name=id]').val(idDocument);
        modalDeleteDocument.find('input[name=id_upload]').val(idUpload);
        modalDeleteDocument.find('#document-title').text(labelDocument);

        modalDeleteDocument.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    $(document).on('click', '.btn-check-document', function (e) {
        e.preventDefault();

        var idDocument = $(this).closest('.row-upload-document').data('id');
        var idUpload = $(this).closest('.row-upload-document').data('id-upload');
        var labelDocument = $(this).closest('.row-upload-document').data('label');
        var urlDocument = $(this).attr('href');

        var modalCheckDocument = $('#modal-check-document');
        modalCheckDocument.find('form').attr('action', urlDocument);
        modalCheckDocument.find('input[name=id]').val(idDocument);
        modalCheckDocument.find('input[name=id_upload]').val(idUpload);
        modalCheckDocument.find('#document-title').text(labelDocument);

        modalCheckDocument.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    $(document).on('click', '.btn-validate-document', function (e) {
        e.preventDefault();

        var idDocument = $(this).closest('.row-upload-document').data('id');
        var labelDocument = $(this).closest('.row-upload-document').data('label');
        var urlDocument = $(this).attr('href');

        var modalValidateDocument = $('#modal-validate-document');
        modalValidateDocument.find('form').attr('action', urlDocument);
        modalValidateDocument.find('input[name=id]').val(idDocument);
        modalValidateDocument.find('#document-title').text(labelDocument);

        modalValidateDocument.modal({
            backdrop: 'static',
            keyboard: false
        });

        modalValidateDocument.find('#document-viewer').html('Fetching Document Files...');
        $.ajax({
            type: "GET",
            url: baseUrl + "upload_document/ajax_get_document_files",
            data: {id_document: idDocument},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                modalValidateDocument.find('#document-viewer').empty();
                $.each(data, function (index, value) {
                    var ext = value.source.split('.').pop().toLowerCase();
                    var source = value.file_url || baseUrl.replace(/p\/[0-9]\/$/i, '') + 'uploads/' + value.directory + '/' + value.source;
                    if (ext == 'jpg' || ext == 'png' || ext == 'jpeg') {
                        modalValidateDocument.find('#document-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            }).css('margin', '20px auto')
                        );
                    } else {
                        modalValidateDocument.find('#document-viewer').append(
                            $('<object>', {
                                width: '100%',
                                height: '500px',
                                type: 'application/pdf'
                            })
                                .attr('data', source)
                                .css('margin', '20px auto')
                                .append($('<a>', {
                                href: source,
                                target: '_blank'
                            }).html('Source file ' + value.source))
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });
});