$(function () {

    var queryString = window.location.search.slice(1);

    $('#table-summary-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        //ajax: baseUrl + 'report/stock_summary_container_data?' + queryString,
        ajax: {
            url: baseUrl + 'report/stock_summary_container_data?' + queryString,
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
            {data: 'no_reference'},
            {data: 'no_container'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'stock'},
            {data: 'do_subtype'},
            {data: 'expired_do_date'},
            {data: 'freetime_do_date'},
            {data: 'position'},
            {data: 'warehouse'},
            {data: 'source_warehouse'},
            {data: 'document_status'},
            {data: 'seal'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'is_empty'},
            {data: 'is_hold'},
            {data: 'age'},
            {data: 'inbound_date'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: ['type-booking'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/booking-tracker?booking=' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-container'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/stock-comparator?booking=' + full.id_booking + '&items[]=-1&containers[]=' + full.id_container + '">' + data + '</a>';
            }
        }, {
            targets: ['type-position'],
            render: function (data, type, full) {
                let blocks = (full.position_blocks || '').replace(new RegExp(/,/, 'g'), ', ');
                if (blocks) {
                    blocks = '<br><small class="text-muted">' + blocks + '</small>'
                }
                return (data || '-') + blocks;
            }
        }, {
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
            targets: ['type-age'],
            render: function (data) {
                return setNumeric(data) + ' days';
            }
        }, {
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        },  {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

    $('#table-summary-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        //ajax: baseUrl + 'report/stock_summary_goods_data?' + queryString,
        ajax: {
            url: baseUrl + 'report/stock_summary_goods_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        scrollX: true,
        search: {
            search: getParameterByName('search')
        },
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'owner_name'},
            {data: 'no_reference'},
            {data: 'no_invoice'},
            {data: 'no_bl'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'no_assembly'},
            {data: 'whey_number'},
            {data: 'position'},
            {data: 'warehouse'},
            {data: 'no_pallet'},
            {data: 'stock_quantity'},
            {data: 'unit'},
            {data: 'unit_weight'},
            {data: 'stock_weight'},
            {data: 'unit_gross_weight'},
            {data: 'stock_gross_weight'},
            {data: 'unit_length'},
            {data: 'unit_width'},
            {data: 'unit_height'},
            {data: 'unit_volume'},
            {data: 'stock_volume'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'is_hold'},
            {data: 'ex_no_container'},
            {data: 'age'},
            {data: 'inbound_date'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: ['type-booking'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/booking-tracker?booking=' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-assembly-goods'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '-' : '<a href="' + baseUrl + 'assembly_goods/view?goods=' + full.id_goods + '">' + data + '</a>';
            }
        }, {
            targets: ['type-goods'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/stock-comparator?booking=' + full.id_booking + '&containers[]=-1&items[]=' + full.id_goods + '">' + data + '</a>';
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-position'],
            render: function (data, type, full) {
                let blocks = (full.position_blocks || '').replace(new RegExp(/,/, 'g'), ', ');
                if (blocks) {
                    blocks = '<br><small class="text-muted">' + blocks + '</small>'
                }
                return (data || '-') + blocks;
            }
        }, {
            targets: ['type-danger'],
            render: function (data, type, full, meta) {
                return data;
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData == 'DANGER TYPE 1' || cellData === 'DANGER TYPE 2') {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-age'],
            render: function (data, type, full, meta) {
                return setNumeric(data) + ' days';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

    $('#table-summary-goods-external').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/stock_summary_goods_data?' + queryString,
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
            {data: 'no_goods'},
            {data: 'no_label'},
            {data: 'description'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'weight'},
            {data: 'volume'},
            {data: 'bl_number'},
            {data: 'first_stock_date'},
            {data: 'status'}
        ],
        columnDefs: [{
            targets: ['type-date'],
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
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

    $('#table-summary-assembly-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/stock_summary_assembly_goods_data?' + queryString,
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'owner_name'},
            {data: 'no_reference'},
            {data: 'no_invoice'},
            {data: 'no_bl'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'no_assembly'},
            {data: 'whey_number'},
            {data: 'position'},
            {data: 'warehouse'},
            {data: 'no_pallet'},
            {data: 'stock_quantity'},
            {data: 'unit'},
            {data: 'stock_tonnage'},
            {data: 'stock_tonnage_gross'},
            {data: 'stock_length'},
            {data: 'stock_width'},
            {data: 'stock_height'},
            {data: 'stock_volume'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'is_hold'},
            {data: 'ex_no_container'},
            {data: 'age'},
            {data: 'inbound_date'},
            {data: 'description'}
        ],
        columnDefs: [{
            targets: ['type-booking'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/booking-tracker?booking=' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-assembly-goods'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '-' : '<a href="' + baseUrl + 'Assembly_goods/view?goods=' + full.id_goods + '">' + data + '</a>';
            }
        }, {
            targets: ['type-goods'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'report/stock-mutation-goods?filter_goods=1&goods[]=' + full.id_goods + '">' + data + '</a>';
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return numberFormat(data, 2, ',', '.');
            }
        }, {
            targets: ['type-position'],
            render: function (data, type, full) {
                let blocks = (full.position_blocks || '').replace(new RegExp(/,/, 'g'), ', ');
                if (blocks) {
                    blocks = '<br><small class="text-muted">' + blocks + '</small>'
                }
                return (data || '-') + blocks;
            }
        }, {
            targets: ['type-danger'],
            render: function (data, type, full, meta) {
                return data;
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData == 'DANGER TYPE 1' || cellData === 'DANGER TYPE 2') {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            },
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData == 1) {
                    $(td).addClass('bg-red');
                }
            }
        }, {
            targets: ['type-age'],
            render: function (data, type, full, meta) {
                return setNumeric(data) + ' days';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

});
