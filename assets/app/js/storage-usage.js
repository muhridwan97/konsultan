$(function () {
    const tableStorageUsage = $('#table-storage-usage.table-ajax');
    const controlTemplate = $('#control-storage-usage-template').html();
    tableStorageUsage.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search storage usage"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'storage-usage/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'date'},
            {data: 'description'},
            {data: 'total_customer_usage', className: "text-center"},
            {data: 'total_proceed', className: "text-center"},
            {data: 'total_validated', className: "text-center"},
            {data: 'total_skipped', className: "text-center"},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) === '' ? '-' : moment(data).format('DD MMMM YYYY');
            }
        }, {
            targets: ['type-proceed'],
            render: function (data, type, full) {
                return `${data} / ${full.total_customer_usage}`;
            }
        }, {
            targets: ['type-validated'],
            className: 'text-center',
            render: function (data) {
                return `<span class="label label-primary">${data}</span>`;
            }
        }, {
            targets: ['type-skipped'],
            className: 'text-center',
            render: function (data) {
                return `<span class="label label-danger">${data}</span>`;
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let labelStatus = 'default';
                switch (data) {
                    case 'PENDING':
                        labelStatus = 'default';
                        break;
                    case 'VALIDATED':
                    case 'PROCEED':
                        labelStatus = 'success';
                        break;
                    case 'SKIPPED':
                        labelStatus = 'danger';
                        break;
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{date}}/g, full.date);

                control = $.parseHTML(control);

                if (full.status !== 'PENDING') {
                    $(control).find('.btn-validate').remove();
                }

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