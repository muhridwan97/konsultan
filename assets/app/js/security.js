$(function () {

    var scannerStorageKey = 'tci-security-scanner';
    var localScannerData = localStorage.getItem(scannerStorageKey);
    var scanScannerHistory = localScannerData == null ? [] : JSON.parse(localScannerData);

    function checkDataExist(data, key) {
        var isExist = false;
        for (var i = 0; i < data.length; i++) {
            if (key == data[i]) {
                isExist = true;
                break;
            }
        }
        return isExist;
    }

    $('#btn-scan-code').on('click', function () {
        var inputCode = $('#code').val();
        if (!checkDataExist(scanScannerHistory, inputCode)) {
            if (scanScannerHistory.length >= 10) {
                scanScannerHistory.splice(0, 1);
            }
            scanScannerHistory.push(inputCode);
            localStorage.setItem(scannerStorageKey, JSON.stringify(scanScannerHistory));
        }
    });

    var codeHistoryWrapper = $('#code-history');

    function printHistoryList() {
        codeHistoryWrapper.find('.list-history').empty();
        var baseUrl = '//' + location.host + location.pathname;
        for (var j = scanScannerHistory.length - 1; j >= 0; j--) {
            codeHistoryWrapper
                .append($('<a>', {
                    'href': baseUrl + '/check?code=' + scanScannerHistory[j],
                    'class': 'list-group-item list-history'
                }).text(scanScannerHistory[j]));
        }
    }

    printHistoryList();


    const securityWrapper = $('.security-wrapper');
    
    $(".btn-check-in").click();
    $(document).on('click', '.btn-check-in', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-in').data('id');
        var label = $(this).closest('.btn-check-in').data('label');
        var driver = $(this).closest('.btn-check-in').data('driver');
        var noPolice = $(this).closest('.btn-check-in').data('no-police');
        var expedition = $(this).closest('.btn-check-in').data('expedition');
        var urlCheck = $(this).attr('href');

        var modalCheckIn = $('#modal-security-check-in');
        modalCheckIn.find('form').attr('action', urlCheck);
        modalCheckIn.find('input[name=id]').val(id);
        modalCheckIn.find('input[name=driver]').val(driver);
        modalCheckIn.find('input[name=no_police]').val(noPolice);
        modalCheckIn.find('input[name=expedition]').val(expedition);
        modalCheckIn.find('#check-in-title').text(label);

        modalCheckIn.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(".btn-check-out").click();
    $(document).on('click', '.btn-check-out', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-out').data('id');
        var label = $(this).closest('.btn-check-out').data('label');
        var urlCheck = $(this).attr('href');

        var modalCheckOut = $('#modal-security-check-out');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.find('input[name=id]').val(id);
        modalCheckOut.find('#check-out-title').text(label);

        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(".btn-check-out-now").click();
    var modalCheckOutNow = $('#modal-security-check-out-now');
    $(document).on('click', '.btn-check-out-now', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlCheck = $(this).attr('href');

        modalCheckOutNow.find('form').attr('action', urlCheck);
        modalCheckOutNow.find('input[name=id]').val(id);
        modalCheckOutNow.find('input[name=label]').val(label);
        modalCheckOutNow.find('#check-out-title').text(label);

        modalCheckOutNow.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    // handling chassis type
    const selectChassisHandlingType = modalCheckOutNow.find('#chassis_handling_type');
    const selectChassis = modalCheckOutNow.find('#chassis');
    selectChassisHandlingType.on('change', function () {
        if ($(this).val() === 'pickup-chassis') {
            selectChassis.prop('disabled', false);
        } else {
            selectChassis.val('').prop('disabled', true);
        }
        selectChassis.select2();
    });

    $(".btn-check-tep").click();
    $(document).on('click', '.btn-check-tep', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-tep').data('id');
        var label = $(this).closest('.btn-check-tep').data('label');
        var urlCheck = $(this).attr('href');

        var modalCheckTEP = $('#modal-tep-check');
        modalCheckTEP.find('form').attr('action', urlCheck);
        modalCheckTEP.find('#check-in-title').text(label);

        modalCheckTEP.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(".btn-check-tep-out").click();
    $(document).on('click', '.btn-check-tep-out', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-tep-out').data('id');
        var label = $(this).closest('.btn-check-tep-out').data('label');
        var urlCheck = $(this).attr('href');

        var modalCheckTEP = $('#modal-tep-check-out');
        modalCheckTEP.find('form').attr('action', urlCheck);
        modalCheckTEP.find('#check-out-title').text(label);

        modalCheckTEP.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '.btn-check-heep', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-heep').data('id');
        var label = $(this).closest('.btn-check-heep').data('label');
        var urlCheck = $(this).attr('href');
        var allowBrowse = $(this).closest('.btn-check-heep').data('browse-photo');
        if (allowBrowse === 'LOCK') {
            $('#btn-browse-photo').hide();
        } else {
            $('#btn-browse-photo').show();
        }
        var modalCheckHEEP = $('#modal-heep-check');
        modalCheckHEEP.find('form').attr('action', urlCheck);
        modalCheckHEEP.find('#check-in-title').text(label);

        modalCheckHEEP.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    $(document).on('click', '.btn-check-out-heep', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-check-out-heep').data('id');
        var label = $(this).closest('.btn-check-out-heep').data('label');
        var urlCheck = $(this).attr('href');
        var allowBrowse = $(this).closest('.btn-check-heep').data('browse-photo');
        if (allowBrowse === 'LOCK') {
            $('#btn-browse-photo').hide();
        } else {
            $('#btn-browse-photo').show();
        }
        var modalCheckHEEP = $('#modal-heep-check-out');
        modalCheckHEEP.find('form').attr('action', urlCheck);
        modalCheckHEEP.find('#check-out-title').text(label);

        modalCheckHEEP.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '.btn-container', function (e) {
        e.preventDefault();

        var id = $(this).closest('.btn-container').data('id');
        var idSafeConduct = $(this).closest('.btn-container').data('id-safe-conduct');
        var label = $(this).closest('.btn-container').data('label');
        var urlCheck = $(this).attr('href');
        
        var allowBrowse = $(this).closest('.btn-container').data('browse-photo');
        if (allowBrowse === 'LOCK') {
            $('#btn-browse-photo').hide();
        } else {
            $('#btn-browse-photo').show();
        }

        var modalCheckOut = $('#modal-checklist-container');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.find('#id_container').val(id);
        modalCheckOut.find('#id_safe_conduct').val(idSafeConduct);
        modalCheckOut.find('#no_container').val(label);
        modalCheckOut.find('#check-title').text(label);

        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '.btn-goods', function (e) {
        e.preventDefault();

        var id_goods = null;
        var label = $(this).closest('.btn-goods').data('label');
        var urlCheck = $(this).attr('href');

        var allowBrowse = $(this).closest('.btn-goods').data('browse-photo');
        if (allowBrowse === 'LOCK') {
            $('#btn-browse-photo').hide();
        } else {
            $('#btn-browse-photo').show();
        }

        var modalCheckOut = $('#modal-checklist-goods');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.find('#id_goods').val(id_goods);
        modalCheckOut.find('#no_container').val(label);
        modalCheckOut.find('#check-title').text(label);

        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });  

    $("#form-safe-conduct").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        var eseal = $("#form-safe-conduct").find("#eseal");

        $.ajax({
           type: "POST",
           url: baseUrl + 'safe_conduct/ajax_get_containers_by_input',
           data:  form.serialize(), // serializes the form's elements.
           success: function(data)
            {
                if(data.containers != undefined){
                   $('#form-safe-conduct').unbind().submit();
                   const buttonSubmit = $('#form-safe-conduct').find('button[type=submit]');
                    $('span#loader').css('display', 'block');
                    buttonSubmit.prop('disabled', true).html('Updating...');
                }else{
                    var label = $(this).closest('.btn-safe-conduct-notif').data('label');
                    var modalNotif = $('#modal-notification');

                    modalNotif.modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    modalNotif.find('#btn-yes').on('click', function (e) {
                        $('#form-safe-conduct').unbind().submit();
                        const buttonSubmit = $('#form-safe-conduct').find('button[type=submit]');
                        $('span#loader').css('display', 'block');
                        buttonSubmit.prop('disabled', true).html('Updating...');
                    });

                    modalNotif.find('#btn-no').on('click', function (e) {
                        const buttonSubmit = $('#form-safe-conduct').find('button[type=submit]');
                        buttonSubmit.prop('disabled', false);
                    });
                }
            }
        });

    });

    $("#form-tep").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        var eseal = $("#form-tep").find("#eseal");

        $.ajax({
           type: "POST",
           url: baseUrl + 'safe_conduct/ajax_get_containers_by_input',
           data:  form.serialize(), // serializes the form's elements.
           success: function(data)
            {
                if(data.containers != undefined){
                   $('#form-tep').unbind().submit();
                    const buttonSubmit = $('#form-tep').find('button[type=submit]');
                    $('span#loader').css('display', 'block');
                    buttonSubmit.prop('disabled', true).html('Updating...');
                }else{
                    var label = $(this).closest('.btn-safe-conduct-notif').data('label');
                    var modalNotif = $('#modal-notification');

                    modalNotif.modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    modalNotif.find('#btn-yes').on('click', function (e) {
                        $('#form-tep').unbind().submit();
                        const buttonSubmit = $('#form-tep').find('button[type=submit]');
                        $('span#loader').css('display', 'block');
                        buttonSubmit.prop('disabled', true).html('Updating...');
                    });

                    modalNotif.find('#btn-no').on('click', function (e) {
                        const buttonSubmit = $('#form-tep').find('button[type=submit]');
                        buttonSubmit.prop('disabled', false);
                    });
                }
            }
        });

    });

    $("#form-checklist-container").on('click', '.btn-save-container', function (e) {
        var checkboxes = $('.checkboxes');
        var reason = $('.reason');

        for (var i = 0; i < checkboxes.length; i++) {
            if ($(checkboxes[i]).is(":checked")) {
                $("#form-checklist-container").find(reason[i]).attr('required', false);
            } else {
                $("#form-checklist-container").find(reason[i]).attr('required', true);
            }
        }

        // var upload = $("#form-checklist-container").find('#attachment').val();
        // var seal = $("#form-checklist-container").find('#attachment_seal').val();
        // if(upload == ""){
        //     alert("Attachment is required");
        //     return false;
        // }
        // if (seal == "") {
        //     alert("Attachment Seal is required");
        //     return false;
        // }
    });

    // $("#form-heep-check").on('click', '.btn-check', function (e) {
    //     var uploadHeep = $("#form-heep-check").find('#attachment').val();
    //     if (uploadHeep == "") {
    //         alert("Attachment is required");
    //         return false;
    //     }
    // });

    $("#form-checklist-goods").on('click', '.btn-save-goods', function (e) {
        var checkboxes = $('.checkboxes-goods');
        var reason = $('.reason-goods');

        for (var i = 0; i < checkboxes.length; i++) {
            if ($(checkboxes[i]).is(":checked")) {
                $("#form-checklist-goods").find(reason[i]).attr('required', false);
            } else {
                $("#form-checklist-goods").find(reason[i]).attr('required', true);
            }
        }

        // var upload = $("#form-checklist-goods").find('#attachment').val();
        // if(upload == ""){
        //     alert("Attachment is required");
        //     return false;
        // }
    });

    let fileFromCapture = null;
    initUploadPhoto($('.upload-photo'));

    function initUploadPhoto(input) {
        var btnSubmit = $(input).closest('form').find(':submit');
        input.fileupload({
            url: baseUrl + 'upload_document_file/upload_s3',
            dataType: 'json',
            add: function (e, data) {
                btnSubmit.attr('disabled', true);
                if (fileFromCapture) {
                    data.files = [fileFromCapture];
                }
                data.submit();
            },
            done: function (e, data) {
                var inputFileParent = $(this).closest('.form-group');
                inputFileParent.find('.text-danger').remove();
                // btnSubmit.find(':submit').attr('disabled', true);
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
                        inputFileParent.find('.file-upload-info').val(file.data.client_name);
                        btnSubmit.attr('disabled', false);
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
            },
            progressall: function (e, data) {
                // console.log(data);
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                    'width',
                    progress + '%'
                ).text(progress + '%');
                // if (progress==100) {
                //     btnSubmit.attr('disabled', false);
                // }
            },
            fail: function (e, data) {
                alert(data.textStatus);
            }
        });
    }
    
    function checkButtonUpload(wrapper) {
        if (wrapper.find('.upload-input-wrapper').children().length) {
            console.log('check');
            wrapper.find('.button-file').attr('disabled', true);
        } else {
            console.log('check2');
            wrapper.find('.button-file').attr('disabled', false);
            wrapper.find('.progress-bar')
                .removeClass('progress-bar-danger')
                .addClass('progress-bar-success')
                .css(
                    'width', '0%'
                );
        }
    }

    $(document).on('click', '.btn-photo-picker', function (e) {
        e.preventDefault();
        const inputFile = $(this).closest('.form-group').find('[type=file]');
        fileFromCapture = null;
        currentUploadFile = null;
        openModalTakePhoto(function(blob, base64, modal) {
            const file = new File([blob], "image.jpg", {lastModified: new Date().getTime()});
            fileFromCapture = file;
            // force initialize without open the dialog file picker
            initUploadPhoto(inputFile);
            //inputFile.trigger('change');
            inputFile.fileupload('add', {files: [file]}); // manually passing File API rather from input
            //inputFile.trigger('fileuploadadd');
            $(modal).modal('hide');
        }, function(modal) {
            inputFile.click();
            $(modal).modal('hide');
        });
    });


    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    // if(!isMobile.any()) {
    //     $('body').html('<div class="mobile-only-info" style="background: rgba(255, 255, 255, .9); padding: 10px; position: fixed; z-index: 9999; width: 100%; height: 100%; top: 0; left: 0; text-align: center; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold"><div>THIS PAGE MUST BE ACCESSED BY THE MOBILE DEVICE</div></div>');
    // }
});