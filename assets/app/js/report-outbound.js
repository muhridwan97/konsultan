$(function () {

    var queryString = window.location.search.slice(1);

    $('#table-outbound-goods-external').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl + 'report/activity_goods_data/OUTBOUND?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'invoice_number'},
            {data: 'no_reference'},
            {data: 'ex_no_container'},
            {data: 'no_label'},
            {data: 'description'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'total_weight'},
            {data: 'total_volume'},
            {data: 'bl_number'},
            {data: 'security_out'},
            {data: 'gate_out'},
            {data: 'no_trucking'},
            {data: 'driver_name'}
        ],
        columnDefs: [{
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data, type, full, meta) {
                return setNumeric(data);
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

});
