$(function () {
    /**
     * Document Uploader
     * @type {void|jQuery|HTMLElement}
     */
    var documentUploader = $('#document-uploader');
    var inputFile = documentUploader.find('#input-file');
    var inputWrapper = documentUploader.find('#uploaded-input-wrapper');
    var uploadedFile = documentUploader.find('#uploaded-file');
    var uploadedItemTemplate = $('#upload-item-template').html();

    /**
     * Delete uploaded file (persisted to database)
     */
    var uploadedOldFile = $('#uploaded-old-file');
    const modalConfirm = $('#modal-confirm');
    modalConfirm.find('.modal-title').text('Delete File');
    let activeUploadedItem = null;

    uploadedOldFile.on('click', '.btn-delete-uploaded-file', function (e) {
        e.preventDefault();

        activeUploadedItem = $(this).closest('.uploaded-item');
        modalConfirm.find('.modal-message').html(`Are you sure want to delete uploaded file <strong>${$(this).data('file').replace(/_/g, ' ')}</strong>?`);
        modalConfirm.find('#btn-yes').data('url', $(this).prop('href'));

        modalConfirm.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalConfirm.find('#btn-yes').on('click', function () {
        let buttonYes = $(this);
        buttonYes.prop('disabled', true).text('DELETING...');
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            accepts: { text: "application/json" },
            success: function (data) {
                buttonYes.prop('disabled', false).text('YES');
                if (data || data.status) {
                    modalConfirm.modal('hide');
                    activeUploadedItem.remove();
                } else {
                    alert('Failed delete uploaded file');
                }
            },
            error: function (xhr, status, error) {
                buttonYes.prop('disabled', false).text('YES');
                console.log(xhr.responseText, status, error);
            }
        });
    });
    modalConfirm.find('#btn-no').on('click', function () {
        modalConfirm.modal('hide');
    });

    /**
     * Handle heavy process to upload, abort multiple files.
     */
    inputFile.fileupload({
        url: baseUrl + 'upload_document_file/upload_s3',
        dataType: 'json',
        add: function (e, data) {
            var uploadErrors = [];
            var acceptFileTypes = /(gif|jpe?g|png)/i;

            // validate each added file
            if (data.files[0]['type'].length && !acceptFileTypes.test(data.files[0]['type'])) {
                uploadErrors.push('Not an accepted file type ' + data.files[0]['name']);
            }
            if (data.files[0]['size'] && data.files[0]['size'] > 10000000) {
                uploadErrors.push('File ' + data.files[0]['name'] + ' size max 10MB');
            }

            if (uploadErrors.length > 0) {
                alert(uploadErrors.join("\n"));
            } else {
                compress(data.files[0], 700, 'auto', .80, function (file) {
                    data.files = [file];

                    // set preview ready
                    var item = $.parseHTML(uploadedItemTemplate);
                    if (data.files && data.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $(item).find('.upload-file-preview').prop('src', e.target.result);
                        };
                        reader.readAsDataURL(data.files[0]);
                    }

                    // add upload to list
                    $(item).addClass('uploading');
                    $(item).find('.btn-delete-file').on('click', function () {
                        if ($(item).hasClass('uploading')) {
                            data.abort();
                            data.context.remove();
                        }
                    });
                    data.context = $(item).appendTo(uploadedFile);
                    data.submit();
                    $('.btn-defer-upload').prop('disabled', true);
                });
            }
        },
        done: function (e, data) {
            var context = $(data.context);
            $.each(data.result, function (index, file) {
                if (file && file.status) {
                    $(context).find('.upload-file-preview').attr('src', file.data.file_url);
                    $(context).find('.upload-file-preview-link').attr('href', file.data.file_url);
                    $(context).find('.upload-file-name').text(file.data.client_name);
                    $(context).find('.btn-delete-file').data('file', file.data.file_name);
                    var input = $('<input/>', {
                        type: 'hidden',
                        name: index + '_uploaded[]',
                        class: 'uploaded_inputs',
                        value: file.data.file_name
                    });
                    inputWrapper.append(input);
                } else {
                    $(context).find('.upload-file-name').html($(file.errors).addClass('text-danger'));
                }
            });
        },
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var progressBar = $(data.context).find('.progress-bar');
            progressBar.css('width', progress + '%').text(progress + '%');
            if (progress == 100) {
                progressBar.removeClass('progress-bar-danger');
                $(data.context).removeClass('uploading');
                $(data.context).find('.btn-delete-file').removeClass('btn-warning').addClass('btn-danger').text('DELETE');
            }
        },
        fail: function (e, data) {
            console.log(data.textStatus);
        },
        stop: function (e) {
            $('.btn-defer-upload').prop('disabled', false);
        },
    });

    /**
     * Delete temporary file before persist to database (recent upload file).
     */
    uploadedFile.on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        if (file) {
            buttonDelete.prop('disabled', true).text('DELETING...');
            $('.btn-defer-upload').prop('disabled', true);
            $.ajax({
                url: baseUrl + 'upload_document_file/delete_temp_s3',
                type: 'POST',
                data: { file: file },
                accepts: { text: "application/json" },
                success: function (data) {
                    $('.btn-defer-upload').prop('disabled', false);
                    if (data || data.status) {
                        inputWrapper.find('input[value="' + file + '"]').remove();
                        buttonDelete.closest('.uploaded-item').remove();
                    } else {
                        buttonDelete.prop('disabled', false).text('DELETE');
                        alert('Failed delete uploaded file');
                    }
                },
                error: function (xhr, status, error) {
                    buttonDelete.prop('disabled', false).text('DELETE');
                    $('.btn-defer-upload').prop('disabled', false);
                    console.log(xhr.responseText, status, error);
                }
            });
        } else {
            // already handled by abort upload event, so this is not necessary
            // buttonDelete.closest('.uploaded-item').remove();
        }
    });

    /**
     * Compress image from source.
     *
     * @param sourceFile instance of File object (input file)
     * @param resizeWidth resize width base, input 'auto' if you want to constraint with height
     * @param resizeHeight resize height base, input 'auto' if you want to constraint with width
     * @param quality range 0-1 (100%) quality of image
     * @param callback function that called after compress
     */
    function compress(sourceFile, resizeWidth, resizeHeight, quality, callback) {
        //toBlob polyfill
        if (!HTMLCanvasElement.prototype.toBlob) {
            Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
                value: function (callback, type, quality) {
                    var dataURL = this.toDataURL(type, quality).split(',')[1];
                    setTimeout(function () {
                        var binStr = atob(dataURL),
                            len = binStr.length,
                            arr = new Uint8Array(len);
                        for (var i = 0; i < len; i++) {
                            arr[i] = binStr.charCodeAt(i);
                        }
                        callback(new Blob([arr], { type: type || 'image/png' }));
                    });
                }
            });
        }

        const fileName = sourceFile.name;
        const reader = new FileReader();
        reader.readAsDataURL(sourceFile);
        reader.onload = event => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const elem = document.createElement('canvas');

                let width = img.width;
                let height = img.height;

                if (resizeWidth) {
                    if (resizeWidth !== 'auto' && img.width > resizeWidth) {
                        width = resizeWidth;
                        if (resizeHeight === 'auto') {
                            const scaleFactor = width / img.width;
                            height = img.height * scaleFactor;
                        }
                    }
                }

                if (resizeHeight) {
                    if (resizeHeight !== 'auto' && img.height > resizeHeight) {
                        height = resizeHeight;
                        if (resizeWidth === 'auto') {
                            const scaleFactor = height / img.height;
                            width = img.width * scaleFactor;
                        }
                    }
                }

                elem.width = width;
                elem.height = height;

                const ctx = elem.getContext('2d');
                // img.width and img.height will contain the original dimensions
                ctx.drawImage(img, 0, 0, elem.width, elem.height);
                ctx.canvas.toBlob((blob) => {
                    const file = new File([blob], fileName, {
                        type: blob.type,
                        lastModified: Date.now()
                    });
                    callback(file, blob);
                }, sourceFile.type, quality);
            };
            reader.onerror = error => console.log(error);
        };
    }


    const tallyEditor = $('.tally-editor');
    const modalPhotoEditor = $('#modal-photo-editor');
    const uploadedViewWrapper = modalPhotoEditor.find('#uploaded-old-file .box-body');
    const uploadedViewTemplate = $('#uploaded-view-template').html();
    let activeRow = null;

    tallyEditor.on('click', '.btn-photo-goods', function () {
        activeRow = $(this).closest('tr');
        inputWrapper.empty();
        uploadedFile.empty();
        uploadedViewWrapper.empty();

        // load temp photo (recently uploaded that not persisted into database yet)
        const tempPhotos = (activeRow.find('#temp_photos').val() || '').split(',').filter(Boolean);
        const tempPhotoDescriptions = (activeRow.find('#temp_photo_descriptions').val() || '').split('||').filter(Boolean);
        tempPhotos.forEach((tempPhoto, index) => {
            // build hidden input in modal photo editor
            const input = $('<input/>', {
                type: 'hidden',
                name: $('#input-file').prop('name') + '_uploaded[]',
                class: 'uploaded_inputs',
                value: tempPhoto
            });
            inputWrapper.append(input);

            // build uploaded state
            var item = $.parseHTML(uploadedItemTemplate);
            $(item).find('.upload-file-preview').attr('src', (assetUrlS3 || (assetUrl + 'uploads/')) + 'temp/' + tempPhoto);
            $(item).find('.upload-file-preview-link').attr('href', (assetUrlS3 || (assetUrl + 'uploads/')) + 'temp/' + tempPhoto);
            $(item).find('.upload-file-name').text(tempPhoto);
            $(item).find('.uploaded_descriptions').val(tempPhotoDescriptions[index] || '');
            $(item).find('.btn-delete-file').data('file', tempPhoto);
            $(item).find('.progress-bar').removeClass('progress-bar-danger').css('width', '100%').text('100%');
            $(item).find('.btn-delete-file').removeClass('btn-warning').addClass('btn-danger').text('DELETE');
            uploadedFile.append($(item));
        });

        // load existing (persisted photo from database)
        const workOrderGoodsId = activeRow.find('#workOrderGoodsId').val();
        if (workOrderGoodsId) {
            fetch(baseUrl + 'work-order-goods-photo/ajax_photos?id_work_order_goods=' + workOrderGoodsId)
                .then(result => result.json())
                .then(photos => {
                    if (photos.length) {
                        uploadedViewWrapper.html('<div class="row">');
                        photos.forEach((photo) => {
                            var uploadedView = uploadedViewTemplate
                                .replace(/{{id}}/g, photo.id)
                                .replace(/{{file}}/g, truncate(photo.src.replace(/^.*[\\\/]/, ''), 35))
                                //.replace(/{{src}}/g, (assetUrlS3 || assetUrl) + photo.src)
                                .replace(/{{src}}/g, photo.url)
                                .replace(/{{description}}/g, photo.description || 'No description');
                            uploadedView = $.parseHTML(uploadedView);

                            uploadedViewWrapper.append(uploadedView);
                        });
                        uploadedViewWrapper.append('</div>');
                    } else {
                        uploadedViewWrapper.html('<p class="text-muted">No photo available for this work order item.</p>');
                    }
                })
                .catch(console.log);
        } else {
            uploadedViewWrapper.html('<p class="text-muted">New goods does not has any photo yet.</p>');
        }

        modalPhotoEditor.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalPhotoEditor.on('click', '.close, .btn-save-photo', function () {
        let uploadedPhotos = '';
        let uploadedPhotosDescription = '';
        var inputs = modalPhotoEditor.find('.uploaded_inputs').map(function () {
            return $(this).val();
        }).get();
        var inputDescriptions = modalPhotoEditor.find('.uploaded_descriptions').map(function () {
            return $(this).val().replace('||', '') || '-';
        }).get();

        if (inputs && Array.isArray(inputs)) {
            uploadedPhotos = inputs.join(',');
        }
        if (inputDescriptions && Array.isArray(inputDescriptions)) {
            uploadedPhotosDescription = inputDescriptions.join('||');
        }
        console.log(uploadedPhotos);
        console.log(uploadedPhotosDescription);

        activeRow.find('#temp_photos').val(uploadedPhotos);
        activeRow.find('#temp_photo_descriptions').val(uploadedPhotosDescription);

        modalPhotoEditor.modal('hide');
    });
});