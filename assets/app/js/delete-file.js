$(function () {
    function checkButtonUpload(wrapper) {
        if (wrapper.find('.upload-input-wrapper').children().length) {
            wrapper.find('.file-upload-browse').text('Add More file');
            wrapper.find('.button-file').attr('disabled', true);
        } else {
            wrapper.find('.file-upload-browse').text('Select file');
            wrapper.find('.button-file').attr('disabled', false);
            wrapper.find('.progress-bar')
                .removeClass('progress-bar-danger')
                .addClass('progress-bar-success')
                .css(
                    'width', '0%'
                );
        }
    }
    
    $(document).on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        console.log('delete');
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        $.ajax({
            url: baseUrl + 'upload-document-file/delete-temp-s3',
            type: 'POST',
            data: {
                file: file
            },
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                if (data.status || data.status == 'true') {
                    var inputFileParent = buttonDelete.closest('.form-group');
                    inputFileParent.find('input[value="' + file + '"]').remove();
                    inputFileParent.find('.file-upload-info').val("");
                    inputFileParent.find('.file-upload-info').attr("placeholder", "Capture photo");
                    buttonDelete.parent().remove();
                    checkButtonUpload(inputFileParent);
                    alert('File ' + file + ' is deleted');
                } else {
                    alert('Failed delete uploaded file');
                }
            }
        })
    });

});