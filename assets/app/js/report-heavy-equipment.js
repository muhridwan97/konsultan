$(function () {
    var tableWorkOrderOvertime = $('#table-heavy-equipment-usage.table-ajax');
    tableWorkOrderOvertime.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/heavy-equipment-usage-data?' + window.location.search.slice(1),
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {data: 'no'},
            {data: 'request_date'},
            {data: 'no_requisition'},
            {data: 'item_category'},
            {data: 'no_purchase_order'},
            {data: 'branch'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'heavy_equipment_item'},
            {data: 'no_reference_in'},
            {data: 'no_reference_out'},
            {data: 'customer_name'},
            {data: 'handling_type'},
            {data: 'no_work_order'},
        ],
        columnDefs: [{
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data || '') === '' ? '-' : moment(data).format('DD MMMM YYYY HH:mm');
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data || '') === '' ? '-' : data;
            }
        }]
    });

});