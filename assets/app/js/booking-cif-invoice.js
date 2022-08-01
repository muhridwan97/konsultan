$(function () {
    var tableBookingCif = $('#table-booking-cif.table-ajax');
    var controlTemplate = $('#control-booking-cif-template').html();
    tableBookingCif.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search invoice"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'booking-cif-invoice/booking-cif-invoice-data',
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
            {data: 'no_reference'},
            {data: 'category'},
            {data: 'total_item'},
            {data: 'total_price_value'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-total-item'],
            render: function (data, type, full) {
                return setNumeric(data) + ' item / ' + setNumeric(full.total_item_quantity) + ' quantity';
            }
        }, {
            targets: ['type-numeric'],
            render: function (data) {
                return setNumeric(data);
            }
        }, {
            targets: ['type-currency'],
            render: function (data, type, full) {
                return full.currency_from + ' ' + setNumeric(Number(data).toFixed(2))
                    + '<br><small class="text-muted">' + full.currency_to + ' ' + setNumeric(Number(data * full.exchange_value).toFixed(2)) + '</small>';
            }
        }, {
            targets: ['type-action'],
            orderable: false,
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{id_booking}}/g, full.id_booking)
                    .replace(/{{no_reference}}/g, full.no_reference);
                control = $.parseHTML(control);

                return $('<div />').append($(control).clone()).html();
            }
        }]
    });


    const formBookingCifInvoice = $('#form-booking-cif-invoice');
    const selectBooking = formBookingCifInvoice.find('#booking');
    const selectCurrencyFrom = formBookingCifInvoice.find('#currency_from');
    const selectCurrencyTo = formBookingCifInvoice.find('#currency_to');
    const inputExchangeDate = formBookingCifInvoice.find('#exchange_date');
    const inputExchangeValue = formBookingCifInvoice.find('#exchange_value');
    const bookingReferenceWrapper = formBookingCifInvoice.find('#booking-reference-wrapper');

    selectBooking.on('change', function () {
        const booking = $(this).select2('data')[0];
        if (booking.category === 'OUTBOUND') {
            tableCIFInvoice.find('.item-value-column').show();
            formBookingCifInvoice.find('.inbound-only input').val('');
            formBookingCifInvoice.find('.inbound-only').find('select').val('').trigger('change');
            formBookingCifInvoice.find('.inbound-only').find('input').val('');
            formBookingCifInvoice.find('.inbound-only').find('#exchange_date').prop('required', false);
            formBookingCifInvoice.find('.inbound-only').find('#exchange_value').prop('required', false);
            formBookingCifInvoice.find('.inbound-only').hide();

            formBookingCifInvoice.find('.outbound-only').find('input').val('').prop('required', true);
            formBookingCifInvoice.find('.outbound-only').show();

            fetch(baseUrl + 'booking-cif-invoice/ajax_get_booking_reference?id_booking=' + booking.id_booking)
                .then(data => data.json())
                .then(result => {
                    bookingReferenceWrapper.show();
                    bookingReferenceWrapper.find('#ref-booking-type').text(result.booking.booking_type);
                    bookingReferenceWrapper.find('#ref-no-booking').text(result.booking.no_booking);
                    bookingReferenceWrapper.find('#ref-no-reference').text(result.booking.no_reference);
                    if (result.cif) {
                        bookingReferenceWrapper.find('#ref-total-item').text(setNumeric(result.cif.total_item));
                        bookingReferenceWrapper.find('#ref-subtotal').text(setNumeric(result.cif.subtotal));
                        bookingReferenceWrapper.find('#ref-total-price').text(setNumeric(result.cif.total_price));
                        formBookingCifInvoice.find('label[for="ndpbm"]').text(`NDPBM (${result.cif.currency_from}-IDR)`);
                    } else {
                        bookingReferenceWrapper.find('#ref-total-item').text('-');
                        bookingReferenceWrapper.find('#ref-subtotal').text('-');
                        bookingReferenceWrapper.find('#ref-total-price').text('-');
                        formBookingCifInvoice.find('label[for="ndpbm"]').text(`NDPBM (IDR)`);
                    }
                })
                .catch(console.log);
        } else {
            tableCIFInvoice.find('.item-value-column').hide();
            formBookingCifInvoice.find('.inbound-only').show();
            formBookingCifInvoice.find('.inbound-only').find('#exchange_date').prop('required', true);
            formBookingCifInvoice.find('.inbound-only').find('#exchange_value').prop('required', true);

            formBookingCifInvoice.find('.outbound-only').find('input').val('').prop('required', false);
            formBookingCifInvoice.find('.outbound-only').hide();

            bookingReferenceWrapper.hide();
        }
        tableCIFInvoice.find('tbody tr').not('.row-placeholder').empty();
        tableCIFInvoice.find('.row-placeholder').show();

        tableCIFInvoice.find('#discount').val('');
        tableCIFInvoice.find('#freight').val('');
        tableCIFInvoice.find('#insurance').val('');
        tableCIFInvoice.find('#handling').val('');
        tableCIFInvoice.find('#other').val('');
        calculateTotalAndReorder();
    });

    selectCurrencyFrom.on('change', checkRate);
    selectCurrencyTo.on('change', checkRate);
    inputExchangeDate.datepicker().on('changeDate', checkRate);

    inputExchangeValue.on('input', function () {
        $(this).data('touch', 1);
    });

    function checkRate() {
        const from = selectCurrencyFrom.val();
        const to = selectCurrencyTo.val();
        let dateStart = moment(inputExchangeDate.val()).format('YYYY-MM-DD');
        if (!dateStart || dateStart === 'Invalid date') {
            dateStart = '';
        } else {
            if (moment(inputExchangeDate.val()).format('d') === '6') { // exchange rate close on sunday
                dateStart = moment(dateStart).subtract(1, 'days').format('YYYY-MM-DD');
            }
        }
        if (from && to && dateStart && (inputExchangeValue.val().trim() === '' || inputExchangeValue.data('touch') === 0)) {
            inputExchangeValue.prop('readonly', true).val(`Fetching rate ${from} to ${to}...`);
            inputExchangeValue.data('touch', 0);

            const dateEnd = moment(dateStart).add(1, 'days').format('YYYY-MM-DD');
            //https://api.exchangeratesapi.io/history?start_at=2018-01-01&end_at=2018-01-02&symbols=IDR&base=USD
            fetch(`https://api.exchangeratesapi.io/history?start_at=${dateStart}&end_at=${dateEnd}&symbols=${to}&base=${from}`)
                .then(data => data.json())
                .then(result => {
                    if (result && result.rates) {
                        const rate = result.rates[Object.keys(result.rates)[0]][to];
                        inputExchangeValue.val(setNumeric(rate));
                    } else {
                        inputExchangeValue.val('0');
                    }
                    inputExchangeValue.prop('readonly', false);
                })
                .catch(error => {
                    console.log(error);
                    inputExchangeValue.prop('readonly', false).val('');
                });
        }
    }


    const tableCIFInvoice = $('#table-cif-invoice');
    const btnAddItem = $('#btn-add-item');
    const modalGoodsInput = $('#modal-goods-input');
    const modalStockGoods = $('#modal-stock-goods');
    const modalTakeStockGoods = $('#modal-take-stock-goods');

    let activeRowGoodsTake = null;

    // calculate when repopulate form from submission
    calculateTotalAndReorder();

    btnAddItem.on('click', function () {
        const selectedBooking = selectBooking.select2('data')[0];
        if (selectedBooking && selectedBooking.category === 'OUTBOUND') {
            modalStockGoods.find('#label-customer').text(selectedBooking.customer_name);
            modalStockGoods.find('#label-reference').text(selectedBooking.no_reference_inbound);
            modalStockGoods.find('tbody').html(`
                <tr><td colspan="10">Fetching stock goods...</td></tr>
            `);

            fetch(baseUrl + 'booking-cif-invoice/ajax-get-stock-booking?booking=' + selectedBooking.id_booking)
                .then(result => result.json())
                .then(stock => {
                    if (stock.goods) {
                        modalStockGoods.find('tbody').empty();

                        // calculate rest of stock by subtracted stock with taken
                        // first, get taken data from taken table stock
                        const takenStock = Array.from(tableCIFInvoice.find('.row-stock')).map(row => {
                            return {
                                goods_name: $(row).find('#goods_name').val(),
                                quantity: $(row).find('#quantity').val(),
                                weight: $(row).find('#weight').val(),
                                gross_weight: $(row).find('#gross_weight').val(),
                                volume: $(row).find('#volume').val(),
                                total_item_value: $(row).find('#total_item_value').val(),
                                id_booking_cif_invoice_detail: $(row).find('#id_booking_cif_invoice_detail').val(),
                            };
                        });

                        // compare with fetched stock, loop through and subtract with stock, check reference id
                        let order = 1;
                        stock.goods.forEach((goods, index) => {
                            let leftQuantity = goods.stock_quantity;
                            let leftWeight = goods.stock_weight;
                            let leftGrossWeight = goods.stock_gross_weight;
                            let leftVolume = goods.stock_volume;
                            let leftTotalItemValue = goods.total_item_value;

                            takenStock.forEach((taken, index) => {
                                if (taken.id_booking_cif_invoice_detail === goods.id_booking_cif_invoice_detail) {
                                    leftQuantity -= taken.quantity;
                                    leftWeight -= taken.weight;
                                    leftGrossWeight -= taken.gross_weight;
                                    leftVolume -= taken.volume;
                                    leftTotalItemValue -= taken.total_item_value;
                                }
                            });

                            leftQuantity = roundVal(leftQuantity);
                            leftWeight = roundVal(leftWeight);
                            leftGrossWeight = roundVal(leftGrossWeight);
                            leftVolume = roundVal(leftVolume);

                            // update goods data
                            // this is important, we clone the object so the stock data is not changed
                            let itemData = {...goods};
                            itemData.stock_quantity = leftQuantity;
                            itemData.stock_weight = leftWeight;
                            itemData.stock_gross_weight = leftGrossWeight;
                            itemData.stock_volume = leftVolume;
                            itemData.total_price = leftQuantity * goods.price;

                            if (leftQuantity > 0) {
                                leftTotalItemValue = leftQuantity * goods.total_item_value / goods.quantity;
                                const row = `
                                    <tr>
                                        <td>${order++}</td>
                                        <td>${goods.goods_name}</td>
                                        <td id="quantity-label">${setNumeric(leftQuantity)}</td>
                                        <td id="weight-label">${setNumeric(leftWeight)}</td>
                                        <td id="gross-weight-label">${setNumeric(leftGrossWeight)}</td>
                                        <td id="volume-label">${setNumeric(leftVolume)}</td>
                                        <td>${setNumeric(goods.price)}</td>
                                        <td id="total-price-label">${setNumeric(goods.price * leftQuantity)}</td>
                                        <td id="total-item-value-label">${setNumeric(Number(leftTotalItemValue).toFixed(2))}</td>
                                        <td class="text-center sticky-col-right">
                                            <input type="hidden" id="goods-data" value="${encodeURIComponent(JSON.stringify(itemData))}">
                                            <button class="btn btn-sm btn-primary btn-take-goods" type="button">
                                                TAKE
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                modalStockGoods.find('tbody').first().append(row);
                            }
                        });
                    } else {
                        modalStockGoods.find('tbody').html(`
                            <tr><td colspan="10">No data available</td></tr>
                        `);
                    }
                })
                .catch(console.log);

            modalStockGoods.modal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            modalGoodsInput.find('#goods_name').val('');
            modalGoodsInput.find('#quantity').val('');
            modalGoodsInput.find('#weight').val('');
            modalGoodsInput.find('#gross_weight').val('');
            modalGoodsInput.find('#volume').val('');
            modalGoodsInput.find('#price').val('');
            modalGoodsInput.find('#price_type').val('TOTAL').trigger('change');
            modalGoodsInput.find('#description').val('');
            modalGoodsInput.modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    });

    modalGoodsInput.find('form').on('submit', function (e) {
        e.preventDefault();

        const quantity = getCurrencyValue(modalGoodsInput.find('#quantity').val());
        let price = getCurrencyValue(modalGoodsInput.find('#price').val());
        if (modalGoodsInput.find('#price_type').val() == 'TOTAL') {
            if (quantity <= 0) {
                price = 0;
            } else {
                price = price / quantity;
            }
        }

        addGoods({
            'goods_name': modalGoodsInput.find('#goods_name').val(),
            'quantity': quantity,
            'weight': getCurrencyValue(modalGoodsInput.find('#weight').val()),
            'gross_weight': getCurrencyValue(modalGoodsInput.find('#gross_weight').val()),
            'volume': getCurrencyValue(modalGoodsInput.find('#volume').val()),
            'price': price,
            'description': modalGoodsInput.find('#description').val(),
        });
        calculateTotalAndReorder();

        modalGoodsInput.modal('hide');
    });

    function addGoods(goods, fromStock = false) {
        // remove placeholder if exist
        tableCIFInvoice.find('.row-placeholder').hide();

        // create new record and hidden input of table
        const lastRow = tableCIFInvoice.find('tbody tr').not('.row-placeholder').length;
        const row = `
            <tr class="${fromStock ? 'row-stock' : ''}">
                <td>${lastRow + 1}</td>
                <td>${goods.goods_name}</td>
                <td>${setNumeric(goods.quantity)}</td>
                <td>${setNumeric(goods.weight)}</td>
                <td>${setNumeric(goods.gross_weight)}</td>
                <td>${setNumeric(goods.volume)}</td>
                <td>${setNumeric(goods.price)}</td>
                <td>${setNumeric(goods.price * goods.quantity)}</td>
                <td class="item-value-column label-item-value">${setNumeric(goods.total_item_value || 0)}</td>
                <td>
                    <input type="hidden" name="goods[][goods_name]" id="goods_name" value="${goods.goods_name}">
                    <input type="hidden" name="goods[][quantity]" id="quantity" value="${goods.quantity}">
                    <input type="hidden" name="goods[][weight]" id="weight" value="${goods.weight}">
                    <input type="hidden" name="goods[][gross_weight]" id="gross_weight" value="${goods.gross_weight}">
                    <input type="hidden" name="goods[][volume]" id="volume" value="${goods.volume}">
                    <input type="hidden" name="goods[][price]" id="price" value="${goods.price}">
                    <input type="hidden" name="goods[][description]" id="description" value="${goods.description}">
                    <input type="hidden" name="goods[][id_booking_cif_invoice_detail]" id="id_booking_cif_invoice_detail" value="${goods.id_booking_cif_invoice_detail || ''}">
                    <input type="hidden" name="goods[][total_item_value]" id="total_item_value" value="${goods.total_item_value || 0}">
                    <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                        <i class="ion-trash-b"></i>
                    </button>
                </td>
            </tr>
        `;
        tableCIFInvoice.find('tbody').append(row);
        if(selectBooking.select2('data')[0]) {
            if(selectBooking.select2('data')[0].category === 'INBOUND') {
                tableCIFInvoice.find('.item-value-column').hide();
            }
        }
    }

    modalStockGoods.on('click', '.btn-take-goods', function () {
        activeRowGoodsTake = $(this).closest('tr');
        const goodsData = JSON.parse(decodeURIComponent(activeRowGoodsTake.find('#goods-data').val()));
        modalTakeStockGoods.find('#label-goods').text(goodsData.goods_name);
        modalTakeStockGoods.find('#quantity').val(setNumeric(goodsData.stock_quantity));
        modalTakeStockGoods.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    function roundVal(value, precision = 3) {
        const multiplier = (function () {
            if (precision === 3) return 1000;

            let digit = '1';
            for (let i = 0; i < precision; i++) {
                digit += '0';
            }
            return Number(digit);
        })();
        return Math.round(value * multiplier) / multiplier;
    }

    modalTakeStockGoods.find('#btn-take-goods-stock').on('click', function () {
        const goodsData = JSON.parse(decodeURIComponent(activeRowGoodsTake.find('#goods-data').val()));
        const quantityTake = getCurrencyValue(modalTakeStockGoods.find('#quantity').val());

        const goodsTake = {
            'goods_name': goodsData.goods_name,
            'quantity': quantityTake,
            'weight': quantityTake * goodsData.weight / goodsData.quantity,
            'gross_weight': quantityTake * goodsData.gross_weight / goodsData.quantity,
            'volume': quantityTake * goodsData.volume / goodsData.quantity,
            'price': goodsData.price,
            'total_item_value': quantityTake * goodsData.total_item_value / goodsData.quantity,
            'description': '',
            'id_booking_cif_invoice_detail': goodsData.id_booking_cif_invoice_detail,
        };
        addGoods(goodsTake, true);

        const leftQuantity = roundVal(goodsData.stock_quantity - goodsTake.quantity);
        const leftWeight = roundVal(goodsData.stock_weight - goodsTake.weight);
        const leftGrossWeight = roundVal(goodsData.stock_gross_weight - goodsTake.gross_weight);
        const leftVolume = roundVal(goodsData.stock_volume - goodsTake.volume);
        const leftTotalItemValue = roundVal(goodsData.total_item_value - goodsTake.total_item_value);

        // update left quantity data in stock
        if (leftQuantity <= 0) {
            activeRowGoodsTake.remove();
            activeRowGoodsTake = null;
        } else {
            goodsData.stock_quantity = leftQuantity;
            goodsData.stock_weight = leftWeight;
            goodsData.stock_gross_weight = leftGrossWeight;
            goodsData.stock_volume = leftVolume;
            goodsData.total_price = goodsData.price * leftQuantity;

            activeRowGoodsTake.find('#goods-data').val(encodeURIComponent(JSON.stringify(goodsData)));
            activeRowGoodsTake.find('#quantity-label').text(setNumeric(leftQuantity || 0));
            activeRowGoodsTake.find('#weight-label').text(setNumeric(leftWeight || 0));
            activeRowGoodsTake.find('#gross-weight-label').text(setNumeric(leftGrossWeight || 0));
            activeRowGoodsTake.find('#volume-label').text(setNumeric(leftVolume || 0));
            activeRowGoodsTake.find('#total-price-label').text(setNumeric(goodsData.price * goodsData.stock_quantity));
            activeRowGoodsTake.find('#total-item-value-label').text(setNumeric(leftTotalItemValue));
        }

        calculateTotalAndReorder();
        modalTakeStockGoods.modal('hide');
    });

    tableCIFInvoice.on('click', '.btn-remove-goods', function () {
        $(this).closest('tr').remove();

        if (tableCIFInvoice.find('tbody tr').not('.row-placeholder').length === 0) {
            tableCIFInvoice.find('.row-placeholder').show();
        }

        calculateTotalAndReorder();
    });

    tableCIFInvoice.find('#discount, #freight, #insurance, #handling, #other').on('input', calculateTotalAndReorder);

    function calculateTotalAndReorder() {
        tableCIFInvoice.find('tbody tr').not('.row-placeholder').each(function (index) {
            // recount header number
            $(this).children('td').first().html((index + 1).toString());

            // reorder index of inputs
            $(this).find('input[name]').each(function () {
                const pattern = new RegExp("goods[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'goods[' + index + ']');
                $(this).attr('name', attributeName);
            });
        });

        let totalQuantity = 0;
        let totalWeight = 0;
        let totalGross = 0;
        let totalVolume = 0;
        let subtotalPrice = 0;
        let discount = getCurrencyValue(tableCIFInvoice.find('#discount').val());
        let freight = getCurrencyValue(tableCIFInvoice.find('#freight').val());
        let insurance = getCurrencyValue(tableCIFInvoice.find('#insurance').val());
        let handling = getCurrencyValue(tableCIFInvoice.find('#handling').val());
        let other = getCurrencyValue(tableCIFInvoice.find('#other').val());
        let totalItemValue = 0;

        tableCIFInvoice.find('tbody tr').not('.row-placeholder').each(function () {
            totalQuantity += Number($(this).find('#quantity').val());
            totalWeight += Number($(this).find('#weight').val());
            totalGross += Number($(this).find('#gross_weight').val());
            totalVolume += Number($(this).find('#volume').val());
            subtotalPrice += Number($(this).find('#quantity').val()) * Number($(this).find('#price').val());
            totalItemValue += Number($(this).find('#total_item_value').val());
        });
        let totalPrice = subtotalPrice - discount + freight + insurance + handling + other;

        tableCIFInvoice.find('#label-total-quantity').text(setNumeric(totalQuantity));
        tableCIFInvoice.find('#label-total-weight').text(setNumeric(totalWeight));
        tableCIFInvoice.find('#label-total-gross-weight').text(setNumeric(totalGross));
        tableCIFInvoice.find('#label-total-volume').text(setNumeric(totalVolume));
        tableCIFInvoice.find('#label-subtotal-price').text(setNumeric(subtotalPrice));
        tableCIFInvoice.find('#label-total-price').text(setNumeric(totalPrice));
        tableCIFInvoice.find('#label-total-item-value').text(setNumeric(totalItemValue));
    }

});