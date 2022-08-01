$(function () {
    const tableWorkOrderUnlockHandheld = $('#table-work-order-unlock-handheld.table-ajax');
    const controlTemplate = $('#control-work-order-unlock-handheld-template').html();
    tableWorkOrderUnlockHandheld.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search unlocked work order"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'work-order-unlock-handheld/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'no_work_order'},
            {data: 'customer_name'},
            {data: 'unlocked_until'},
            {data: 'description'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) === '' ? '-' : moment(data).format('DD MMMM YYYY');
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                if (data === 'LOCKED') {
                    return `<span class="label label-danger">${data}</span>`;
                } else {
                    return `<span class="label label-success">${data}</span>`
                }
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id_work_order)
                    .replace(/{{no_work_order}}/g, 'Unlock ' + full.no_work_order);

                control = $.parseHTML(control);

                return $('<div />').append($(control).clone()).html();
            }
        }]
    });

});