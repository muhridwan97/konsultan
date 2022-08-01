$(function(){
    var queryString = window.location.search.slice(1);

    $('#table-ex-btd').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/custom_btd_data?' + queryString,
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
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'container_size'},
            {data: 'no_container'},
            {data: 'owner_name'},
            {data: 'goods_name'},
            {data: 'no_sprint'},
            {data: 'sprint_date'},
            {data: 'inbound_date'},
            {data: 'pencacahan_date'},
            {data: 'outbound_date'},
            {data: 'no_reference_out'},
            {data: 'reference_out_date'},
            {data: 'booking_date'},
            {data: 'kep_btd_lelang'},
            {data: 'kep_btd_date'},
            {data: 'kep_bmn_lelang'},
            {data: 'kep_bmn_date'},
            {data: 'tanggal_lelang_1'},
            {data: 'tanggal_lelang_2'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: [2, 8, 9, 10, 11, 13, 14, 16, 18, 19, 20],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });


    $('#table-ex-bdn').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/custom_bdn_data?' + queryString,
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
            {data: 'no_bdn'},
            {data: 'date_bdn'},
            {data: 'container_size'},
            {data: 'no_container'},
            {data: 'owner_name'},
            {data: 'goods_name'},
            {data: 'no_sprint'},
            {data: 'sprint_date'},
            {data: 'inbound_date'},
            {data: 'pencacahan_date'},
            {data: 'outbound_date'},
            {data: 'no_reference_out'},
            {data: 'no_bmn'},
            {data: 'date_bmn'},
            {data: 'other_clearance_doc'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: [2, 8, 9, 10, 11, 14],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-ex-bmn').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/custom_bmn_data?' + queryString,
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
            {data: 'no_bmn'},
            {data: 'date_bmn'},
            {data: 'container_size'},
            {data: 'no_container'},
            {data: 'owner_name'},
            {data: 'goods_name'},
            {data: 'no_sprint'},
            {data: 'sprint_date'},
            {data: 'inbound_date'},
            {data: 'pencacahan_date'},
            {data: 'outbound_date'},
            {data: 'no_reference_out'},
            {data: 'reference_out_date'},
            {data: 'no_djkn'},
            {data: 'date_djkn'},
            {data: 'tanggal_lelang_1'},
            {data: 'tanggal_lelang_2'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });


    $('#table-ex-tegahan').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_tpp/custom_tegahan_data?' + queryString,
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
            {data: 'container_size'},
            {data: 'container_size'},
            {data: 'container_size'},
            {data: 'owner_name'},
            {data: 'tps_name'},
            {data: 'tps_region'},
            {data: 'goods_name'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'document_status'},
            {data: 'outbound_date'},
            {data: 'no_doc_kep'},
            {data: 'doc_kep_date'}
        ],
        columnDefs: [{
            targets: [3, 5, 7, 8, 21, 23],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: 11,
            render: function (data, type, full, meta) {
                return data == '20' ? '1' : '-';
            }
        }, {
            targets: 12,
            render: function (data, type, full, meta) {
                return data == '40' ? '1' : '-';
            }
        }, {
            targets: 13,
            render: function (data, type, full, meta) {
                return data == '45' ? '1' : '-';
            }
        }, {
            targets: [18],
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