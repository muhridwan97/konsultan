$(function () {
    const tableTEPRequestHold = $('#table-tep-request-hold.table-ajax');
    const controlTemplate = $('#control-tep-request-hold-template').html();
    tableTEPRequestHold.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search request"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit-request-hold/ajax-get-data?' + window.location.search.slice(1),
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
                return data ? '<ul style="padding-left: 10px"><li>' + (data || '').replace(/,/g, '</li><li>') : '';
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


    // form request hold item
    const formTepRequest = $('#form-tep-request-hold');
    const selectCustomer = formTepRequest.find('#customer');

    // hold editor scripts
    const goodsHoldEditor = $('#goods-hold-editor');
    const tableGoods = goodsHoldEditor.find('#table-goods');
    const btnAddGoods = $('#btn-add-goods');

    const modalGoodsList = $('#modal-goods-list');
    const btnReloadGoods = modalGoodsList.find('#btn-reload-goods');
    const btnTakeAllGoods = modalGoodsList.find('#btn-take-all-goods');

    const modalTakeGoods = $('#modal-take-goods');
    const inputHoldDescription = modalTakeGoods.find('#hold_description');
    const btnConfirmTakeGoods = modalTakeGoods.find('#btn-confirm-take-goods');

    let lastCustomerId = modalGoodsList.data('customer-id');
    let lastGoodsData = [];

    selectCustomer.on('change', function (e) {
        e.preventDefault();

        const customerId = $(this).val();
        modalGoodsList.data('customer-id', customerId);

        const placeholder = `<tr class="row-placeholder"><td colspan="6">No requested goods data</td></tr>`;
        tableGoods.find('tbody').html(placeholder);
    });

    if (selectCustomer.val() && !tableGoods.find('.row-goods').length) {
        selectCustomer.trigger('change');
    }

    btnAddGoods.on('click', function (e) {
        e.preventDefault();

        const customerId = modalGoodsList.data('customer-id');
        if (customerId && customerId !== lastCustomerId || lastGoodsData.length === 0) {
            fetchGoodsList();
        } else {
            buildGoodsList(lastGoodsData);
        }

        modalGoodsList.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    function fetchGoodsList() {
        const customerId = modalGoodsList.data('customer-id');
        const stockUrl = `${baseUrl}transporter-entry-permit-request-hold/ajax-get-outstanding-item-request?id_customer=${customerId}`;

        modalGoodsList.find('tbody').html(`
            <tr><td colspan="8">Fetching requested goods...</td></tr>
        `);
        btnTakeAllGoods.prop('disabled', true);
        btnReloadGoods.prop('disabled', true);

        fetch(stockUrl)
            .then(result => result.json())
            .then(stock => {
                btnTakeAllGoods.prop('disabled', false);
                btnReloadGoods.prop('disabled', false);

                lastGoodsData = stock;
                lastCustomerId = customerId;

                buildGoodsList(stock);
            })
            .catch(console.log);
    }

    function buildGoodsList(goods) {
        if (goods.length) {
            // find taken item
            const takenGoods = Array.from(tableGoods.find('.row-stock')).map((row) => {
                return {
                    id_booking: $(row).find('#id_booking').val(),
                    id_goods: $(row).find('#id_goods').val(),
                    id_unit: $(row).find('#id_unit').val(),
                    ex_no_container: $(row).find('#ex_no_container').val(),
                };
            });
console.log(takenGoods);
            // loop through the goods list
            modalGoodsList.find('tbody').empty();
            goods.forEach((item) => {
                let isTaken = checkIfGoodsIsTaken(takenGoods, item);
                if (!isTaken) {
                    appendGoodsIntoList(item);
                }
            });
        } else {
            modalGoodsList.find('tbody').html(`
                <tr><td colspan="8">No request data available, check your stock and request outbound</td></tr>
            `);
        }
    }

    function checkIfGoodsIsTaken(takenGoods, item) {
        let isTaken = false;
        takenGoods.forEach((taken, index) => {
            const sameBooking = taken.id_booking === item.id_booking;
            const sameGoods = taken.id_goods === item.id_goods;
            const sameUnit = taken.id_unit === item.id_unit;
            const sameExNoContainer = (taken.ex_no_container || '') === (item.ex_no_container || '');
            console.log(sameBooking, sameGoods, sameUnit, sameExNoContainer, !taken['_taken'])
            if (sameBooking && sameGoods && sameUnit && sameExNoContainer && !taken['_taken']) {
                isTaken = true;
                takenGoods[index]['_taken'] = true;
            }
        });
        return isTaken;
    }

    function appendGoodsIntoList(item) {
        const order = modalGoodsList.find('tbody tr').length + 1;
        const row = `
            <tr class="${item._is_hold ? 'goods-list-hold text-muted' : ''}" ${item._is_hold ? 'title="This item is already hold"' : ''}>
                <td>${order}</td>
                <td id="no-reference-label">
                    ${item.no_reference_outbound}<br>
                    <small class="text-muted">${item.no_reference_inbound}</small>
                </td>
                <td id="goods-label">
                    ${item.goods_name}<br>
                    <small class="text-muted">${item.no_goods}</small>
                </td>
                <td id="unit-label">${item.unit}</td>
                <td id="ex-no-container-label">${item.ex_no_container || '-'}</td>
                <td id="request-quantity-label">${setCurrencyValue(Number(item.request_quantity || 0), '', ',', '.')}</td>
                <td id="realized-quantity-label">${setCurrencyValue(Number(item.qork_order_quantity || 0), '', ',', '.')}</td>
                <td id="related-request-label">${item.no_requests || '-'}</td>
                <td class="text-center sticky-col-right">
                    <input type="hidden" name="goods-data" id="goods-data" value="${encodeURIComponent(JSON.stringify(item))}">
                    <button class="btn btn-sm ${item._is_hold ? '' : 'btn-danger'} btn-take-goods" type="button" ${item._is_hold ? 'disabled' : ''}>
                        HOLD
                    </button>
                </td>
            </tr>
        `;
        modalGoodsList.find('tbody').first().append(row);
    }

    btnReloadGoods.on('click', function () {
        fetchGoodsList();
    });

    btnTakeAllGoods.on('click', function () {
        modalGoodsList.find('tbody tr:not(.goods-list-hold)').each(function (i, row) {
            modalGoodsList.selectedRow = $(row);
            inputHoldDescription.val('Request to hold')
            btnConfirmTakeGoods.click();
        });
    });

    modalGoodsList.on('click', '.btn-take-goods', function (e) {
        e.preventDefault();

        modalGoodsList.selectedRow = $(this).closest('tr');

        const goodsData = modalGoodsList.selectedRow.find('#goods-data').val();
        const goods = JSON.parse(decodeURIComponent(goodsData));

        modalTakeGoods.find('#label-goods').text(goods.goods_name);
        modalTakeGoods.find('#label-unit').text(goods.unit);

        modalTakeGoods.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalTakeGoods.on('submit', function (e) {
        e.preventDefault();

        const goodsData = modalGoodsList.selectedRow.find('#goods-data').val();
        const goods = JSON.parse(decodeURIComponent(goodsData));
        goods.hold_item_description = inputHoldDescription.val();

        appendGoodsIntoHoldItemTable(goods);
        resetStateTakenGoods();

        modalTakeGoods.modal('hide');
    });

    function resetStateTakenGoods() {
        modalGoodsList.selectedRow.remove();
        modalGoodsList.selectedRow = null;
        inputHoldDescription.val('')
    }

    function appendGoodsIntoHoldItemTable(item) {
        if (tableGoods.find('.row-placeholder').length) {
            tableGoods.find('.row-placeholder').remove();
        }

        const lastRow = tableGoods.find('tbody tr').length;
        const row = `
            <tr class="row-goods row-stock" data-id="${uniqueId()}">
                <td>${lastRow + 1}</td>
                <td id="no-reference-label">
                    ${item.no_reference_outbound}<br>
                    <small class="text-muted">${item.no_reference_inbound}</small>
                </td>
                <td id="goods-label">
                    ${item.goods_name}<br>
                    <small class="text-muted">${item.no_goods}</small>
                </td>
                <td id="unit-label">${item.unit}</td>
                <td id="description-label">${item.hold_item_description || '-'}</td>
                <td class="sticky-col-right" style="min-height: 58px">
                    <input type="hidden" name="goods[][id_upload]" id="id_upload" value="${item.id_upload_outbound}">
                    <input type="hidden" name="goods[][id_booking]" id="id_booking" value="${item.id_booking_outbound}">
                    <input type="hidden" name="goods[][id_goods]" id="id_goods" value="${item.id_goods}">
                    <input type="hidden" name="goods[][id_unit]" id="id_unit" value="${item.id_unit}">
                    <input type="hidden" name="goods[][ex_no_container]" id="ex_no_container" value="${item.ex_no_container || ''}">
                    <input type="hidden" name="goods[][quantity]" id="quantity" value="${item.request_quantity}">
                    <input type="hidden" name="goods[][description]" id="description" value="${item.hold_item_description}">
                    <input type="hidden" name="goods[][id_request_uploads]" id="id_request_uploads" value="${item.id_request_uploads}">
                    <input type="hidden" name="goods[][id_requests]" id="id_requests" value="${item.id_requests}">
                    <input type="hidden" name="goods[][no_requests]" id="no_requests" value="${item.no_requests}">
                    <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                        <i class="ion-trash-b"></i>
                    </button>
                </td>
            </tr>
        `;
        tableGoods.find('tbody').first().append(row);
        reorderIndexTableRows();
    }

    tableGoods.on('click', '.btn-remove-goods', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();

        let row = tableGoods.find('tbody tr');
        if (row.length === 0) {
            const placeholder = `<tr class="row-placeholder"><td colspan="6">No requested goods data</td></tr>`;
            tableGoods.find('tbody').html(placeholder);
        } else {
            reorderIndexTableRows();
        }
    });

    function reorderIndexTableRows() {
        tableGoods.find('tr.row-goods').each(function (index) {
            // recount header number
            $(this).children('td').first().html((index + 1).toString());

            // reorder index of inputs
            $(this).find('input[name]').each(function () {
                const pattern = new RegExp("goods[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'goods[' + index + ']');
                $(this).attr('name', attributeName);
            });
        });

        setTimeout(function () {
            setTableViewport();
        }, 300);
    }
});