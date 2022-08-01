$(function () {
    var queryString = window.location.search.slice(1);

    $('#table-inbound-bc').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_bc/in_data?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        searching: false,
        scrollX: true,
        columns: [
            {data: 'no'},
            {data: 'branch'},
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'warehouse'},
            {data: 'bc_doc_/_reference_no'},
            {data: 'bc_doc_/_reference_date'},
            {data: 'bc_doc_/_booking_type'},
            {data: 'booking_no'},
            {data: 'booking_date'},
            {data: 'supplier'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner'},
            {data: 'safe_conduct_no'},
            {data: 'safe_conduct_date'},
            {data: 'vehicle_type'},
            {data: 'driver'},
            {data: 'police_no'},
            {data: 'expedition'},
            {data: 'eseal_no'},
            {data: 'job_no'},
            {data: 'transaction_date'},
            {data: 'item_category'},
            {data: 'item_no'},
            {data: 'item_name'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'tonnage'},
            {data: 'volume'},
            {data: 'position'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'container_seal'},
            {data: 'container_status'},
            {data: 'item_condition'},
            {data: 'status_danger'},
            {data: 'pallet_no'},
            {data: 'description'},
            {data: 'admin_name'},
            {data: 'tally_name'}
        ],
        columnDefs: [{
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-outbound-bc').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report_bc/out_data?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        searching: false,
        scrollX: true,
        columns: [
            {data: 'no'},
            {data: 'branch'},
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'warehouse'},
            {data: 'bc_doc_/_reference_no_in'},
            {data: 'bc_doc_/_reference_date_in'},
            {data: 'bc_doc_/_booking_type_in'},
            {data: 'booking_no_in'},
            {data: 'booking_date_in'},
            {data: 'bc_doc_/_reference_no'},
            {data: 'bc_doc_/_reference_date'},
            {data: 'bc_doc_/_booking_type'},
            {data: 'booking_no'},
            {data: 'booking_date'},
            {data: 'supplier'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner'},
            {data: 'safe_conduct_no'},
            {data: 'safe_conduct_date'},
            {data: 'vehicle_type'},
            {data: 'driver'},
            {data: 'police_no'},
            {data: 'expedition'},
            {data: 'eseal_no'},
            {data: 'job_no'},
            {data: 'transaction_date'},
            {data: 'item_category'},
            {data: 'item_no'},
            {data: 'item_name'},
            {data: 'quantity'},
            {data: 'unit'},
            {data: 'tonnage'},
            {data: 'volume'},
            {data: 'position'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'container_seal'},
            {data: 'container_status'},
            {data: 'item_condition'},
            {data: 'status_danger'},
            {data: 'no_ex_container'},
            {data: 'pallet_no'},
            {data: 'description'},
            {data: 'admin_name'},
            {data: 'tally_name'}
        ],
        columnDefs: [{
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});