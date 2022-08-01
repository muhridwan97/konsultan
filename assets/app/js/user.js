$(function () {
    const tableStorageUsage = $('#table-user.table-ajax');
    const controlTemplate = $('#control-user-template').html();
    tableStorageUsage.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search user"
        },
        serverSide: true,
        processing: true,
        pageLength: 25,
        ajax: {
            url: baseUrl + 'user/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'name'},
            {data: 'username'},
            {data: 'email'},
            {data: 'status'},
            {data: 'total_role'},
            {data: 'no'}
        ],
        columnDefs: [{
            targets: ['type-email'],
            render: function (data) {
                return `<a href="mailto:${data}">${data}</a>`;
            }
        }, {
            targets: ['type-role'],
            render: function (data, type, full) {
                return data === 0
                    ? 'No role available'
                    : `<a href="${baseUrl}user/role/${full.id}">${setNumeric(data)} roles</a>`;
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let labelStatus = 'default';
                switch (data) {
                    case 'PENDING':
                        labelStatus = 'warning';
                        break;
                    case 'ACTIVATED':
                        labelStatus = 'success';
                        break;
                    case 'SUSPENDED':
                        labelStatus = 'danger';
                        break;
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            className: 'text-center',
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{name}}/g, full.name);

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' ? '-' : data;
            }
        }]
    });

});