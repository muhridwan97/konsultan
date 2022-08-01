$(function () {
    const tableTEPRequestRelease = $('#table-tep-request-release.table-ajax');
    const controlTemplate = $('#control-tep-request-release-template').html();
    tableTEPRequestRelease.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search request"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit-request-release/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'no_hold_reference'},
            {data: 'customer_name'},
            {data: 'hold_type'},
            {data: 'description'},
            {data: 'goods_name'},
            {data: 'hold_status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-goods'],
            render: function (data, type, full) {
                return data ? '<ul style="padding-left: 10px"><li>' + (data || '').replace(',', '</li><li>') : '';
            }
        }, {
            targets: ['type-hold-type'],
            render: function (data) {
                let labelStatus = 'default';
                switch (data) {
                    case 'HOLD':
                        labelStatus = 'danger';
                        break;
                    case 'RELEASED':
                        labelStatus = 'success';
                        break;
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-hold-status'],
            render: function (data) {
                let labelStatus = 'default';
                switch (data) {
                    case 'HOLD':
                        labelStatus = 'danger';
                        break;
                    case 'PARTIAL RELEASED':
                        labelStatus = 'primary';
                        break;
                    case 'RELEASED':
                        labelStatus = 'success';
                        break;
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_hold_reference}}/g, full.no_hold_reference);

                control = $.parseHTML(control);

                if (full.hold_status === 'RELEASED') {
                    $(control).find('.action-delete').remove();
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


    const formTepRequestRelease = $('#form-tep-request-release');
    const selectCustomer = formTepRequestRelease.find('#customer');
    const tableHoldGoods = formTepRequestRelease.find('#table-hold-goods');
    let selectedHoldId = tableHoldGoods.data('selected-hold');

    selectCustomer.on('change', function () {
        tableHoldGoods.find('tbody').html(`
            <tr><td colspan="6">Fetching hold goods...</td></tr>
        `);

        const customerId = $(this).val();
        const requestUrl = `${baseUrl}transporter-entry-permit-request-hold/ajax-get-hold-item-request?id_customer=${customerId}`;
        formTepRequestRelease.find('[type="submit"]').prop('disabled', true);
        fetch(requestUrl)
            .then(result => result.json())
            .then(goods => {
                if (goods.length) {
                    formTepRequestRelease.find('[type="submit"]').prop('disabled', false);
                    tableHoldGoods.find('tbody').empty();
                    goods.forEach((item) => {
                        buildGoodsList(item);
                    });
                    tableHoldGoods.find('input').iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue',
                        increaseArea: '20%'
                    });
                } else {
                    tableHoldGoods.find('tbody').html(`
                        <tr><td colspan="6">No hold data available</td></tr>
                    `);
                }
            })
            .catch(console.log);
    });

    if (selectCustomer.val()) {
        selectCustomer.trigger('change');
    }

    function buildGoodsList(item) {
        const order = tableHoldGoods.find('tbody tr').length + 1;
        const row = `
            <tr>
                <td class="text-center">${order}</td>
                <td class="no-wrap">${item.no_hold_reference}</td>
                <td>${item.no_reference}</td>
                <td>${item.goods_name}</td>
                <td>${item.unit}</td>
                <td class="no-wrap">${item.no_requests.replace(/,/g, '<br>')}</td>
                <td class="text-center">
                    <div class="checkbox icheck mt0">
                        <label for="goods-${item.id}">
                            <input type="checkbox" name="hold_goods[${item.id}]" id="goods-${item.id}" value="${item.id}" 
                                ${parseInt(selectedHoldId) === parseInt(item.id) ? 'checked' : ''} class="check-goods">
                        </label>
                    </div>
                </td>
            </tr>
        `;
        tableHoldGoods.find('tbody').first().append(row);
    }

});