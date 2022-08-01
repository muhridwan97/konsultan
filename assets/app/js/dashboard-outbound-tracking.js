$(function () {
    const tableTrackingDetail = $('#table-outbound-tracking-detail.table-ajax');
    tableTrackingDetail.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search tracking outbound"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'dashboard-outbound-tracking/ajax-get-data-tracking?' + window.location.search.slice(1),
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        pageLength: 20,
        columns: [
            {data: 'no'},
            {data: 'nomor_order'},
            {data: 'nomor_kontainer'},
            {data: 'vehicle'},
            {data: 'tep_code'},
            {data: 'checked_in_at'},
            {data: 'checked_out_at'},
            {data: 'tanggal_ambil_kontainer'},
            {data: 'tanggal_stuffing'},
            {data: 'tanggal_dooring'},
            {data: 'tanggal_kontainer_kembali_kedepo'},
            {data: 'notified_at'},
        ],
        columnDefs: [{
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data) === '' ? '-' : moment(data).format('DD MMMM YYYY  H:mm');
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' ? '-' : data;
            }
        }]
    });

});