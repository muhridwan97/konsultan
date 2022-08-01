$(function () {
    $('#table-stock-outbound').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/stock_outbound_data?' + window.location.search.slice(1),
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        scrollX: true,
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_reference_inbound'},
            {data: 'no_reference_outbound'},
            {data: 'no_goods'},
            {data: 'whey_number'},
            {data: 'goods_name'},
            {data: 'unit'},
            {data: 'ex_no_container'},
            {data: 'booking_outbound_quantity'},
            {data: 'request_quantity'},
            {data: 'hold_statuses'},
            {data: 'unload_locations'},
            {data: 'priorities'},
            {data: 'priority_descriptions'},
            {data: 'work_order_quantity'},
            {data: 'stock_outbound'},
            {data: 'age_inbound'},
            {data: 'age_outbound'},
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-hold-status'],
            render: function (data, type, full) {
                let labelStatus = 'default';
                if ((data || '').includes("HOLD")) {
                    labelStatus = 'danger';
                }
                if (data === 'RELEASED') {
                    labelStatus = 'success';
                }

                let content = `<span class="label label-${labelStatus}">${data}</span>`;

                if (data !== 'NOT REQUESTED') {
                    content = `<a href="${baseUrl}transporter-entry-permit-request-hold/view-history?id_customer=${full.id_customer}&id_booking=${full.id_booking_outbound}&id_goods=${full.id_goods}&id_unit=${full.id_unit}&ex_no_container=${full.ex_no_container || ''}&hold_status=${full.hold_statuses}">
                                ${content}
                               </a>`;
                }

                return content;
            }
        }, {
            targets: ['type-priority-location'],
            render: function (data, type, full) {
                let content = data;

                if (data === "NOT REQUESTED") {
                    content = `<span class="label label-default">${data}</span>`;
                }
                else if (data === "NOT SET") {
                    content = `<span class="label label-danger">${data}</span>`;
                    content = `<a href="${baseUrl}transporter-entry-permit-request-priority/edit?id_upload=${full.id_upload_outbound}&id_booking=${full.id_booking_outbound}&id_goods=${full.id_goods}&id_unit=${full.id_unit}&ex_no_container=${full.ex_no_container || ''}">
                                ${content}
                               </a>`;
                } else {
                    content = `<a href="${baseUrl}transporter-entry-permit-request-priority/view-history?id_upload=${full.id_upload_outbound}&id_booking=${full.id_booking_outbound}&id_goods=${full.id_goods}&id_unit=${full.id_unit}&ex_no_container=${full.ex_no_container || ''}">
                                ${content}
                               </a>`;
                }

                const currentQuantity = (full.request_quantity > full.booking_outbound_quantity ? full.booking_outbound_quantity : full.request_quantity);
                if (currentQuantity > 0 && currentQuantity > full.work_order_quantity) {

                }

                return content;
            }
        }, {
            targets: '_all',
            render: function (data) {
                return (!data || $.trim(data) === '') ? '-' : data;
            }
        }],
        createdRow: function(row, data, dataIndex) {
            if ((data.hold_statuses || '').includes("HOLD")) {
                $(row).addClass('danger');
            }
        }
    });
});
