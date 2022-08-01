$(function () {
    const tableCustomerStorageCapacity = $('#table-customer-storage-capacity.table-ajax');
    const controlTemplate = $('#control-customer-storage-capacity-template').html();
    tableCustomerStorageCapacity.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search customer storage"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'customer-storage-capacity/data?' + window.location.search.slice(1),
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
            {data: 'customer_name'},
            {data: 'effective_date'},
            {data: 'expired_date'},
            {data: 'warehouse_capacity', class: 'text-center'},
            {data: 'yard_capacity', class: 'text-center'},
            {data: 'covered_yard_capacity', class: 'text-center'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                const statuses = {'ACTIVE': 'success', 'PENDING': 'warning', 'PASSED': 'danger', 'EXPIRED': 'danger'};
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{customer_name}}/g, full.customer_name);

                control = $.parseHTML(control);
                if (full.status === 'EXPIRED') {
                    $(control).find('.action-edit').remove();
                    $(control).find('.action-delete').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return !data || $.trim(data) === '' ? '-' : data;
            }
        }]
    });
});