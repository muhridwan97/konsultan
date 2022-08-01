$(function () {

    var invoiceItemTemplate = $('#row-invoice-item-table-template').html();
    var modalInvoiceEditor = $('#modal-invoice-editor');
    var activeRow = null;

    $(document).on('click', '#table-invoice-editor .btn-add-invoice-table', function (e) {
        e.preventDefault();

        modalInvoiceEditor.find('#item').val('');
        modalInvoiceEditor.find('#unit').val('');
        modalInvoiceEditor.find('#type').val('').trigger("change");
        modalInvoiceEditor.find('#quantity').val('1');
        modalInvoiceEditor.find('#price').val('');
        modalInvoiceEditor.find('#multiplier').val('1');
        modalInvoiceEditor.find('#description').val('');
        modalInvoiceEditor.find('#item_summary').val('');
        modalInvoiceEditor.find('#btn-submit').text('Add Invoice Item')
            .addClass('new').removeClass('update');

        modalInvoiceEditor.find('#btn-delete').hide();

        modalInvoiceEditor.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on('click', '#table-invoice-editor .btn-edit-invoice-table', function (e) {
        e.preventDefault();

        activeRow = $(this);

        var item = $(this).closest('tr').find('#item_name').val();
        var unit = $(this).closest('tr').find('#unit').val();
        var type = $(this).closest('tr').find('#type').val();
        var quantity = $(this).closest('tr').find('#quantity').val();
        var price = $(this).closest('tr').find('#unit_price').val();
        var multiplier = $(this).closest('tr').find('#unit_multiplier').val();
        var description = $(this).closest('tr').find('#description').val();
        var itemSummary = $(this).closest('tr').find('#item_summary').val();

        modalInvoiceEditor.find('#item').val(item);
        modalInvoiceEditor.find('#unit').val(unit);
        modalInvoiceEditor.find('#type').val(type).trigger("change");
        modalInvoiceEditor.find('#quantity').val(quantity);
        modalInvoiceEditor.find('#price').val(setCurrencyValue(price, 'Rp. '));
        modalInvoiceEditor.find('#multiplier').val(multiplier);
        modalInvoiceEditor.find('#description').val(description);
        modalInvoiceEditor.find('#item_summary').val(itemSummary);
        modalInvoiceEditor.find('#btn-submit').text('Update Invoice Item')
            .addClass('update').removeClass('new');

        modalInvoiceEditor.find('#btn-delete').show();

        modalInvoiceEditor.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalInvoiceEditor.find('#btn-submit').on('click', function (e) {
        e.preventDefault();

        var item = modalInvoiceEditor.find('#item').val();
        var unit = modalInvoiceEditor.find('#unit').val();
        var type = modalInvoiceEditor.find('#type').val();
        var quantity = modalInvoiceEditor.find('#quantity').val();
        var price = modalInvoiceEditor.find('#price').val();
        var multiplier = modalInvoiceEditor.find('#multiplier').val();
        var description = modalInvoiceEditor.find('#description').val();
        var itemSummary = modalInvoiceEditor.find('#item_summary').val();
        var total = Math.floor(getCurrencyValue(price)) * quantity * multiplier;

        var sourceRow = null;
        if ($(this).hasClass('update')) {
            sourceRow = activeRow.closest('tr');
        } else {
            var invoiceEditor = $('#table-invoice-editor');
            sourceRow = $.parseHTML(invoiceItemTemplate);
        }

        $(sourceRow).find('#item_name').val(item);
        $(sourceRow).find('.label-item').text(item);
        $(sourceRow).find('#unit').val(unit);
        $(sourceRow).find('.label-unit').text(unit);
        $(sourceRow).find('#type').val(type);
        $(sourceRow).find('.label-type').text(type);
        $(sourceRow).find('#quantity').val(quantity);
        $(sourceRow).find('.label-quantity').text(quantity.replace(/\./, ','));
        $(sourceRow).find('#unit_price').val(Math.floor(getCurrencyValue(price)));
        $(sourceRow).find('.label-price').text(price);
        $(sourceRow).find('#unit_multiplier').val(multiplier);
        $(sourceRow).find('.label-multiplier').text(multiplier);
        $(sourceRow).find('#description').val(description);
        $(sourceRow).find('.label-description').text(description == '' ? 'No description' : description);
        $(sourceRow).find('#item_summary').val(itemSummary);
        $(sourceRow).find('.label-item-summary').text(itemSummary == '' ? 'No item summary' : itemSummary);
        $(sourceRow).find('.label-total').text(setCurrencyValue(total, 'Rp. ' + (total < 0 ? '-' : '')));

        if ($(this).hasClass('new')) {
            // insert before total, before tax (if exist), before stamp (if exist)
            var lastRow = invoiceEditor.find('tbody').children('tr').last();
            if (invoiceEditor.find('.tax').length) {
                lastRow = invoiceEditor.find('.tax');
            } else if (invoiceEditor.find('.stamp').length) {
                lastRow = invoiceEditor.find('.stamp');
            }
            $(sourceRow).insertBefore(lastRow);
        }

        calculateInvoiceTaxStamp();
        calculateTotalPrice();
        reorderItem();
        modalInvoiceEditor.modal('hide');
    });

    modalInvoiceEditor.find('#btn-delete').on('click', function (e) {
        e.preventDefault();

        activeRow.closest('tr').remove();
        calculateInvoiceTaxStamp();
        calculateTotalPrice();
        reorderItem();
        modalInvoiceEditor.modal('hide');
    });

    function reorderItem() {
        var invoiceEditor = $('#table-invoice-editor');
        invoiceEditor.find('tbody tr').not('.skip-ordering').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
    }

    function calculateInvoiceTaxStamp() {
        var invoiceEditor = $('#table-invoice-editor');
        var totalPriceBeforeTax = 0;
        invoiceEditor.find('tbody tr.tax').prevAll().each(function () {
            var total = Number(getCurrencyValue($(this).find('.label-total').text()));
            if (!isNaN(total)) {
                totalPriceBeforeTax += total;
            }
        });

        var tax = Math.floor(0.1 * totalPriceBeforeTax);
        if (tax > 0) {
            if (invoiceEditor.find('.tax').length) {
                invoiceEditor.find('.tax #unit_price').val(tax);
                invoiceEditor.find('.tax .label-price').text(setCurrencyValue(tax, 'Rp. '));
                invoiceEditor.find('.tax .label-total').text(setCurrencyValue(tax, 'Rp. '));
            } else {
                var sourceRow = $.parseHTML(invoiceItemTemplate);
                $(sourceRow).addClass('tax');
                $(sourceRow).find('#item_name').val('PPN (10%)');
                $(sourceRow).find('.label-item').text('PPN (10%)');
                $(sourceRow).find('#unit').val('OTHER');
                $(sourceRow).find('.label-unit').text('OTHER');
                $(sourceRow).find('#type').val('OTHER');
                $(sourceRow).find('.label-type').text('OTHER');
                $(sourceRow).find('#quantity').val(1);
                $(sourceRow).find('.label-quantity').text('1');
                $(sourceRow).find('#unit_price').val(tax);
                $(sourceRow).find('.label-price').text(tax);
                $(sourceRow).find('#unit_multiplier').val(1);
                $(sourceRow).find('.label-multiplier').text(1);
                $(sourceRow).find('#description').val('');
                $(sourceRow).find('.label-description').text('No description');
                $(sourceRow).find('.label-item-summary').text('No item summary');
                $(sourceRow).find('.label-total').text(setCurrencyValue(tax, 'Rp. '));
                $(sourceRow).find('.btn-edit-invoice-table').remove();

                var lastRow = invoiceEditor.find('tbody').children('tr').last();
                if (invoiceEditor.find('.stamp').length) {
                    lastRow = invoiceEditor.find('.stamp');
                }
                $(sourceRow).insertBefore(lastRow);
            }
        } else {
            invoiceEditor.find('.tax').remove();
        }

        var stamp = 0;
        if (totalPriceBeforeTax > 5000000) {
            stamp = 10000;
        }/* else if (totalPriceBeforeTax > 1000000) {
            stamp = 6000;
        } else if (totalPriceBeforeTax >= 250000) {
            stamp = 3000;
        }*/

        if (stamp > 0) {
            if (invoiceEditor.find('.stamp').length) {
                invoiceEditor.find('.stamp #unit_price').val(stamp);
                invoiceEditor.find('.stamp .label-price').text(stamp);
                invoiceEditor.find('.stamp .label-total').text(setCurrencyValue(stamp, 'Rp. '));
            } else {
                var sourceRow = $.parseHTML(invoiceItemTemplate);
                $(sourceRow).addClass('stamp');
                $(sourceRow).find('#item_name').val('Materai');
                $(sourceRow).find('.label-item').text('Materai');
                $(sourceRow).find('#unit').val('OTHER');
                $(sourceRow).find('.label-unit').text('OTHER');
                $(sourceRow).find('#type').val('OTHER');
                $(sourceRow).find('.label-type').text('OTHER');
                $(sourceRow).find('#quantity').val(1);
                $(sourceRow).find('.label-quantity').text('1');
                $(sourceRow).find('#unit_price').val(stamp);
                $(sourceRow).find('.label-price').text(stamp);
                $(sourceRow).find('#unit_multiplier').val(1);
                $(sourceRow).find('.label-multiplier').text(1);
                $(sourceRow).find('#description').val('');
                $(sourceRow).find('.label-description').text('No description');
                $(sourceRow).find('.label-item-summary').text('No item summary');
                $(sourceRow).find('.label-total').text(setCurrencyValue(stamp, 'Rp. '));
                $(sourceRow).find('.btn-edit-invoice-table').remove();

                $(sourceRow).insertBefore(invoiceEditor.find('tbody').children('tr').last());
            }
        } else {
            invoiceEditor.find('.stamp').remove();
        }
    }

    function calculateTotalPrice() {
        var invoiceEditor = $('#table-invoice-editor');
        var totalPrice = 0;
        invoiceEditor.find('tbody tr').not('.skip-ordering').each(function () {
            var total = Number(getCurrencyValue($(this).find('.label-total').text()));
            if (!isNaN(total)) {
                totalPrice += total;
            }
        });
        invoiceEditor.find('.label-total-price').text(setCurrencyValue(totalPrice, 'Rp. '));
    }

});