$(function () {
    var tableDeliveryOrder = $('#table-delivery-order');
    var formDeliveryOrder = $('#form-delivery-order');
    var bookingSelect = formDeliveryOrder.find('#booking');
    var bookingDataWrapper = formDeliveryOrder.find('#booking-data-wrapper');

    if (bookingSelect.val() != null && bookingSelect.val() != '') {
        loadBookingData(bookingSelect.val());
    }

    bookingSelect.on('change', function () {
        loadBookingData($(this).val());
    });

    function loadBookingData(bookingId) {
        $.ajax({
            type: "GET",
            url: baseUrl + "delivery_order/ajax_get_booking_data",
            data: {id_booking: bookingId, method: $('input[name=method]').val()},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                if (data.trim() == '') {
                    bookingDataWrapper.html('<p class="lead text-danger">No goods data available</p>');
                } else {
                    bookingDataWrapper.html(data);
                    bookingDataWrapper.find(".select2").select2();
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    initializeLoaderDeliveryOrder();

    function initializeLoaderDeliveryOrder() {
        formDeliveryOrder.on('click', '.btn-take', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableSource = $(this).closest('#source-' + type + '-wrapper');
            var tableDestination = formDeliveryOrder.find('#destination-' + type + '-wrapper');

            var rowSource = tableSource.find('[data-row=' + id + ']');
            rowSource.find('input[type=hidden]').attr('disabled', false);
            rowSource.find('#quantity').attr('type', 'number');
            rowSource.find('#quantity-label').addClass('hidden');
            rowSource.find('#tonnage').attr('type', 'number');
            rowSource.find('#tonnage-label').addClass('hidden');
            rowSource.find('#volume').attr('type', 'number');
            rowSource.find('#volume-label').addClass('hidden');
            rowSource.find('.row-conversion').hide();
            tableDestination.append(rowSource);

            if (tableSource.find('tr[data-row]').length == 0) {
                tableSource.find('#placeholder').show();
            }
            if (tableDestination.find('tr[data-row]').length > 0) {
                tableDestination.find('#placeholder').hide();
            }

            $(this).text('Return').attr('class', 'btn btn-danger btn-block btn-return');
            $('#total_items').val(parseInt($('#total_items').val()) + 1);

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });

        formDeliveryOrder.on('click', '.btn-return', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableDestination = $(this).closest('#destination-' + type + '-wrapper');
            var tableSource = formDeliveryOrder.find('#source-' + type + '-wrapper');

            var rowDestination = tableDestination.find('[data-row=' + id + ']');
            rowDestination.find('input[type=hidden]').attr('disabled', true);
            rowDestination.find('#quantity').attr('type', 'hidden');
            rowDestination.find('#quantity-label').removeClass('hidden');
            rowDestination.find('#tonnage').attr('type', 'hidden');
            rowDestination.find('#tonnage-label').removeClass('hidden');
            rowDestination.find('#volume').attr('type', 'hidden');
            rowDestination.find('#volume-label').removeClass('hidden');
            rowDestination.find('.row-conversion').show();
            tableSource.append(rowDestination);

            if (tableDestination.find('tr[data-row]').length == 0) {
                tableDestination.find('#placeholder').show();
            }
            if (tableSource.find('tr[data-row]').length > 0) {
                tableSource.find('#placeholder').hide();
            }

            $(this).text('Take').attr('class', 'btn btn-primary btn-block btn-take');
            $('#total_items').val(parseInt($('#total_items').val()) - 1);

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });
    }

    function reorderTable(table) {
        table.find('tr[data-row]').not('#placeholder').not('.skip-ordering')
            .each(function (index) {
                $(this).children('td').first().html(index + 1);
            });
    }

    tableDeliveryOrder.on('click', '.btn-print-delivery-order', function (e) {
        e.preventDefault();

        var id = $(this).closest('.row-delivery-order').data('id');
        var no = $(this).closest('.row-delivery-order').data('no');
        var printTotal = $(this).closest('.row-delivery-order').data('print-total');
        var printMax = $(this).closest('.row-delivery-order').data('print-max');
        var urlPrint = $(this).attr('href');

        var modalPrint = $('#modal-confirm-print-delivery-order');
        modalPrint.find('#print-title').text(no);
        modalPrint.find('#print-total').text((printTotal + 1) + 'x');
        modalPrint.find('#print-max').text(printMax + 'x');
        modalPrint.find('input[name=id]').val(id);
        modalPrint.find('form').attr('action', urlPrint);

        var buttonSubmitPrint = modalPrint.find('button[type=submit]');
        if (printTotal >= printMax) {
            modalPrint.find('#print-subtitle').hide();
            buttonSubmitPrint
                .text('Reaching Maximum of ' + printMax + 'X Print')
                .prop('disabled', true);
        } else {
            modalPrint.find('#print-subtitle').show();
            buttonSubmitPrint
                .text('Print Now')
                .prop('disabled', false);
        }

        modalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableDeliveryOrder.on('click', '.btn-update-max-print', function (e) {
        e.preventDefault();

        var idDeliveryOrder = $(this).closest('.row-delivery-order').data('id');
        var labelDeliveryOrder = $(this).closest('.row-delivery-order').data('no');
        var labelTotalPrint = $(this).closest('.row-delivery-order').data('print-total');
        var labelTotalPrintMax = $(this).closest('.row-delivery-order').data('print-max');
        var urlUpdate = $(this).attr('href');

        var modalUpdateTotalPrint = $('#modal-update-max-print');
        modalUpdateTotalPrint.find('form').attr('action', urlUpdate);
        modalUpdateTotalPrint.find('input[id=id]').val(idDeliveryOrder);
        modalUpdateTotalPrint.find('#delivery-order-title').text(labelDeliveryOrder);
        modalUpdateTotalPrint.find('#delivery-order-print').text(labelTotalPrint + ' x print');
        modalUpdateTotalPrint.find('#print_max')
            .attr('min', labelTotalPrint)
            .val(labelTotalPrintMax);

        modalUpdateTotalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableDeliveryOrder.on('click', '.btn-delete-delivery-order', function (e) {
        e.preventDefault();

        var idDeliveryOrder = $(this).data('id');
        var labelDeliveryOrder = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteDeliveryOrder = $('#modal-delete-delivery-order');
        modalDeleteDeliveryOrder.find('form').attr('action', urlDelete);
        modalDeleteDeliveryOrder.find('input[name=id]').val(idDeliveryOrder);
        modalDeleteDeliveryOrder.find('#delivery-order-title').text(labelDeliveryOrder);

        modalDeleteDeliveryOrder.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});