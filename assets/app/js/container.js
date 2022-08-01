$(function () {
    var tableContainer = $('#table-container.table-ajax');
    var controlTemplate = $('#control-container-template').html();

    tableContainer.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search container"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'container/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'shipping_line'},
            {data: 'no_container'},
            {data: 'type'},
            {data: 'size'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 1,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? 'No Shipping Line' : data;
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{container}}/g, full.no_container);
            }
        }]
    });
});