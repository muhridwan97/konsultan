$(function () {
    var queryString = window.location.search.slice(1);

    $('#table-comparator-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/booking-comparator-data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'owner_name'},
            {data: 'category'},
            {data: 'booking_type'},
            {data: 'no_reference'},
            {data: 'status'},
            {data: 'first_gate_in_date'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {
                class: 'success',
                data: 'booking_quantity'
            },
            {
                class: 'info',
                data: 'work_order_quantity'
            },
            {
                class: 'success',
                data: 'booking_unit'
            },
            {
                class: 'info',
                data: 'work_order_unit'
            },
            {data: 'booking_ex_container'},
            {data: 'work_order_weight'},
            {data: 'work_order_total_weight'},
            {data: 'work_order_gross_weight'},
            {data: 'work_order_total_gross_weight'},
            {data: 'work_order_volume'},
            {data: 'work_order_total_volume'},
            {data: 'work_order_ex_container'},
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let statusLabel = 'primary';
                switch (data) {
                    case 'APPROVED':
                        statusLabel = 'primary';
                        break;
                    case 'COMPLETED':
                        statusLabel = 'success';
                        break;
                }
                return `<span class="label label-${statusLabel}">${data}</span>`;
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});