$(function () {
    const tableTEPTracking = $('#table-tep-tracking-link.table-ajax');
    const controlTemplate = $('#control-tep-tracking-template').html();
    tableTEPTracking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search linked TEP"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit-tracking/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'tep_code'},
            {data: 'checked_out_at'},
            {data: 'phbid_no_vehicle'},
            {data: 'site_transit_actual_date'},
            {data: 'unloading_actual_date'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-tep'],
            render: function (data, type, full) {
                return data + '<br><small class="text-muted">' + full.receiver_no_police + '</small>';
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
                    case 'LINKED':
                        labelStatus = 'warning';
                        break;
                    case 'SITE TRANSIT':
                        labelStatus = 'primary';
                        break;
                    case 'UNLOADED':
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
                    .replace(/{{id_transporter_entry_permit}}/g, full.id_transporter_entry_permit || full.id_tep)
                    .replace(/{{tep_code}}/g, full.tep_code);

                control = $.parseHTML(control);

                if (full.status === 'NOT LINKED') {
                    $(control).find('.action-view').remove();
                    $(control).find('.action-edit').remove();
                    $(control).find('.action-site-transit').remove();
                    $(control).find('.action-unloading').remove();
                    $(control).find('.action-delete').remove();
                } else {
                    $(control).find('.action-link').remove();

                    if (full.status === 'LINKED') {
                        $(control).find('.action-edit').remove();
                    }

                    if (full.status === 'UNLOADED') {
                        $(control).find('.action-delete').remove();
                    }

                    // show site transit button when not filled (LINKED)
                    if (full.site_transit_actual_date) {
                        $(control).find('.action-site-transit').remove();
                    }

                    // show confirm unloading button when actual date is confirmed
                    if (full.unloading_actual_date || !full.site_transit_actual_date) {
                        $(control).find('.action-unloading').remove();
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