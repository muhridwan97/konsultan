$(function () {
    const tableDeliveryInspection = $('#table-delivery-inspection.table-ajax');
    const controlTemplate = $('#control-delivery-inspection-template').html();
    tableDeliveryInspection.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search delivery inspection"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'delivery-inspection/data?' + window.location.search.slice(1),
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
            {data: 'date'},
            {data: 'location'},
            {data: 'pic_tci'},
            {data: 'pic_khaisan'},
            {data: 'pic_smgp'},
            {data: 'total_vehicle'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-total-vehicle'],
            render: function (data, type, full) {
                return `${full.total_match} / ${full.total_vehicle}`;
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let statusLabel = 'primary';
                switch (data) {
                    case 'PENDING':
                        statusLabel = 'default';
                        break;
                    case 'CONFIRMED':
                        statusLabel = 'success';
                        break;
                }
                return `<span class="label label-${statusLabel}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{date}}/g, full.date);

                control = $.parseHTML(control);

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

});