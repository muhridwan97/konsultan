$(function () {
    const tableTEPTracking = $('#table-safe-conduct-handover.table-ajax');
    const controlTemplate = $('#control-safe-conduct-handover-template').html();
    tableTEPTracking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search handover"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'safe-conduct-handover/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'no_safe_conduct'},
            {data: 'tep_code'},
            {data: 'no_police'},
            {data: 'received_date'},
            {data: 'driver_handover_date'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-safe-conduct'],
            render: function (data, type, full) {
                let link = `${baseUrl}safe-conduct/view/${full.id_safe_conduct}`;
                if (full.id_safe_conduct_group) {
                    link = `${baseUrl}safe-conduct-group/view/${full.id_safe_conduct_group}`;
                }
                return `<a href="${link}">${data}</a><br><small class="text-muted">${full.no_reference.replace(/,/g, '<br>')}</small>`;
            }
        }, {
            targets: ['type-tep'],
            render: function (data, type, full) {
                return `<a href="${baseUrl}transporter-entry-permit-tracking/view/${full.id_tep_tracking}">${data}</a>`;
            }
        }, {
            targets: ['type-vehicle'],
            render: function (data, type, full) {
                return `${data}<br><small class="text-muted">${full.driver}</small>`;
            }
        }, {
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data) === '' ? '-' : moment(data).format('DD MMMM YYYY  H:mm');
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let labelStatus = 'default';
                switch (data) {
                    case 'PENDING':
                        labelStatus = 'default';
                        break;
                    case 'RECEIVED':
                        labelStatus = 'primary';
                        break;
                    case 'HANDOVER':
                        labelStatus = 'success';
                        break;
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{id_safe_conduct}}/g, full.id_safe_conduct)
                    .replace(/{{no_safe_conduct}}/g, full.no_safe_conduct);

                control = $.parseHTML(control);

                if (full.status === 'PENDING') {
                    $(control).find('.action-view').remove();
                    $(control).find('.action-edit').remove();
                    $(control).find('.action-delete').remove();
                } else {
                    $(control).find('.action-handover').remove();
                    // if user only has permission safe-conduct-handover and the data is already handover, remove edit action
                    if (full.status === "HANDOVER" && $(control).find('.action-edit.allow-edit-all').length === 0) {
                        $(control).find('.action-edit').remove();
                    }
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' ? '-' : data;
            }
        }]
    });

});