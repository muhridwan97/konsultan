$(function () {
    var table = $('#table-work-order-document.table-ajax');
    var controlTemplate = $('#control-work-order-document-template').html();
    table.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search document"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'work-order-document/ajax_get_data',
        order: [[0, "desc"]],
        columns: [
            {data: 'no', class: 'responsive-hide'},
            {data: 'date', class: 'responsive-title'},
            {data: 'total_files'},
            {data: 'status'},
            {data: 'status_job_validate'},
            {data: 'status_job_validate'},
            {data: 'validator_name'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) === '' || data == null ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: ['type-status'],
            render: function (data, type, full) {
                if (full.total_files <= 0) {
                    data = 'EMPTY';
                } else if (!data) {
                    data = 'PENDING'
                }
                var statuses = {
                    'EMPTY': 'warning',
                    'PENDING': 'default',
                    'APPROVED': 'success',
                    'REJECTED': 'danger',
                };
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-status-job'],
            render: function (data, type, full) {
                var statuses = {
                    'VALIDATED': 'primary',
                    'UNVALIDATED': 'warning',
                };
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-job-state'],
            render: function (data, type, full) {
                return full.total_validated + ' / ' + full.total_job + ' validated';
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{date}}/g, full.date);

                control = $.parseHTML(control);
                if (full.status === 'APPROVED') {
                    $(control).find('.edit').remove();
                }
                if (full.status !== 'PENDING' && !(!full.status && full.total_files > 0)) {
                    $(control).find('.btn-validate').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) === '' || data == null ? '-' : data;
            }
        }]
    });

    var formWorkOrderDocument = $('#form-work-order-document');
    var inputDate = formWorkOrderDocument.find('#date');
    var jobList = formWorkOrderDocument.find('#job-list-wrapper');

    function getJobList() {
        const date = $(this).val();
        if (date !== '' && date !== undefined) {
            jobList.html('Fetching job data, please wait...');
            $.get(`${baseUrl}work-order/ajax-get-work-order-by-date?from_date=${date}&to_date=${date}`)
                .then(function (data) {
                    jobList.html(data);
                })
                .catch(err => console.log(err.message));
        } else {
            jobList.html('Select job date above');
        }
    }

    inputDate.on('change', getJobList);

    getJobList.call(inputDate);


    /**
     * Document Uploader
     * @type {void|jQuery|HTMLElement}
     */
    var documentUploader = $('#document-uploader');
    var inputFile = documentUploader.find('#input-file');
    var inputWrapper = documentUploader.find('#uploaded-input-wrapper');
    var uploadedFile = documentUploader.find('#uploaded-file');
    var uploadedItemTemplate = $('#upload-item-template').html();

    inputFile.fileupload({
        url: baseUrl + 'upload_document_file/upload',
        dataType: 'json',
        add: function (e, data) {
            var uploadErrors = [];
            var acceptFileTypes = /(gif|jpe?g|png|pdf|zip|rar|xls|xlsx|doc|docx)/i;
            if (data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                uploadErrors.push('Not an accepted file type');
            }
            if (data.originalFiles[0]['size'] && data.originalFiles[0]['size'] > 2000000) {
                uploadErrors.push('File size max 2MB');
            }

            if (uploadErrors.length > 0) {
                alert(uploadErrors.join("\n"));
            } else {
                var item = $.parseHTML(uploadedItemTemplate);
                data.context = $(item).appendTo(uploadedFile);
                data.submit();
            }
        },
        done: function (e, data) {
            var context = $(data.context);
            $.each(data.result, function (index, file) {
                if (file && file.status) {
                    $(context).find('.upload-file-name').text(file.data.client_name);
                    $(context).find('.btn-delete-file').data('file', file.data.file_name);
                    var input = $('<input/>', {
                        type: 'hidden',
                        name: index + '_uploaded[]',
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
            progressBar.removeClass('progress-bar-danger').css('width', progress + '%').text(progress + '%');
        },
        fail: function (e, data) {
            console.log(data.textStatus);
        }
    });

    documentUploader.on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        if (file) {
            if (buttonDelete.hasClass('old-file')) {
                buttonDelete.closest('.uploaded-item').remove();
                inputWrapper.find('input[value="' + file + '"]').remove();
            } else {
                buttonDelete.prop('disabled', true).text('DELETING...');
                $.ajax({
                    url: baseUrl + 'upload_document_file/delete_temp_upload',
                    type: 'POST',
                    data: {file: file},
                    accepts: {text: "application/json"},
                    success: function (data) {
                        buttonDelete.prop('disabled', false).text('DELETE');
                        if (data || data.status) {
                            inputWrapper.find('input[value="' + file + '"]').remove();
                            buttonDelete.closest('.uploaded-item').remove();
                            alert('File ' + file + ' is deleted');
                        } else {
                            alert('Failed delete uploaded file');
                        }
                    },
                    error: function (xhr, status, error) {
                        buttonDelete.prop('disabled', false).text('DELETE');
                        console.log(xhr.responseText, status, error);
                    }
                });
            }
        } else {
            buttonDelete.closest('.uploaded-item').remove();
        }
    });
});