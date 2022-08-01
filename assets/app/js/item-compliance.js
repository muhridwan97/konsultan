$(function () {
    var tableItemCompliance = $('#table-item-compliance.table-ajax');
    var controlTemplate = $('#control-item-template').html();

    tableItemCompliance.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search Item"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'item_compliance/data?' + window.location.search.slice(1),
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-hide',
                data: 'item_name'
            },
            {data: 'no_hs'},
            {data: 'unit'},
            {data: 'customer_name'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-action'],
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{item}}/g, full.item_name);
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

});