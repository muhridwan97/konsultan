$(function () {
    const table = $('#table-upload.table-ajax');
    const controlTemplate = $('#control-upload-template').html();

    const formUpload = $('#form-upload');
    const formResponse = $('#form-response');
    const formEditUpload = $('#form-edit-upload-document');
    const selectUploadIn = formUpload.find('#upload_in');
    const selectCustomer = formUpload.find('#customer');
    const selectType = formUpload.find('#type');
    const radioHold = formUpload.find('[name=is_hold]');
    const formUploadWrapper = $('#form-upload-wrapper');
    const uploadInWrapper = $('#upload-in-wrapper');
    const uploadPhotoWrapper = $('#upload-photo-wrapper');
    const selectContainerType = formResponse.find('#container_type');
    const lclTypeWrapper = formResponse.find('#lcl-type');
    const fclTypeWrapper = formResponse.find('#fcl-type');
    const buttonAddFcl = formResponse.find('#btn-add-fcl');
    const containerTypeWrapper = formResponse.find('.container_type');

    formResponse.find(".document_subtype").hide();
    formResponse.find("#document_subtype").attr("required", false);
    formResponse.find(".total_item").hide();
    formResponse.find("#total_item").attr("required", false);
    containerTypeWrapper.hide();
    selectContainerType.attr("required", false);
    let inputParty = formResponse.find('#party');
    let inputShape = formResponse.find('#shape');
    let inputLclParty = formResponse.find('#lcl_party');

    table.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search upload"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'upload/ajax_get_data?' + window.location.search.slice(1),
        order: [[0, "desc"]],
        pageLength: 25,
        columns: [
            {data: 'no', class: 'responsive-hide'},
            {data: 'booking_type', class: 'responsive-title'},
            {data: 'no_booking', class: 'no-wrap'},
            {data: 'no_upload', class: 'no-wrap'},
            {data: 'description'},
            {data: 'name'},
            {data: 'is_hold'},
            {data: 'status'},
            {data: 'is_valid_all'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-booking'],
            render: function (data, type, full) {
                let label = 'Validate First!';
                if (full.is_valid == 1) {
                    if (full.id_booking) {
                        label = `<a href="${baseUrl}booking/view/${full.id_booking}">${full.no_booking}</a>`;
                    } else {
                        label = `<a href="${baseUrl}booking/create/${full.id}">Create Booking</a>`;
                    }
                }
                return label;
            }
        }, {
            targets: ['type-status-hold'],
            render: function (data, type, full) {
                return `<span class='label label-${data == '1' ? 'danger' : 'success'}'>${data == '1' ? 'Yes' : 'No'}</span>`;
            }
        }, {
            targets: ['type-status-upload'],
            render: function (data) {
                let statusLabel = 'default';
                if (data === 'ON PROCESS') {
                    statusLabel = 'info';
                } else if (data === 'HOLD' || data === 'REJECTED') {
                    statusLabel = 'danger';
                } else if (data === 'BILLING') {
                    statusLabel = 'primary';
                } else if (data === 'PAID') {
                    statusLabel = 'success';
                } else if (data === 'AP') {
                    statusLabel = 'warning';
                } else if (data === 'CLEARANCE') {
                    statusLabel = 'success';
                }
                return `<span class='label label-${statusLabel}'>${data }</span>`;
            }
        }, {
            targets: ['type-status-docs'],
            render: function (data, type, full) {
                return `<span class='label label-${data == 1 ? 'success' : 'danger'}'>${data == 1 ? 'Yes' : 'No'} (${full.total_valid_document}/${full.total_document})</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_upload}}/g, full.no_upload)
                    .replace(/{{description}}/g, full.description)
                    .replace(/{{validate_upload_disable}}/g, (full.is_valid_all == 1 && full.total_valid_photo > 0 && full.is_valid_all_photo == 1) || (full.is_valid_all == 1 && full.category == 'OUTBOUND') || (full.is_valid_all == 1 && full.branch_type != 'PLB') || (full.is_valid_all == 1 && (full.id_parent == '12954' || full.id_person == '12954')) || (full.is_valid_all == 1 && (moment(full.created_at) < moment('2021-09-01'))) ? '' : 'disabled')//(full.is_valid_all == 1 && (moment(full.created_at).format('D MMMM YYYY')>= moment('2021-09-01').format('D MMMM YYYY')) && full.total_valid_photo > 0 && full.is_valid_all_photo == 1) || (full.is_valid_all == 1 && full.category == 'OUTBOUND')
                    .replace(/{{validate_upload_tooltip}}/g, full.is_valid_all == 1 ? '' : 'tooltip')
                    .replace(/{{validate_upload_title}}/g, full.is_valid_all == 1 ? '' : 'Wait until this upload are validated')
                    .replace(/{{edit_upload_disable}}/g, full.id_booking == null && full.category == 'OUTBOUND' ? '' : 'hidden');

                control = $.parseHTML(control);

                if (['NEW', 'ON PROCESS'].includes(full.status)) {
                    if (full.is_hold == 0) {
                        $(control).find('.action-release').remove();
                    } else {
                        $(control).find('.action-hold').remove();
                    }
                } else {
                    if (full.is_hold == 0) {
                        $(control).find('.action-release').remove();
                    }
                    $(control).find('.action-hold').remove();
                }

                if (full.category === 'OUTBOUND' || full.branch_type === 'PLB') {
                    $(control).find('.action-validate').remove();
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' || data == null ? '-' : data;
            }
        }],
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip({container: 'body'});
        }
    });

    radioHold.on('ifChanged', function() {
        if ($(this).val() === '1') {
            $('#field-hold-description').show();
            $('#hold_description').prop('required', true);
        } else {
            $('#field-hold-description').hide();
            $('#hold_description').prop('required', false);
        }
    });

    table.on('click', '.btn-validate-upload', function (e) {
        e.preventDefault();

        if ($(this).parent().hasClass('disabled')) {
            return;
        }

        var idUpload = $(this).closest('.row-upload').data('id');
        var labelUpload = $(this).closest('.row-upload').data('label');
        var urlUpload = $(this).attr('href');
        var urlResponse = $(this).data('url-view');

        var modalValidateUpload = $('#modal-validate-upload');
        modalValidateUpload.find('form').attr('action', urlUpload);
        modalValidateUpload.find('#link-view-upload').attr('href', urlResponse);
        modalValidateUpload.find('input[name=id]').val(idUpload);
        modalValidateUpload.find('#upload-title').text(labelUpload);

        modalValidateUpload.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    selectCustomer.on('change', function () {
        selectType.empty().append($('<option>'));
        $.ajax({
            url: baseUrl + 'booking_type/ajax_get_customer_booking_types',
            type: 'GET',
            data: {id_customer: selectCustomer.val()},
            success: function (data) {
                if (data) {
                    data.forEach(row => {
                        selectType.append(
                            $('<option>', {
                                value: row.id,
                                'data-type': row.type,
                                'data-category': row.category
                            })
                                .text(`${row.booking_type} (${row.category} - ${row.type})`)
                        );
                    });
                }
            }
        });
    });

    selectType.on('change', function () {
        if (selectType.find('option:selected').data('category') === "OUTBOUND") {
            if (selectType.find('option:selected').data('type') === "EXPORT") {
                selectUploadIn.prop({
                    multiple: true,
                    name: 'upload_in[]'
                });
            } else {
                selectUploadIn.prop({
                    multiple: false,
                    name: 'upload_in'
                });
            }

            uploadInWrapper.show();
            uploadPhotoWrapper.hide();
            selectUploadIn.attr("required", true);
            selectUploadIn.prop('disabled', true).empty().append($('<option>'));
            $.ajax({
                type: 'GET',
                url: baseUrl + 'upload/ajax_get_uploads_in_by_customer',
                data: {id_customer: selectCustomer.val()},
                success: function (data) {
                    selectUploadIn.prop("disabled", false);
                    data.forEach(row => {
                        selectUploadIn.append(
                            $('<option>', {value: row.id})
                                .text(`${row.no_upload} (${row.description})`)
                        );
                    });
                    uploadInWrapper.find('select').select2();
                }
            });
        } else{
            uploadInWrapper.hide();
            uploadPhotoWrapper.show();
            selectUploadIn.attr("required", false).val('').trigger('change');
        }

        $.ajax({
            url: baseUrl + 'document_type/ajax_get_booking_document_type',
            type: 'GET',
            data: {
                id_booking_type: $(this).val(),
                type: 'form'
            },
            success: function (data) {
                formUploadWrapper.html(data);
                $('.datepicker').datepicker({
                    autoclose: true,
                    format: 'dd MM yyyy',
                });
                uploadDocument();
            }
        });
    });

    
    var document_subtype_edit = formEditUpload.find('#document_subtype').val();
    var document_expired_date_edit = formEditUpload.find('#expired_date').val();
    var document_freetime_date_edit = formEditUpload.find('#freetime_date').val();

     // for edit do from form edit/replace document
    if(formEditUpload.find("#document_type").val() == "DO"){
        $('.document_subtype').show();
        $('#document_subtype').val(document_subtype_edit).trigger("change");
        $('#document_subtype').attr("required", true);

        if(document_subtype_edit == "SOC"){
            $('.expired_date').show();
            $('#expired_date').val(document_expired_date_edit).trigger("change");
            $('#expired_date').attr("required", true);

            $('.freetime_date').hide();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", false);
        }else if(document_subtype_edit == "COC"){
            $('.expired_date').show();
            $('#expired_date').val(document_expired_date_edit).trigger("change");
            $('#expired_date').attr("required", true);

            $('.freetime_date').show();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", true);
        }else if(document_subtype_edit == "LCL"){
            $('.expired_date').hide();
            $('#expired_date').val(document_expired_date_edit).trigger("change");
            $('#expired_date').attr("required", false);

            $('.freetime_date').hide();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", false);
        }else{
            $('.expired_date').hide();
            $('#expired_date').val(document_expired_date_edit).trigger("change");
            $('#expired_date').attr("required", false);

            $('.freetime_date').hide();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", false);
        }
    }

    var myarr = ["BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft"];
    if(myarr.indexOf(formEditUpload.find("#document_type").val()) > -1){
        $('.total_item').show();
        $('#total_item').attr("required", true);
        let category = $('#category').val();
        if (category == 'INBOUND') {
            containerTypeWrapper.show();
            selectContainerType.attr("required", true);
        }
    }

     // for edit do from edit document
    formEditUpload.find('#document_subtype').on('change', function () {
        var document_subtype = $(this).val();

        $('.expired_date').show();
        $('#expired_date').val(document_expired_date_edit).trigger("change");
        $('#expired_date').attr("required", true);

        if(document_subtype == "SOC"){
            $('.freetime_date').hide();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", false);
        }
        if(document_subtype == "COC"){
            $('.freetime_date').show();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", true);
        }

        if(document_subtype == "LCL"){
            $('.freetime_date').hide();
            $('#freetime_date').val(document_freetime_date_edit).trigger("change");
            $('#freetime_date').attr("required", false);

            $('.expired_date').hide();
            $('#expired_date').val(document_expired_date_edit).trigger("change");
            $('#expired_date').attr("required", false);
        }
    });           

     // for create do from response
    formResponse.find('#document_subtype').on('change', function () {
        var document_subtype = $(this).val();

        $('.expired_date').show();
        $('#expired_date').val("").trigger("change");
        $('#expired_date').attr("required", true);

        if(document_subtype == "SOC"){
            $('.freetime_date').hide();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", false);
        }
        if(document_subtype == "COC"){
            $('.freetime_date').show();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", true);
        }

        if(document_subtype == "LCL"){
            $('.freetime_date').hide();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", false);

            $('.expired_date').hide();
            $('#expired_date').val("").trigger("change");
            $('#expired_date').attr("required", false);
        }
    });

     // for create do from response
    $('#document_type').on('change', function () {
        $.ajax({
            url: baseUrl + 'document_type/ajax_get_document_type_by_id',
            type: 'GET',
            data: {
                id: $(this).val(),
                type: 'form'
            },
            success: function (data) {
                if(data != null){
                    $('.freetime_date').hide();
                    $('#freetime_date').attr("required", false);
                    if(data.document_type == "DO"){
                        $('.document_subtype').show();
                        $('#document_subtype').val("").trigger("change");
                        $('#document_subtype').attr("required", true);

                        $('.expired_date').hide();
                        $('#expired_date').attr("required", false);
                    }else{
                        $('.document_subtype').hide();
                        $('#document_subtype').val("").trigger("change");
                        $('#document_subtype').attr("required", false);
                        $('#total_item').val("");
                        $('.expired_date').hide();
                        $('#expired_date').attr("required", false);
                        selectContainerType.val("").trigger("change");

                        if(myarr.indexOf(data.document_type) > -1){
                            $('.total_item').show();
                            $('#total_item').attr("required", true);
                            let category = $('#category').val();
                            if (category == 'INBOUND') {
                                containerTypeWrapper.show();
                                selectContainerType.attr("required", true);                                
                            }
                        }else{
                            $('.total_item').hide();
                            $('#total_item').attr("required", false);
                            containerTypeWrapper.hide();
                            selectContainerType.attr("required", false);
                            fclTypeWrapper.hide();
                            lclTypeWrapper.hide();
                            inputParty.attr("required", false);
                            inputShape.attr("required", false);
                            inputLclParty.attr("required", false);
                        }
                    }
                }
            }
        });
    });

    // for create do from delivery order feature
    if (formResponse.length) {
        $.ajax({
            url: baseUrl + 'document_type/ajax_get_document_type_by_id',
            type: 'GET',
            data: {
                id: formResponse.find("#document_type").val(),
                type: 'form'
            },
            success: function (data) {
                if(data != null){
                    $('.freetime_date').hide();
                    $('#freetime_date').attr("required", false);
                    if(data.document_type == "DO"){
                        $('.document_subtype').show();
                        $('#document_subtype').attr("required", true);
                    }else{
                        $('.document_subtype').hide();
                        $('#document_subtype').attr("required", false);

                        if(myarr.indexOf(data.document_type) > -1){
                            $('.total_item').show();
                            $('#total_item').attr("required", true);
                            let category = $('#category').val();
                            if (category == 'INBOUND') {
                                containerTypeWrapper.show();
                                selectContainerType.attr("required", true);
                            }
                        }else{
                            $('.total_item').hide();
                            $('#total_item').attr("required", false);
                            containerTypeWrapper.hide();
                            selectContainerType.attr("required", false);
                            fclTypeWrapper.hide();
                            lclTypeWrapper.hide();
                            inputParty.attr("required", false);
                            inputShape.attr("required", false);
                            inputLclParty.attr("required", false);
                        }
                    }
                }
            }
        });
    }

    uploadDocument();

    function uploadDocument() {
        $('.upload-document').fileupload({
            url: baseUrl + 'upload_document_file/upload',
            dataType: 'json',
            add: function(e, data) {
                $(this).addClass('uploading', true);
                checkDisableSubmitButton(this);

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
                $(this).removeClass('uploading', false);
                checkDisableSubmitButton(this);

                checkButtonUpload(inputFileParent);
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                    'width',
                    progress + '%'
                ).text(progress + '%');
            },
            fail: function (e, data) {
                $(this).removeClass('uploading', false);
                checkDisableSubmitButton(this);

                alert(data.textStatus);
            }
        });
    }

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

    formUploadWrapper.on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        $.ajax({
            url: baseUrl + 'upload_document_file/delete_temp_upload',
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

    formUpload.on('submit', function () {

        //check upload document
        const requiredDocuments = formUpload.find('.required-document');

        let hasDocument = true;
        if (requiredDocuments.length) {
            requiredDocuments.each(function (index, element) {
                if ($(element).find('.upload-input-wrapper').children().length === 0) {
                    hasDocument = false;
                }
            });
        }

        if (!hasDocument) {
            alert('Upload all required document');
            return false;
        }

        //check subtype DO
        const bookingRequired = formUpload.find('.do-required');

        let hasDocumentDORequired = true;
        var documentNumber = "";
        var documentDate = "";
        var documentSubtype = "";
        if (bookingRequired.length) {
            bookingRequired.each(function (i, e) {
                if ($(e).find('.upload-input-wrapper').children().length !== 0) {
                    hasDocumentDORequired = false;
                }

                if ($(e).find(".doc_no").val() !== "") {
                    documentNumber = $(e).find(".doc_no").val();
                    hasDocumentDORequired = false;
                }

                if ($(e).find(".doc_date").val() !== "") {
                    documentDate = $(e).find(".doc_date").val();
                    hasDocumentDORequired = false;
                }

                if ($(e).find("#document_subtype").val() !== "") {
                    documentSubtype = $(e).find("#document_subtype").val();
                    hasDocumentDORequired = false;
                }
            });
        }

        if ( (!hasDocumentDORequired && documentNumber !== "" && documentDate === "")) {
            alert('For the document number '+documentNumber+', Please select your document date !');
            return false;
        }

        if ( (!hasDocumentDORequired && documentNumber !== "" && documentSubtype === "")) {
            alert('Document subtype is required');
            return false;
        }

        //check document date
        const formDocumentOptional = formUpload.find('.optional-document');

        let hasDocumentOptional = true;
        var documentNumb = "";
        var docDate = "";
        if(formDocumentOptional.length > 0){
            formDocumentOptional.each(function(idx, elm){
                if ($(elm).find(".doc_no").val() !== "" && $(elm).find(".doc_date").val() === ""){
                    documentNumb = $(elm).find(".doc_no").val();
                    hasDocumentOptional = false;
                    docDate = $(elm).find(".doc_date").val();
                }
            });
        }

        if ( (!hasDocumentOptional && documentNumb !== "" && docDate === "")) {
            alert('For the document number '+documentNumb+', Please select your document date !');
            return false;
        }

        const buttonSubmit = formUpload.find('button[type=submit]');
        $('span#loader').css('display', 'block');
        buttonSubmit.prop('disabled', true).html('Uploading...');

        return true;
    });

    selectContainerType.on('change', function () {
        let container_type = $(this).val();
        inputParty.val('');
        inputShape.val('');
        inputLclParty.val('');

        if (container_type === "FCL") {
            lclTypeWrapper.hide();
            fclTypeWrapper.show();
            inputParty.attr("required", true);
            inputShape.attr("required", true);
            inputLclParty.attr("required", false);
        } else if(container_type === "LCL"){
            lclTypeWrapper.show();
            fclTypeWrapper.hide();
            fclTypeMultiWrapper.empty();
            inputParty.attr("required", false);
            inputShape.attr("required", false);
            inputLclParty.attr("required", true);
        }
    });

    var fclTemplate = $('#row-fcl-template').html();
    var fclTypeMultiWrapper = formResponse.find('#fcl-type-multi');
    buttonAddFcl.on('click', function (e) {
        e.preventDefault();

        if (getTotalFcl() == 0) {
            fclTypeMultiWrapper.empty();
        }
        fclTypeMultiWrapper.append(fclTemplate);
        reorderFcl();
    });

    function getTotalFcl() {
        return parseInt(fclTypeMultiWrapper.find('div.row-fcl').length);
    }   
    
    function reorderFcl() {
        fclTypeMultiWrapper.find('div.row-Fcl').each(function (index) {
            $(this).find('label').first().html("Fcl " + (index + 1));
        });
        $('.select2').select2();
    }

    fclTypeMultiWrapper.on('click', '.btn-remove-fcl', function (e) {
        e.preventDefault();

        $(this).closest('div.row-fcl').remove();
        reorderFcl();
    });
});


