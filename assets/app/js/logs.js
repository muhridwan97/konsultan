$(function () {
    var tableLogs = $('#table-logs');
    var controlTemplate = $('#control-logs-template').html();

    tableLogs.DataTable({
        language: {searchPlaceholder: "Search log"},
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'logs/logs_data',
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'type'},
            {data: 'data'},
            {data: 'name'},
            {data: 'created_at'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 'type-json',
            data: 'data',
            render: function (data, type, full) {
                var dataParsed = JSON.parse(data);
                    return 'Account: ' + dataParsed.name + ' (' + dataParsed.username + ')<br> Access: <b>' + dataParsed.access + '</b>';
                
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{type}}/g, full.type);
            }
        }, {
            targets: 'type-date',
            render: function (data, type, full, meta) {
                return moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});