$(function () {
    const modalViewDocument = $('#modal-view-document');

    $(document).on('click', '.btn-preview-document', function (e) {
        e.preventDefault();

        const documentId = $(this).data('id');

        modalViewDocument.find('#document-viewer').html('<i class="fa fa-spinner"></i> Fetching document...');

        $.ajax({
            type: "GET",
            url: baseUrl + "upload-document/ajax-get-document-files",
            data: {id_document: documentId},
            cache: true,
            accepts: {text: "application/json"},
            success: function (data) {
                modalViewDocument.find('#document-viewer').empty();
                $.each(data, function (index, value) {
                    const ext = value.source.split('.').pop().toLowerCase();
                    const source = baseUrl.replace(/p\/[0-9]\/$/i, '') + 'uploads/' + value.directory + '/' + value.source;
                    if (ext === 'jpg' || ext === 'png' || ext === 'jpeg' || ext === 'gif') {
                        modalViewDocument.find('#document-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            })
                        );
                    } else {
                        modalViewDocument.find('#document-viewer').append(
                            $('<object>', {
                                width: '100%',
                                height: '500px',
                                type: 'application/pdf'
                            })
                                .attr('data', source)
                                .append($('<a>', {
                                href: source,
                                target: '_blank'
                            }).html('Download: ' + value.source))
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