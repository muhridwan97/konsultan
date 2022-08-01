$(function () {
    const photoTemplate = $('#photo-template').html();
    const btnAddPhoto = $('#btn-add-photo');
    const btnAddItem = $('.btn-add-item');
    const formUploadPhoto = $('#form-photo');
    let photoWrapper = $('#photo-wrapper');
    let totalPhoto = photoWrapper.children().length;


    btnAddPhoto.on('click', function (e) {
        e.preventDefault();
        photoWrapper.append(
            photoTemplate
                .replace(/{{no}}/g, (totalPhoto + 1))
                .replace(/{{index}}/g, totalPhoto)
        );
        totalPhoto++;
        uploadPhoto();
        if(totalPhoto>1){
            $('#item_name_0').attr('required', true);
            $('#no_hs_0').attr('required', true);
            $('.card-photo').addClass('required-document');
        }
    });
    photoWrapper.on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();
        var btnSubmit = $(this).closest('form');
        btnSubmit.find(':submit').attr('disabled', false);

        totalPhoto--;

        if(totalPhoto == 1){
            $('#item_name_0').attr('required', false);
            $('#no_hs_0').attr('required', false);
            $('.card-photo').removeClass('required-document');
        }
        $(this).closest('.card-photo').remove();

    });

    uploadPhoto();

    function uploadPhoto() {
    $('.upload-photo').fileupload({
        url: baseUrl + 'upload_document_file/upload_s3',
        dataType: 'json',
        add: function(e, data) {
            $(this).addClass('uploading', true);
            var btnSubmit = $(this).closest('form');
            btnSubmit.find(':submit').attr('disabled', true);
            data.submit();
        },
        done: function (e, data) {
            var inputFileParent = $(this).closest('.form-group');
            inputFileParent.find('.text-danger').remove();
            $.each(data.result, function (index, file) {
                if (file != null && file.status) {
                    inputFileParent.find('.uploaded-file')
                        .append($('<p/>', {class: 'text-muted text-ellipsis'})
                            .html('<a href="#" data-file="' + file.data.file_name + '" class="btn btn-danger btn-sm btn-delete-file">DELETE</a> &nbsp; ' + file.data.client_name));
                    inputFileParent.find('.upload-input-wrapper')
                        .append($('<input/>', {
                            type: 'hidden',
                            name: index + '_name[]',
                            value: file.data.file_name
                        }));
                } else {
                    inputFileParent.find('.progress-bar')
                        .addClass('progress-bar-danger')
                        .text('Upload failed').css(
                        'width', '100%'
                    );
                    inputFileParent.find('.uploaded-file')
                        .append($(file.errors).addClass('text-danger'));
                }
            });
            checkButtonUpload(inputFileParent);
            var btnSubmit = $(this).closest('form');
            btnSubmit.find(':submit').attr('disabled', false);
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                'width',
                progress + '%'
            ).text(progress + '%');
        },
        fail: function (e, data) {
            alert(data.textStatus);
        }
    });
    };

    photoWrapper.on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        $.ajax({
            url: baseUrl + 'upload_document_file/delete_temp_s3',
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
                    buttonDelete.parent().remove();
                    checkButtonUpload(inputFileParent);
                    alert('File ' + file + ' is deleted');
                } else {
                    alert('Failed delete uploaded file');
                }
            }
        })
    });

    function checkDisableSubmitButton(item) {
        const buttonSubmit = $(item).closest('form').find('button[type=submit]');
        if ($('.upload-document.uploading').length) {
            buttonSubmit.prop('disabled', true);
        } else {
            buttonSubmit.prop('disabled', false);
        }
    }
    
    function checkButtonUpload(wrapper) {
        if (wrapper.find('.upload-input-wrapper').children().length) {
            wrapper.find('.button-file').text('More file');
        } else {
            wrapper.find('.button-file').text('Select file');
            wrapper.find('.progress-bar')
                .removeClass('progress-bar-danger')
                .addClass('progress-bar-success')
                .css(
                    'width', '0%'
                );
        }
    }

    var modalAddItem = $('#modal-add-item');
    var modalValidatePhoto = $('#modal-validate-photo');
    function changeModalAdd(modal) {
        modalAddItem = modal;
    }
    $(document).on('click', '.btn-validate-photo', function (e) {
        e.preventDefault();

        var idPhoto = $(this).closest('.row-upload-photo').data('id');
        var labelPhoto = $(this).closest('.row-upload-photo').data('label');
        var idUpload = $(this).closest('.row-upload-photo').data('id-upload');
        var idPerson = $(this).closest('.row-upload-photo').data('id-person');
        var idItem = $(this).closest('.row-upload-photo').data('id-item');
        var itemName = $(this).closest('.row-upload-photo').data('item-name');
        var urlPhoto = $(this).attr('href');
        
        if(idItem !== ''){
            modalValidatePhoto.find('#item_name').html(
                $('<option>', {value: idItem}).text(`${itemName}`)
            ).trigger('change');
        }
        changeModalAdd(modalValidatePhoto);
        modalValidatePhoto.find('form').attr('action', urlPhoto);
        modalValidatePhoto.find('input[name=id]').val(idPhoto);
        modalValidatePhoto.find('input[name=id_upload]').val(idUpload);
        modalValidatePhoto.find('input[name=id_customer]').val(idPerson);
        modalValidatePhoto.find('#photo-title').text(labelPhoto);
        modalValidatePhoto.find('select[name=item_name]').data('params','owner='+idPerson);

        modalValidatePhoto.modal({
            backdrop: 'static',
            keyboard: false
        });

        modalValidatePhoto.find('#photo-viewer').html('Fetching Photo Files...');
        $.ajax({
            type: "GET",
            url: baseUrl + "upload_item_photo/ajax_get_photo_files",
            data: {id_photo: idPhoto},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                modalValidatePhoto.find('#photo-viewer').empty();
                $.each(data, function (index, value) {
                    var ext = value.src.split('.').pop().toLowerCase();
                    var source = value.url;
                    if (ext == 'jpg' || ext == 'png' || ext == 'jpeg') {
                        modalValidatePhoto.find('#photo-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            }).css('margin', '20px auto')
                        );
                    } else {
                        modalValidatePhoto.find('#photo-viewer').append(
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
                            }).html('Source file ' + value.photo))
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });
    $(document).on('click', '.btn-view-photo', function (e) {
        e.preventDefault();

        var idPhoto = $(this).closest('.row-upload-photo').data('id');
        var idItem = $(this).closest('.row-upload-photo').data('id-item');
        var totalFile = $(this).closest('.row-upload-photo').data('total-file');

        var ajaxUrl = "upload_item_photo/ajax_get_photo_files";
        if(totalFile == 0){
            idPhoto = idItem;
            ajaxUrl = "item-compliance/ajax_get_photo_files";
        }
        var modalViewPhoto = $('#modal-view-photo');
        

        modalViewPhoto.modal({
            backdrop: 'static',
            keyboard: false
        });

        modalViewPhoto.find('#photo-viewer').html('Fetching Photo Files...');
        $.ajax({
            type: "GET",
            url: baseUrl + ajaxUrl,
            data: {id_photo: idPhoto},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                modalViewPhoto.find('#photo-viewer').empty();
                $.each(data, function (index, value) {
                    var ext = value.src.split('.').pop().toLowerCase();
                    var source = value.url;
                    if (ext == 'jpg' || ext == 'png' || ext == 'jpeg') {
                        modalViewPhoto.find('#photo-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            }).css('margin', '20px auto')
                        );
                    } else {
                        modalViewPhoto.find('#photo-viewer').append(
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
                            }).html('Source file ' + value.photo))
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

    btnAddItem.on('click', function (e) {
        e.preventDefault();

        changeModalAdd($('#modal-add-item'));
        var idUpload = $(this).data('id-upload');
        var idPerson = $(this).data('id-person');
        var urlItem = $(this).attr('href');
        
        modalAddItem.find('form').attr('action', urlItem);
        modalAddItem.find('input[name=id_upload]').val(idUpload);
        modalAddItem.find('input[name=id_customer]').val(idPerson);
        modalAddItem.find('select[name=item_name]').data('params','owner='+idPerson);

        modalAddItem.modal({
            backdrop: 'static',
            keyboard: false
        });

    });

    const modalCreateItem = $('#modal-create-item');
    $('.btn-create-item').on('click', function (e) {
        e.preventDefault();
        var customerId = $(this).closest('form').find('#id_customer').val();
        console.log(customerId);

        modalCreateItem.find('#item_name').val('');
        modalCreateItem.find('#no_hs').val('');
        modalCreateItem.find('#unit').val('');
        modalCreateItem.find('#customer').val(customerId);
        modalCreateItem.find('#description').val('');
        modalCreateItem.modal({
            backdrop: 'static',
            keyboard: false
        });
        modalCreateItem.find('.alert').hide();
    });

    modalCreateItem.find('#btn-submit').on('click', function (e) {
        e.preventDefault();

        const buttonSubmit = $(this);
        buttonSubmit.attr('disabled', true);
        
        let customerId = modalAddItem.find('input[name=id_customer]').val();

        $.ajax({
            type: 'POST',
            url: baseUrl + "item_compliance/ajax_save",
            data: modalCreateItem.find('form').serialize(),
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                modalCreateItem.find('.alert').show();
                if (data.status === 'success') {
                    modalCreateItem.find('.alert').addClass('alert-success').removeClass('alert-danger');
                    modalCreateItem.find('.messages').html(data.message);
                    setTimeout(function () {
                        modalCreateItem.modal('hide');
                        buttonSubmit.attr('disabled', false);

                        if (modalAddItem.length && customerId == data.itemCompliance.id_customer) {
                            modalAddItem.find('#item_name').data('data', data.itemCompliance);
                            modalAddItem.find('#item_name').html(
                                $('<option>', {value: data.itemCompliance.id}).text(`${data.itemCompliance.item_name}`)
                            ).trigger('change');
                        }
                    }, 500);
                } else {
                    modalCreateItem.find('.alert').addClass('alert-danger').removeClass('alert-success');
                    modalCreateItem.find('.messages').html(data.message);
                    buttonSubmit.attr('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                buttonSubmit.attr('disabled', false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

    formUploadPhoto.on('submit', function () {

        //check upload photos
        const requiredPhotos = formUploadPhoto.find('.required-photo');

        let hasPhoto = true;
        if (requiredPhotos.length) {
            requiredPhotos.each(function (index, element) {
                if ($(element).find('.upload-input-wrapper').children().length === 0) {
                    hasPhoto = false;
                }
            });
        }

        if (!hasPhoto) {
            alert('Upload all required document');
            return false;
        }

        const buttonSubmit = formUploadPhoto.find('button[type=submit]');
        $('span#loader').css('display', 'block');
        buttonSubmit.prop('disabled', true).html('Uploading...');

        return true;
    });

    $('.status-cek').on("click", function () {

        //check status
        const status = $(this).attr("value");
        let item = modalValidatePhoto.find('#item_name').val();
        console.log(item);
        if(status === '1' && item === ''){
            alert('Item required');
            return false;
        }
        const buttonSubmit = modalValidatePhoto.find('button[type=submit]');
        $('span#loader').css('display', 'block');
        buttonSubmit.prop('disabled', true).html('Uploading...');
        if(status === '1'){
            modalValidatePhoto.find('#status').val('1');
            $('#form-validate-photo').trigger('submit');
        }else{
            modalValidatePhoto.find('#status').val('-1');
            $('#form-validate-photo').trigger('submit');
        }        
        return true;
    });

    $('#table-photo').on('click', '.btn-delete-photo', function (e) {
        e.preventDefault();

        var idPhoto = $(this).closest('.row-upload-photo').data('id');
        var labelPhoto = $(this).closest('.row-upload-photo').data('label');
        var idUpload = $(this).closest('.row-upload-photo').data('id-upload');
        var urlPhoto = $(this).attr('href');

        var modalDeletePhoto = $('#modal-delete-photo');
        modalDeletePhoto.find('form').attr('action', urlPhoto);
        modalDeletePhoto.find('input[name=id]').val(idPhoto);
        modalDeletePhoto.find('input[name=id_upload]').val(idUpload);
        modalDeletePhoto.find('#photo-title').text(labelPhoto);

        modalDeletePhoto.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#photo-wrapper').on('click', '.btn-delete-photo', function (e) {
        e.preventDefault();

        var idPhoto = $(this).data('id');
        var labelPhoto = $(this).data('label');
        var idUpload = $(this).data('id-upload');
        var urlPhoto = $(this).attr('href');

        var modalDeletePhoto = $('#modal-delete-photo');
        modalDeletePhoto.find('form').attr('action', urlPhoto);
        modalDeletePhoto.find('input[name=id]').val(idPhoto);
        modalDeletePhoto.find('input[name=id_upload]').val(idUpload);
        modalDeletePhoto.find('#photo-title').text(labelPhoto);

        modalDeletePhoto.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});