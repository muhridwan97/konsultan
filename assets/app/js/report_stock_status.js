$(function () {
    var queryString = window.location.search.slice(1);

    $('#table-status-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/stock_status_data?' + queryString,
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_packing_list'},
            {data: 'no_container'},
            {data: 'no_goods'},
            {data: 'whey_number'},
            {data: 'goods_name'},
            {data: 'stock_quantity'},
            {data: 'unit'},
            {data: 'no_reference'},
            {data: 'payment_status'},
            {data: 'bcf_status'},
            {data: 'remark'}
        ],
        columnDefs: [{
            targets: ['numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});