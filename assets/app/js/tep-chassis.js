$(function () {
    const tableTEPChassis = $('#table-tep-chassis.table-ajax');
    const controlTemplate = $('#control-tep-chassis-template').html();
    tableTEPChassis.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search chassis"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit-chassis/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'no_chassis'},
            {data: 'checked_in_at'},
            {data: 'checked_in_description'},
            {data: 'checked_out_at'},
            {data: 'checked_out_description'},
            {data: 'no_work_order'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-tep'],
            render: function (data, type, full, meta) {
                return '<a href="' + (baseUrl + 'transporter-entry-permit/view/' + full.id_tep) + '">' + full.tep_code + '</a>';
            }
        }, {
            targets: ['type-work-order'],
            render: function (data, type, full, meta) {
                return full.id_work_order ? '<a href="' + (baseUrl + 'work-order/view/' + full.id_work_order) + '">' + full.no_work_order + '</a>' : '-';
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id);

                control = $.parseHTML(control);

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