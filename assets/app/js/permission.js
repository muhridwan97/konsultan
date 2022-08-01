$(function () {
    var tablePermission = $('#table-permission');
    var controlTemplate = $('#control-permission-template').html();

    tablePermission.DataTable({
        language: {searchPlaceholder: "Search permission"},
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'permission/data',
        columns: [
            {data: 'no'},
            {data: 'module'},
            {data: 'submodule'},
            {data: 'permission'},
            {data: 'created_at'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 4,
            data: 'deadline',
            render: function (data, type, full, meta) {
                return moment(data).format('LL');
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{permission}}/g, full.permission);
            }
        }]
    });
});