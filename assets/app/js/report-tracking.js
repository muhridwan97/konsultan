$(function () {
    const tableOutboundTracking = $('#table-outbound-tracking.table-ajax');
    tableOutboundTracking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search outbound tracking"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report-tracking/ajax-get-outbound-tracking?' + window.location.search.slice(1),
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        pageLength: 25,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {data: 'no'},
            {data: 'no_reference'},
            {data: 'no_reference_inbound'},
            {data: 'no_order'},
            {data: 'order_date'},
            {data: 'no_safe_conduct'},
            {data: 'no_safe_conduct_group'},
            {data: 'no_safe_conduct_description'},
            {data: 'no_plat'},
            {data: 'phbid_no_plat'},
            {data: 'vehicle_type'},
            {data: 'phbid_vehicle_type'},
            {data: 'driver'},
            {data: 'tep_code'},
            {data: 'tracking_link_description'},
            {data: 'no_work_order'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'ambil_kontainer_/_take_container'},
            {data: 'checked_in'},
            {data: 'checked_out'},
            {data: 'rm_kolam_/_stuffing'},
            {data: 'dooring_/_site_transit'},
            {data: 'site_transit_actual'},
            {data: 'site_transit_description'},
            {data: 'kontainer_kembali_ke_depo_/_unloading'},
            {data: 'unloading_actual'},
            {data: 'unloading_description'},
            {data: 'received_date'},
            {data: 'driver_handover_date'},
            {data: 'handover_description'},
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