$(function () {

    var formInvoice = $('#form-invoice');
    var selectInvoiceStatus = formInvoice.find('#status');
    var selectInvoiceSetting = formInvoice.find('#setting');
    var selectInvoiceType = formInvoice.find('#type');
    var selectCustomer = formInvoice.find('#customer');
    var selectHandling = formInvoice.find('#handling');
    var selectWorkOrder = formInvoice.find('#work_order');
    var selectBookingIn = formInvoice.find('#booking_in');
    var selectBookingInvoice = formInvoice.find('#booking_invoice');
    var chargeInvoiceWrapper = $('#charge-invoice-wrapper');

    var formInvoiceMode = $('#form-invoice-mode');
    var formInvoiceModeFull = formInvoiceMode.find('#type-booking-full');
    var formInvoiceModeFullExtension = formInvoiceMode.find('#type-booking-full-extension');
    var formInvoiceModeHandling = formInvoiceMode.find('#type-handling');
    var formInvoiceModeWorkOrder = formInvoiceMode.find('#type-work-order');
    var formInvoiceModeCustom = formInvoiceMode.find('#type-custom');

    selectInvoiceStatus.on('change', function () {
        if ($(this).val() == 'DRAFT') {
            formInvoiceModeFull.find('#outbound_date').attr('disabled', false);
            formInvoiceModeFullExtension.find('#outbound_date').attr('disabled', false);
        } else {
            formInvoiceModeFull.find('#outbound_date').attr('disabled', true).datepicker("setDate", new Date());
            formInvoiceModeFullExtension.find('#outbound_date').attr('disabled', true).datepicker("setDate", new Date());
        }
    });

    selectInvoiceType.on('change', function () {
        formInvoiceMode.find('> div').hide();
        chargeInvoiceWrapper.empty();

        formInvoiceMode.find('input').attr('disabled', true);
        formInvoiceMode.find('select').attr('disabled', true);

        switch ($(this).val()) {
            case 'BOOKING DEPO':
                formInvoiceModeFull.show();
                formInvoiceModeFull.find('input').attr('disabled', false);
                formInvoiceModeFull.find('select').attr('disabled', false);
                selectBookingIn.attr('disabled', false);
                formInvoiceModeFull.find('#outbound_date').attr('disabled', true);
                formInvoiceModeFull.find('#outbound-wrapper').hide();
                formInvoiceModeFull.find('#booking-wrapper').attr('class', 'col-md-12');
                break;
            case 'BOOKING FULL':
                formInvoiceModeFull.show();
                formInvoiceModeFull.find('input').attr('disabled', false);
                formInvoiceModeFull.find('select').attr('disabled', false);
                selectBookingIn.attr('disabled', false);
                if (selectInvoiceStatus.val() !== 'DRAFT') {
                    formInvoiceModeFull.find('#outbound_date').attr('disabled', true).datepicker("setDate", new Date());
                }
                formInvoiceModeFull.find('#outbound-wrapper').show();
                formInvoiceModeFull.find('#booking-wrapper').attr('class', 'col-md-6');
                break;
            case 'BOOKING FULL EXTENSION':
                formInvoiceModeFullExtension.show();
                formInvoiceModeFullExtension.find('input').attr('disabled', false);
                formInvoiceModeFullExtension.find('select').attr('disabled', false);
                if (selectInvoiceStatus.val() !== 'DRAFT') {
                    formInvoiceModeFullExtension.find('#outbound_date').attr('disabled', true).datepicker("setDate", new Date());
                }
                break;
            case 'HANDLING':
                formInvoiceModeHandling.show();
                selectHandling.attr('disabled', false);
                break;
            case 'WORK ORDER':
                formInvoiceModeWorkOrder.show();
                selectWorkOrder.attr('disabled', false);
                break;
            default:
                formInvoiceModeCustom.show();
                formInvoiceModeCustom.find('input').attr('disabled', false);
                formInvoiceModeCustom.find('select').attr('disabled', false);
                break;
        }
    });

    /* MANUAL INVOICE */
    var tableInvoiceItem = $('#table-invoice-item');
    var totalColumns = tableInvoiceItem.first().find('th').length;
    var tableDetailInvoiceItem = tableInvoiceItem.find('tbody');
    var buttonAddInvoiceItem = formInvoiceModeCustom.find('#btn-add-invoice-item');
    var invoiceDetailTemplate = $('#row-invoice-item-template').html();

    buttonAddInvoiceItem.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailInvoiceItem.empty();
        }

        tableDetailInvoiceItem.append(invoiceDetailTemplate);
        reorderItem();
    });

    tableDetailInvoiceItem.on('click', '.btn-remove-invoice-item', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInvoiceItem.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Invoice Item</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInvoiceItem.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('.select2').select2();
    }

    function getTotalItem() {
        return parseInt(tableDetailInvoiceItem.find('tr.row-invoice-item').length);
    }

    /* END OF MANUAL INVOICE */


    /* INVOICE SOURCE DATA */
    function fetchBookingData(selectBooking, type) {
        selectBooking.empty().append($('<option>')).prop("disabled", true);

        $.ajax({
            type: "GET",
            url: baseUrl + "invoice/ajax_get_booking",
            data: {
                id_customer: selectCustomer.val(),
                type: type
            },
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                selectBooking.prop("disabled", false);
                $.each(data, function (index, value) {
                    selectBooking.append(
                        $('<option>', {
                            value: value.id
                        }).text(value.no_booking + ' (' + value.no_reference + ')')
                    );
                });
            },
            error: function (xhr, status, error) {
                selectBooking.prop("disabled", false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    function fetchBookingExtensionData() {
        selectBookingInvoice.empty().append($('<option>')).prop("disabled", true);

        $.ajax({
            type: "GET",
            url: baseUrl + "invoice/ajax_get_booking_invoice",
            data: {
                id_customer: selectCustomer.val(),
                type: 'BOOKING FULL,BOOKING FULL EXTENSION'
            },
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                selectBookingInvoice.prop("disabled", false);
                $.each(data, function (index, value) {
                    selectBookingInvoice.append(
                        $('<option>', {
                            value: value.id
                        }).text(value.no_invoice + ' (' + value.no_reference + ' - ' + value.no_reference_booking + ')')
                    );
                });
            },
            error: function (xhr, status, error) {
                selectBookingInvoice.prop("disabled", false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    function fetchHandlingData() {
        selectHandling.empty().append($('<option>')).prop("disabled", true);

        $.ajax({
            type: "GET",
            url: baseUrl + "invoice/ajax_get_handling",
            data: {id_customer: selectCustomer.val()},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                selectHandling.prop("disabled", false);
                $.each(data, function (index, value) {
                    selectHandling.append(
                        $('<option>', {
                            value: value.id
                        }).text(value.no_handling + ' - ' + value.handling_type + ' (' + value.no_booking + ' - ' + value.no_reference + ')')
                    );
                });
            },
            error: function (xhr, status, error) {
                selectHandling.prop("disabled", false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    function fetchWorkOrderData() {
        selectWorkOrder.empty().append($('<option>')).prop("disabled", true);

        $.ajax({
            type: "GET",
            url: baseUrl + "invoice/ajax_get_work_order",
            data: {id_customer: selectCustomer.val()},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                selectWorkOrder.prop("disabled", false);
                $.each(data, function (index, value) {
                    selectWorkOrder.append(
                        $('<option>', {
                            value: value.id
                        }).text(value.no_work_order + ' - ' + value.handling_type + ' (' + value.no_handling + ' - ' + value.no_reference + ')')
                    );
                });
            },
            error: function (xhr, status, error) {
                selectWorkOrder.prop("disabled", false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    function fetchInvoiceSourceData() {
        chargeInvoiceWrapper.closest('.panel').hide();
        chargeInvoiceWrapper.empty();
        switch (selectInvoiceType.val()) {
            case 'BOOKING FULL':
            case 'BOOKING DEPO':
                fetchBookingData(selectBookingIn, 'INBOUND');
                break;
            case 'BOOKING FULL EXTENSION':
                fetchBookingExtensionData();
                break;
            case 'HANDLING':
                fetchHandlingData();
                break;
            case 'WORK ORDER':
                fetchWorkOrderData();
                break;
            default:
                break;
        }
    }

    selectInvoiceType.on('change', function () {
        fetchInvoiceSourceData();
    });

    selectCustomer.on('change', function () {
        fetchInvoiceSourceData();
    });

    /* END OF INVOICE SOURCE DATA */


    /* HANDLING DATA CHARGE */
    function fetchInvoiceCharge(url, data) {
        chargeInvoiceWrapper.closest('.panel').show();
        chargeInvoiceWrapper.html('Fetching invoice source data...');
        $.ajax({
            type: "GET",
            url: url,
            data: data,
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                if (data.trim() == '') {
                    chargeInvoiceWrapper.html('<p class="text-primary">No charge list available</p>');
                } else {
                    chargeInvoiceWrapper.html(data);
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    function fetchCharges() {
        // handling
        if (formInvoiceModeHandling.is(':visible')) {
            fetchInvoiceCharge(baseUrl + "invoice/ajax_get_charge_by_handling", {
                id_handling: selectHandling.val(),
                setting: selectInvoiceSetting.val()
            });
        }

        // work order
        if (formInvoiceModeWorkOrder.is(':visible')) {
            fetchInvoiceCharge(baseUrl + "invoice/ajax_get_charge_by_work_order", {
                id_work_order: selectWorkOrder.val(),
                setting: selectInvoiceSetting.val()
            });
        }

        // booking full
        if (formInvoiceModeFull.is(':visible')) {
            var chargeUrl = baseUrl + "invoice/ajax_get_charge_by_booking_full";
            if(selectInvoiceType.val() === 'BOOKING DEPO') {
                chargeUrl = baseUrl + "invoice/ajax_get_charge_by_booking_depo";
            }

            fetchInvoiceCharge(chargeUrl, {
                id_booking: selectBookingIn.val(),
                setting: selectInvoiceSetting.val(),
                outbound_date: formInvoiceModeFull.find('#outbound_date').val(),
            });
        }

        // booking full extension
        if (formInvoiceModeFullExtension.is(':visible')) {
            fetchInvoiceCharge(baseUrl + "invoice/ajax_get_charge_by_booking_full_extension", {
                id_invoice: selectBookingInvoice.val(),
                setting: selectInvoiceSetting.val(),
                outbound_date: formInvoiceModeFullExtension.find('#outbound_date').val(),
            });
        }
    }

    // fetch charges when data reference is changed.
    selectHandling.on('change', function () {
        fetchCharges();
    });
    selectWorkOrder.on('change', function () {
        fetchCharges();
    });
    selectBookingIn.on('change', function () {
        fetchCharges();
    });
    selectBookingInvoice.on('change', function () {
        fetchCharges();
    });

    // fetch charges when invoice setting is changed.
    selectInvoiceSetting.on('change', function () {
        var bookingInSelected = selectBookingIn.val() != null && selectBookingIn.val() != '';
        var bookingInvoiceSelected = selectBookingInvoice.val() != null && selectBookingInvoice.val() != '';
        if (bookingInSelected || bookingInvoiceSelected) {
            fetchCharges();
        }
    });

    // fetch charges when outbound date is changed
    formInvoiceModeFull.find('#outbound_date').on('changeDate', function (e) {
        if (selectBookingIn.val() != null && selectBookingIn.val() != '') {
            fetchCharges();
        }
    });
    formInvoiceModeFullExtension.find('#outbound_date').on('changeDate', function (e) {
        if (selectBookingInvoice.val() != null && selectBookingInvoice.val() != '') {
            fetchCharges();
        }
    });
    /* END OF HANDLING INVOICE CHARGE */


    $('#table-invoice').on('click', '.btn-delete-invoice', function (e) {
        e.preventDefault();

        var idInvoice = $(this).closest('.row-invoice').data('id');
        var labelInvoice = $(this).closest('.row-invoice').data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteInvoice = $('#modal-delete-invoice');
        modalDeleteInvoice.find('form').attr('action', urlDelete);
        modalDeleteInvoice.find('input[name=id]').val(idInvoice);
        modalDeleteInvoice.find('#invoice-title').text(labelInvoice);

        modalDeleteInvoice.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-invoice').on('click', '.btn-print-invoice', function (e) {
        e.preventDefault();

        var labelInvoice = $(this).closest('.row-invoice').data('label');
        var urlPrint = $(this).attr('href');

        var modalPrintInvoice = $('#modal-print-invoice');
        modalPrintInvoice.find('#invoice-title').text(labelInvoice);
        modalPrintInvoice.find('#print-with-header').attr('href', urlPrint + '?with_header=1');
        modalPrintInvoice.find('#print-without-header').attr('href', urlPrint + '?with_header=0');

        modalPrintInvoice.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-invoice').on('click', '.btn-publish-invoice', function (e) {
        e.preventDefault();

        var idInvoice = $(this).closest('.row-invoice').data('id');
        var labelInvoice = $(this).closest('.row-invoice').data('label');
        var isPerforma = $(this).closest('.row-invoice').data('is-performa');
        var urlPublish = $(this).attr('href');

        var modalPublishInvoice = $('#modal-publish-invoice');
        modalPublishInvoice.find('form').attr('action', urlPublish);
        modalPublishInvoice.find('input[name=id]').val(idInvoice);
        modalPublishInvoice.find('#invoice-title').text(labelInvoice);

        if (isPerforma) {
            modalPublishInvoice.find('button[type="submit"]')
                .addClass('btn-warning')
                .removeClass('btn-success')
                .text('Publish Invoice Performa');
        } else {
            modalPublishInvoice.find('button[type="submit"]')
                .addClass('btn-success')
                .removeClass('btn-warning')
                .text('Publish Invoice');
        }

        modalPublishInvoice.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-invoice').on('click', '.btn-cancel-invoice', function (e) {
        e.preventDefault();

        var idInvoice = $(this).closest('.row-invoice').data('id');
        var labelInvoice = $(this).closest('.row-invoice').data('label');
        var urlPublish = $(this).attr('href');

        var modalCancelInvoice = $('#modal-cancel-invoice');
        modalCancelInvoice.find('form').attr('action', urlPublish);
        modalCancelInvoice.find('input[name=id]').val(idInvoice);
        modalCancelInvoice.find('#invoice-title').text(labelInvoice);

        modalCancelInvoice.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-invoice').on('click', '.btn-upload-faktur', function (e) {
        e.preventDefault();

        var idInvoice = $(this).closest('.row-invoice').data('id');
        var urlUpload = $(this).attr('href');
        var noFaktur = $(this).data('no-faktur');
        var attachmentFaktur = $(this).data('attachment-faktur');

        var modalUpload = $('#modal-upload-faktur');
        modalUpload.find('form').attr('action', urlUpload);
        modalUpload.find('input[name=id]').val(idInvoice);
        modalUpload.find('#uploaded_faktur').text(attachmentFaktur);
        modalUpload.find('#no_faktur').val(noFaktur);

        modalUpload.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-invoice').on('click', '.btn-pay-invoice', function (e) {
        e.preventDefault();

        var idInvoice = $(this).closest('.row-invoice').data('id');
        var urlPayment = $(this).attr('href');
        var paymentDate = $(this).data('payment-date');
        var transferBank = $(this).data('transfer-bank');
        var transferAmount = $(this).data('transfer-amount');
        var cashAmount = $(this).data('cash-amount');
        var overPaymentAmount = $(this).data('over-payment-amount');
        var paymentDescription = $(this).data('payment-description');

        var modalUpload = $('#modal-pay-invoice');
        modalUpload.find('form').attr('action', urlPayment);
        modalUpload.find('input[name=id]').val(idInvoice);
        modalUpload.find('#payment_date').val(paymentDate);
        modalUpload.find('#transfer_bank').val(transferBank);
        modalUpload.find('#transfer_amount').val(transferAmount);
        modalUpload.find('#cash_amount').val(cashAmount);
        modalUpload.find('#over_payment_amount').val(overPaymentAmount);
        modalUpload.find('#payment_description').val(paymentDescription);

        modalUpload.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    var tableInvoice = $('#table-invoice.table-ajax');
    var controlTemplate = $('#control-invoice-template').html();
    tableInvoice.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search invoice"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'invoice/invoice_data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title',
                data: 'customer_name'
            },
            {data: 'no_invoice'},
            {data: 'no_reference'},
            {data: 'invoice_date'},
            {data: 'total_price'},
            {data: 'type'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 4,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: 3,
            render: function (data, type, full, meta) {
                var referenceLink = '#';
                if (full.type === 'BOOKING FULL' || full.type === 'BOOKING FULL EXTENSION' || full.type === 'BOOKING PARTIAL' || full.type === 'BOOKING DEPO') {
                    referenceLink = baseUrl + 'booking/view/' + full.id_booking;
                } else if (full.type === 'HANDLING') {
                    referenceLink = baseUrl + 'handling/view/' + full.id_handling;
                } else if (full.type === 'WORK ORDER') {
                    referenceLink = baseUrl + 'work-order/view/' + full.id_work_order;
                }
                return '<a href="' + referenceLink + '">' + data + '</a>' + '<br><span>' + full.no_reference_booking + '</span>';
            }
        }, {
            targets: 5,
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: -2,
            render: function (data, type, full, meta) {
                var statusLabel = 'primary';
                if (data == 'DRAFT') {
                    statusLabel = 'default';
                } else if (data == 'PUBLISHED') {
                    statusLabel = 'success';
                } else if (data == 'CANCELED') {
                    statusLabel = 'danger';
                }

                var statusPerforma = '';
                if (full.is_performa) {
                    statusPerforma = "<br><span class='label label-warning'>PERFORMA</span>"
                }
                return "<span class='label label-" + statusLabel + "'>" + data.toUpperCase() + "</span>" + statusPerforma;
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_invoice}}/g, full.no_invoice)
                    .replace(/{{no_reference}}/g, full.no_reference)
                    .replace(/{{is_performa}}/g, full.is_performa)
                    .replace(/{{no_faktur}}/g, full.no_faktur)
                    .replace(/{{attachment_faktur}}/g, full.attachment_faktur)
                    .replace(/{{label_upload_faktur}}/g, (full.no_faktur === '' || full.no_faktur === null) ? 'Upload Faktur' : 'Update Faktur')
                    .replace(/{{payment_date}}/g, $.trim(full.payment_date) == '' ? '' : moment(full.payment_date).format('D MMMM YYYY'))
                    .replace(/{{transfer_bank}}/g, full.transfer_bank)
                    .replace(/{{transfer_amount}}/g, 'Rp. ' + numberFormat(full.transfer_amount, 0, ',', '.'))
                    .replace(/{{cash_amount}}/g, 'Rp. ' + numberFormat(full.cash_amount, 0, ',', '.'))
                    .replace(/{{over_payment_amount}}/g, 'Rp. ' + numberFormat(full.over_payment_amount, 0, ',', '.'))
                    .replace(/{{payment_description}}/g, full.payment_description)
                    .replace(/{{payment_update_payment}}/g, (full.payment_date === '' || full.payment_date === null) ? 'Pay Invoice' : 'Update Payment');

                var control = $.parseHTML(control);
                if (full.status != 'PUBLISHED') {
                    $(control).find('.upload-faktur').remove();
                    $(control).find('.update-payment').remove();
                }
                if (full.status != 'DRAFT') {
                    $(control).find('.publish-invoice').remove();
                }
                if (full.status != 'PUBLISHED') {
                    $(control).find('.cancel-invoice').remove();
                }
                return $('<div />').append($(control)).html();
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });
});