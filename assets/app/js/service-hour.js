$(function () {
    var tableServiceHour = $('#table-service-hour.table-ajax');
    var controlTemplate = $('#control-service-hour-template').html();
    tableServiceHour.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search service hour"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'service-hour/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'service_day'},
            {data: 'service_time_start'},
            {data: 'service_time_end'},
            {data: 'effective_date'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-hour'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '-' : moment(full.effective_date + ' ' + data).format('HH:mm');
            }
        }, {
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('DD MMMM YYYY');
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{service_hour_label}}/g, full.service_day + ' effective ' + full.effective_date);

                control = $.parseHTML(control);

                return $('<div />').append($(control).clone()).html();
            }
        }]
    });

});