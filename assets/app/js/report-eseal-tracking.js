$(function () {

    const queryString = window.location.search.slice(1);

    $('#table-eseal-tracking').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/eseal_tracking_data?' + queryString,
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
            {data: 'no_safe_conduct'},
            {data: 'no_eseal'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition_type'},
            {data: 'security_start'},
            {data: 'security_stop'},
            {data: 'containers_load'},
            {data: 'source_warehouse'},
            {data: 'destination'},
            {data: 'status_tracking'},
            {data: 'total_route'},
            {data: 'total_distance'},
            {data: 'id_safe_conduct'},
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data, type, full) {
                return '<a href="' + baseUrl + 'safe-conduct/view/' + full.id_safe_conduct + '">' + data + '</a>';
            }
        }, {
            targets: ['type-loading'],
            render: function (data, type, full) {
                return (full.containers_load === '' ? full.goods_load : full.containers_load);
            }
        }, {
            targets: ['type-eseal'],
            render: function (data, type, full) {
                return `${data || '-'}<br><small class="text-muted">${full.device_name || ''}</small>`;
            }
        }, {
            targets: ['type-source'],
            render: function (data, type, full) {
                return `${data || '-'}<br><small class="text-muted">${full.source_warehouse_address || ''}</small>`;
            }
        }, {
            targets: ['type-destination'],
            render: function (data, type, full) {
                return `${data || '-'}<br><small class="text-muted">${full.destination_address || ''}</small>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data) {
                return `<a class="btn btn-primary" href="${baseUrl}report/eseal-route/${data}">ROUTES</a>`;
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) == '') ? '-' : data;
            }
        }]
    });
});
