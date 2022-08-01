$(function () {
    const queryString = window.location.search.slice(1);

    $('#table-inbound-progress').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/inbound-progress_data/?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {data: 'no', class: 'responsive-hide'},
            {data: 'branch_name'},
            {data: 'customer'},
            {data: 'booking_type'},
            {data: 'no_reference'},
            {data: 'no_registration'},
            {data: 'no_bill_of_loading'},
            {data: 'no_invoice'},
            {data: 'party'},
            {data: 'goods_name'},
            {data: 'total_net_weight'},
            {data: 'total_gross_weight'},
            {data: 'cif'},
            {data: 'eta_date'},
            {data: 'ata_date'},
            {data: 'upload_date'},
            {data: 'draft_date'},
            {data: 'type_parties'},
            {data: 'parties'},
            {data: 'confirmation_date'},
            {data: 'do_date'},
            {data: 'expired_do_date'},
            {data: 'freetime_do_date'},
            {data: 'sppb_date'},
            {data: 'sppd_date'},
            {data: 'hardcopy_date'},
            {data: 'status'},
        ],
        columnDefs: [{
            targets: ['date'],
            render: function (data, type, full) {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['date-time'],
            render: function (data, type, full) {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

    $('#table-outbound-progress').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/outbound_progress_data/?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {data: 'no', class: 'responsive-hide'},
            {data: 'branch_name'},
            {data: 'customer'},
            {data: 'booking_type'},
            {data: 'no_invoice'},
            {data: 'no_registration'},
            {data: 'no_reference'},
            {data: 'no_reference_inbound'},
            {data: 'goods_name'},
            {data: 'total_net_weight'},
            {data: 'total_gross_weight'},
            {data: 'cif'},
            {data: 'upload_date'},
            {data: 'draft_date'},
            {data: 'type_parties'},
            {data: 'parties'},
            {data: 'confirmation_date'},
            {data: 'billing_date'},
            {data: 'bpn_date'},
            {data: 'sppf_date'},
            {data: 'sppb_date'},
            {data: 'sppd_date'},
            {data: 'sppd_in_date'},
            {data: 'status'},
            {data: 'is_hold'},
        ],
        columnDefs: [{
            targets: ['date'],
            render: function (data, type, full) {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['date-time'],
            render: function (data, type, full) {
                //return $.trim(data) === '' ? '-' : moment(data, 'D/MM/YYYY HH:mm:ss').format('D MMMM YYYY HH:mm');
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-hold'],
            render: function (data) {
                return data == '1' ? 'YES' : 'NO';
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });

});
