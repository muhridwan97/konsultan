$(function () {

    var queryString = window.location.search.slice(1);

    $('#table-summary-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report-tpp/stock-summary-container-data?' + queryString,
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'owner_name'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'no_nhp'},
            {data: 'nhp_date'},
            {data: 'no_bc11'},
            {data: 'bc11_date'},
            {data: 'no_ba_segel'},
            {data: 'ba_segel_date'},
            {data: 'no_kep'},
            {data: 'kep_date'},
            {data: 'no_bl'},
            {data: 'bl_date'},
            {data: 'document_status'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'shipping_line'},
            {data: 'no_container'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'stock'},
            {data: 'position'},
            {data: 'warehouse'},
            {data: 'seal'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'is_empty'},
            {data: 'is_hold'},
            {data: 'age'},
            {data: 'inbound_date'},
            {data: 'outbound_date'},
            {data: 'lelang_date_1'},
            {data: 'lelang_date_2'},
            {data: 'lelang_date_3'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: ['type-booking'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/booking_tracker?booking=' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-container'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/stock_mutation_container?filter_container=1&container[]=' + full.id_container + '">' + data + '</a>';
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
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
            targets: ['type-age'],
            render: function (data) {
                return numberFormat(data, 0, ',', '.') + ' days';
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });


    $('#table-shipping-line-stock').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/shipping_line_stock_data?' + queryString,
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
            {data: 'no_container'},
            {data: 'container_size'},
            {data: 'container_type'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner_name'},
            {data: 'position'},
            {data: 'completed_at'},
            {data: 'completed_at'},
            {data: 'seal'},
            {data: 'no_bc11'},
            {data: 'bc11_date'},
            {data: 'pos'},
            {data: 'no_bl'},
            {data: 'goods_name'},
            {data: 'stock'},
            {data: 'unit'},
            {data: 'document_status'},
            {data: 'document_status_date'},
            {data: 'shipping_line_name'},
            {data: 'tps_name'},
            {data: 'no_nhp'},
            {data: 'nhp_date'},
            {data: 'no_doc_kep'},
            {data: 'doc_kep_date'},
            {data: 'no_reference_out'},
            {data: 'reference_out_date'}
        ],
        columnDefs: [{
            targets: [8, -1, -3, -5],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: 9,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('H:mm');
            }
        }, {
            targets: 15,
            render: function (data, type, full, meta) {
                var goodsName = $.trim(data) == '' ? '-' : data.replace(/\^\^/g, '<br>');
                return goodsName;
            }
        }, {
            targets: 16,
            render: function (data, type, full, meta) {
                var quantity = $.trim(data) == '' ? '-' : data.replace(/\^\^/g, '<br>');
                return quantity;
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-custom-stock').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/customs_stock_data?' + queryString,
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
            {data: 'booking_type'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'no_ba_serah'},
            {data: 'ba_serah_date'},
            {data: 'no_ba_seal'},
            {data: 'ba_seal_date'},
            {data: 'inbound_date'},
            {data: 'cargo_type'},
            {data: 'no_container'},
            {data: 'ex_container'},
            {data: 'container_size'},
            {data: 'container_size'},
            {data: 'container_size'},
            {data: 'owner_name'},
            {data: 'tps_name'},
            {data: 'tps_region'},
            {data: 'goods_name'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'no_nhp'},
            {data: 'nhp_date'},
            {data: 'document_status'},
            {data: 'stripping_date'},
            {data: 'stuffing_date'},
            {data: 'outbound_date'},
            {data: 'no_doc_kep'},
            {data: 'doc_kep_date'},
            {data: 'no_reference_out'},
            {data: 'reference_out_date'},
            {data: 'description'},
            {data: 'total_in_container'},
            {data: 'total_in_goods'},
            {data: 'total_out_container'},
            {data: 'total_out_goods'}
        ],
        columnDefs: [{
            targets: [3, 5, 7, 8, -6, -8, -10, -11, -12, -14],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: 12,
            render: function (data, type, full, meta) {
                return data == '20' ? '1' : '-';
            }
        }, {
            targets: 13,
            render: function (data, type, full, meta) {
                return data == '40' ? '1' : '-';
            }
        }, {
            targets: 14,
            render: function (data, type, full, meta) {
                return data == '45' ? '1' : '-';
            }
        }, {
            targets: [-1, -2, -3, -4, 19],
            render: function (data, type, full, meta) {
                return numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});