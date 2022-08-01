$(function () {

    var queryString = window.location.search.slice(1);

    $('#table-work-order-summary-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/work_order_summary_container_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        scrollX: true,
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_reference'},
            {data: 'no_booking'},
            {data: 'no_handling'},
            {data: 'handling_type'},
            {data: 'handling_status'},
            {data: 'no_safe_conduct'},
            {data: 'no_police'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'tep_code'},
            {data: 'checked_in_at'},
            {data: 'checked_out_at'},
            {data: 'no_work_order'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'taken_by'},
            {data: 'no_container'},
            {data: 'type'},
            {data: 'size'},
            {data: 'seal'},
            {data: 'is_empty'},
            {data: 'is_hold'},
            {data: 'status_condition'},
            {data: 'status_danger'},
            {data: 'description'},
            {data: 'created_at'}
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-danger'],
            render: function (data) {
                return data;
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData === 'DANGER TYPE 1' || cellData === 'DANGER TYPE 2') {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-empty'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Empty' : 'Full';
            },
            createdCell: function (td, cellData) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-hold'],
            render: function (data) {
                return data == 1 ? 'Yes' : 'No';
            },
            createdCell: function (td, cellData) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

    $('#table-work-order-summary-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/work_order_summary_goods_data?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        scrollX: true,
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_reference'},
            {data: 'no_booking'},
            {data: 'no_handling'},
            {data: 'handling_type'},
            {data: 'handling_status'},
            {data: 'no_safe_conduct'},
            {data: 'no_police'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'tep_code'},
            {data: 'checked_in_at'},
            {data: 'checked_out_at'},
            {data: 'no_work_order'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'taken_by'},
            {data: 'user_spv'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'no_hs'},
            {data: 'whey_number'},
            {data: 'quantity'},
            {data: 'unit_weight'},
            {data: 'total_weight'},
            {data: 'unit_gross_weight'},
            {data: 'total_gross_weight'},
            {data: 'unit_volume'},
            {data: 'total_volume'},
            {data: 'unit_length'},
            {data: 'unit_width'},
            {data: 'unit_height'},
            {data: 'no_pallet'},
            {data: 'ex_no_container'},
            {data: 'is_hold'},
            {data: 'status_condition'},
            {data: 'status_danger'},
            {data: 'description'},
            {data: 'created_at'}
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setCurrencyValue(Number(data || 0), '', ',', '.');
            }
        }, {
            targets: ['type-danger'],
            render: function (data) {
                return data;
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData === 'DANGER TYPE 1' || cellData === 'DANGER TYPE 2') {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-empty'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Empty' : 'Full';
            },
            createdCell: function (td, cellData) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-hold'],
            render: function (data) {
                return data == 1 ? 'Yes' : 'No';
            },
            createdCell: function (td, cellData) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

});
