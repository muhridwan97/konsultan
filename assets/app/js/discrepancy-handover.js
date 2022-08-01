$(function () {
    const tableDiscrepancyHandover = $('#table-discrepancy-handover.table-ajax');
    const controlTemplate = $('#control-discrepancy-handover-template').html();
    tableDiscrepancyHandover.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search handover"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'discrepancy-handover/data?' + window.location.search.slice(1),
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'no_discrepancy'},
            {data: 'created_at'},
            {data: 'no_reference'},
            {data: 'customer_name'},
            {data: 'total_discrepancy_item'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-status'],
            render: function (data) {
                const statuses = {
                    'PENDING': 'default',
                    'UPLOADED': 'primary',
                    'CONFIRMED': 'success',
                    'EXPLAINED': 'success',
                    'CANCELED': 'danger',
                    'IN USE': 'info',
                    'NOT USE': 'danger',
                    'DOCUMENT': 'primary',
                    'PHYSICAL': 'success',
                };
                return "<span class='label label-" + (statuses[data] || 'default') + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-date'],
            render: (data) => {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_reference}}/g, full.no_reference)
                    .replace(/{{attachment}}/g, full.attachment || '')
                    .replace(/{{attachment_url}}/g, full.attachment_url || '')
                    .replace(/{{no_discrepancy}}/g, full.no_discrepancy);

                control = $.parseHTML(control);

                if (!['IN USE'].includes(full.status)) {
                    $(control).find('.action-upload').remove();
                }
                if (!['UPLOADED'].includes(full.status)) {
                    $(control).find('.action-resend').remove();
                }

                if (['CANCELED'].includes(full.status)) {
                    $(control).find('.action-cancel').remove();
                }

                if (!['PENDING'].includes(full.status)) {
                    $(control).find('.action-usage').remove();
                }

                if (!['EXPLAINED', 'CONFIRMED'].includes(full.status)) {
                    $(control).find('.action-realization').remove();
                }

                if (['NOT USE'].includes(full.status)) {
                    $(control).find('.action-print').remove();
                }

                if (full.status === 'UPLOADED') {
                    $(control).find('.btn-upload-label').text('Edit Attachment');
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    const modalAttachment = $('#modal-attachment');
    tableDiscrepancyHandover.on('click', '.btn-upload', function (e) {
        e.preventDefault();

        const label = $(this).data('label');
        const noReference = $(this).data('no-reference');
        const attachment = $(this).data('attachment');
        const attachmentUrl = $(this).data('attachment-url');
        const url = $(this).attr('href');
        const fieldUploadedAttachment = $('#field-uploaded-attachment');

        modalAttachment.find('#handover-title').text(`${label} (${noReference})`);
        modalAttachment.find('form').attr('action', url);

        if (attachment === '') {
            fieldUploadedAttachment.hide();
        } else {
            fieldUploadedAttachment.show();
            fieldUploadedAttachment.find('#uploaded-attachment-link').attr('href', attachmentUrl).text(attachment);
        }

        modalAttachment.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});