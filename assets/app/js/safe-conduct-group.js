$(function () {
    var tableDeliveryTracking = $('#table-safe-conduct-group.table-ajax');
    var controlTemplate = $('#control-safe-conduct-group-template').html();
    tableDeliveryTracking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search safe conduct group"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'safe-conduct-group/data',
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'no_safe_conduct_group'},
            {data: 'total_safe_conduct'},
            {data: 'no_safe_conducts'},
            {data: 'created_at'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data) {
                return (data || '-').replace(/,/g, '<br>');
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_safe_conduct_group}}/g, full.no_safe_conduct_group);

                control = $.parseHTML(control);

                return $('<div>').append($(control).clone()).html();
            }
        }]
    });

});