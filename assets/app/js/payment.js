$(function () {
    var tablePayment = $('#table-payment.table-ajax');
    var controlTemplate = $('#control-payment-template').html();
    const dataTablePayment = tablePayment.DataTable({
        language: {searchPlaceholder: "Search payment"},
        serverSide: true,
        pageLength: 25,
        ajax: baseUrl + 'payment/data?' + window.location.search.slice(1),
        order: [[9, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title no-wrap',
                data: 'no_booking'
            },
            {data: 'no_payment'},
            {data: 'payment_type'},
            {data: 'payment_date'},
            {data: 'amount_request'},
            {data: 'elapsed_day_until_realized'},
            {data: 'status'},
            {data: 'status_check'},
            {data: 'id'}
        ],
        columnDefs: [{
            orderable: false,
            targets: ['type-no'],
            render: function (data, type, full, meta) {
                if(tablePayment.data('allow-realize') === 1) {
                    return `
                        <div class="checkbox icheck">
                            <label for="check_row_${data}">
                                <input type="checkbox" id="check_row_${data}" class="check-rows" name="check_row_${data}" value="${full.id}">
                            </label>
                        </div>
                    `;
                }
                return data;
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return full.customer_name + '<br>' + (data || full.no_upload) + '<br><span class="text-muted">' + (full.no_reference || full.upload_description) + '</span>';
            }
        }, {
            targets: ['type-payment'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'payment/view/' + full.id + '">' + data + '</a><br><small class="text-danger">' + full.branch + '</small>';
            }
        }, {
            targets: ['type-type-payment'],
            render: function (data, type, full, meta) {
                const color = full.charge_position === 'BEFORE TAX' ? 'muted' : 'danger';
                const labelChargePosition = '<small class="text-' + color + '">' + full.charge_position + '</small>';
                return data + '<br>' + labelChargePosition;
            }
        }, {
            targets: ['type-payment-date'],
            render: function (data, type, full, meta) {
                const labelPaidByCustomer = full.payment_type === "OB TPS PERFORMA" && full.amount_request <= 0 ? '<strong class="text-success">Paid By Customer</strong>' : '';
                const labelPaymentDate = $.trim(data) === '' ? (labelPaidByCustomer ? '' : '-') : moment(data).format('D MMMM YYYY H:mm');
                const labelTPSPaymentDate = full.tps_invoice_payment_date ? `<br><span class="text-primary">TPS Payment: ${moment(full.tps_invoice_payment_date).format('D MMMM YYYY')}</span>` : '';
                return labelPaymentDate + (labelPaidByCustomer && $.trim(data) !== '' ? '<br>' : '') + labelPaidByCustomer + labelTPSPaymentDate;
            }
        }, {
            targets: ['type-currency-bank'],
            render: function (data, type, full, meta) {
                return setCurrencyValue(parseInt(data), 'Rp. ') + '<br><small class="text-danger">' + (full.bank || '') + '</small>';
            }
        }, {
            targets: ['type-currency'],
            render: function (data, type, full, meta) {
                return setCurrencyValue(parseInt(full.amount), 'Rp. ') + '<br><small class="text-small">' + (full.elapsed_day_until_realized || 0) + ' day(s) elapsed</small>';
            }
        }, {
            targets: ['type-status-payment'],
            render: function (data, type, full) {
                let statusLabel = 'default';
                if (full.status === 'APPROVED') {
                    statusLabel = 'success';
                } else if (full.status === 'REJECTED') {
                    statusLabel = 'danger';
                }

                let statusRealizationLabel = 'default';
                let labelRealization = 'REQUESTED';
                if (full.is_realized === '1') {
                    statusRealizationLabel = 'primary';
                    labelRealization = 'REALIZED';
                } else if (full.is_submitted === '1') {
                    if (full.submitted_at) {
                        statusRealizationLabel = 'warning';
                        labelRealization = 'SUBMITTED';
                    } else {
                        statusRealizationLabel = 'danger';
                        labelRealization = 'SUBMISSION REJECTED';
                    }
                }
                return `<span class="label label-${statusLabel}">${data}</span><br><span class="label label-${statusRealizationLabel}">${labelRealization}</span>`;
            }
        }, {
            targets: ['type-status-check'],
            render: function (data, type, full, meta) {
                var statusLabel = 'default';
                switch (data) {
                    case 'ASK APPROVAL':
                        statusLabel = 'warning';
                        break;
                    case 'REJECTED':
                        statusLabel = 'danger';
                        break;
                    case 'APPROVED':
                        statusLabel = 'success';
                        break;
                }
                return '<span class="label label-' + statusLabel + '">' + data + '</span>';
            }
        }, {
            targets: ['type-action'],
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_booking}}/g, full.no_booking)
                    .replace(/{{payment_type}}/g, full.payment_type)
                    .replace(/{{payment_date}}/g, full.payment_date)
                    .replace(/{{charge_position_label}}/g, full.charge_position == 'BEFORE TAX' ? 'Set After Tax' : 'Set Before Tax')
                    .replace(/{{no_payment}}/g, full.no_payment)
                    .replace(/{{check_label}}/g, setCurrencyValue(Number(full.amount || amount_request), 'Rp. ') + ' from bank ' + full.bank + ' (' + full.customer_name + ')<br><small class=\'text-danger\'>'+(encodeURIComponent(full.invoice_description || full.description))+'</small>');

                var control = $.parseHTML(control);
                const isPaidByCustomer = (full.payment_type === 'OB TPS PERFORMA' && full.amount_request <= 0);
                const isPaidByCompany = (full.payment_type === 'OB TPS PERFORMA' && full.amount_request > 0);
                if ( (full.status == 'APPROVED' && full.is_realized != 1) || (full.status_check == 'APPROVED') ) {
                    $(control).find('.edit').remove();
                }
                if (full.status != 'DRAFT') {
                    $(control).find('.validate').remove();
                }
                if (full.status != 'APPROVED' || full.status_check == 'APPROVED' || isPaidByCustomer) {
                    $(control).find('.set-bank').remove();
                }
                if (!isPaidByCompany) {
                    $(control).find('.submit-payment').remove();
                }
                if (!(full.status === 'APPROVED' && (full.is_submitted === '0' || !full.submitted_at))) {
                    $(control).find('.submit').remove();
                }
                if (!(full.is_realized === '0' && (full.is_submitted === '1' && full.submitted_at) && full.bank)) {
                    $(control).find('.reject-submission').remove();
                    $(control).find('.realization').remove();
                }
                if (full.status_check != 'ASK APPROVAL') {
                    $(control).find('.set-check').remove();
                }
                if ( ((full.status == 'APPROVED' &&  full.approved_by != tablePayment.data('login')) ||  (full.status != 'APPROVED' &&  full.created_by != tablePayment.data('login'))) && tablePayment.data('login') != 1) {
                    $(control).find('.set-notif').remove();
                }
                if (full.is_realized === '1') {
                    $(control).find('.set-notif').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }],
        drawCallback: function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
            $('[data-toggle="tooltip"]').tooltip({container: 'body'});
        }
    });

    $(document).on('click', '.btn-resend', function (e) {
        e.preventDefault();

        var idPayment = $(this).closest('.row-payment').data('id');
        var modalNotif = $('#modal-resend-notification');
        var formResend = modalNotif.find('form');
        var url = $(this).data('url');
        if (!url) {
            url = $(this).attr('href');
        }

        modalNotif.find('.btn-submit-notif').hide();
        modalNotif.find('.warning-resend').hide();
        modalNotif.find('.text-notif').hide();
        
        var timeleft = 10;
        var downloadTimer = setInterval(function(){
          if(timeleft <= 0){
            clearInterval(downloadTimer);
            modalNotif.find('#countdown').html("");
            modalNotif.find('.btn-submit-notif').show();
            modalNotif.find('.warning-resend').show();
            modalNotif.find('.text-notif').show();
          } else {
            modalNotif.find('#countdown').html(timeleft + " seconds remaining");
          }
          timeleft -= 1;
        }, 1000);

        formResend.attr('action', url);
        modalNotif.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    $('.btn-validate-payment').on('click', function (e) {
        e.preventDefault();

        var idPayment = $(this).closest('.row-payment').data('id');
        var labelPayment = $(this).closest('.row-payment').data('label');
        var urlValidation = $(this).attr('href');

        var modalValidatePayment = $('#modal-validate-payment');
        modalValidatePayment.find('form').attr('action', urlValidation);
        modalValidatePayment.find('input[name=id]').val(idPayment);
        modalValidatePayment.find('#payment-title').text(labelPayment);

        modalValidatePayment.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    const modalPaymentSubmission = $('#modal-payment-submission');
    tablePayment.on('click', '.btn-submission', function (e) {
        e.preventDefault();

        const currentRow = $(this).closest('.row-payment');
        const data = dataTablePayment.row(currentRow.closest('tr')).data();

        const label = $(this).closest('.row-payment').data('label');
        const url = $(this).attr('href');

        modalPaymentSubmission.find('form').attr('action', url);
        modalPaymentSubmission.find('#label-submit-no-payment').text(label);
        modalPaymentSubmission.find('#attachment_info').val(data.submission_attachment || '');

        modalPaymentSubmission.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    const formPayment = $('#form-payment');
    const selectCategory = formPayment.find('#payment_category');
    const selectPaymentType = formPayment.find('#payment_type');
    const selectBooking = formPayment.find('#booking');
    const selectAskPayment = formPayment.find('#ask_payment');
    const selectUpload = formPayment.find('#upload');
    const paidByCustomer = formPayment.find('#paid_by_customer');
    const checkIncludeRealization = formPayment.find('#include_realization');
    const inputPayment = formPayment.find('#amount');
    const inputNoInvoice = formPayment.find('#no_invoice');
    const inputSettlementDate = formPayment.find('#settlement_date');
    const bookingPaymentWrapper = $('#booking-payment-wrapper');
    const buttonSavePayment = formPayment.find('#btn-save-payment');
    const buttonEditPayment = formPayment.find('#btn-edit-payment');

    buttonSavePayment.on('click', function (e) {

        if(selectCategory.val() == '' || selectPaymentType.val() == ''){
            selectCategory.attr('required', true);
            selectPaymentType.attr('required', true);
            selectCategory.select2();
            selectPaymentType.select2();
        }

    });

    buttonEditPayment.on('click', function (e) {

        if(selectPaymentType.val() == ''){
            selectPaymentType.attr('required', true);
            selectPaymentType.select2();
        }

    });

    formPayment.find('.group-bank').hide();
    formPayment.find('#payment_method').attr('required', false);
    formPayment.find('#bank_name').attr('required', false);
    formPayment.find('#account_holder_name').attr('required', false);
    formPayment.find('#account_number').attr('required', false);
    formPayment.find('#withdrawal_date').attr('required', false);
    formPayment.find('#withdrawal_time').attr('required', false);
    selectAskPayment.on('change', function () {
        if ( ($(this).val() === 'BANK') && (!checkIncludeRealization.is(':checked')) ) {
            formPayment.find('.group-bank').show();
            formPayment.find('#payment_method').attr('required', true);
            formPayment.find('#bank_name').attr('required', true);
            formPayment.find('#account_holder_name').attr('required', true);
            formPayment.find('#account_number').attr('required', true);
            formPayment.find('#withdrawal_date').attr('required', true);
            formPayment.find('#withdrawal_time').attr('required', true);
        }else {
            formPayment.find('.group-bank').hide();
            formPayment.find('#payment_method').attr('required', false);
            formPayment.find('#bank_name').attr('required', false);
            formPayment.find('#account_holder_name').attr('required', false);
            formPayment.find('#account_number').attr('required', false);
            formPayment.find('#withdrawal_date').attr('required', false);
            formPayment.find('#withdrawal_time').attr('required', false);
        }
    });

    if (selectAskPayment.val() === 'BANK' && (!checkIncludeRealization.is(':checked')) ) {
        formPayment.find('.group-bank').show();
        formPayment.find('#payment_method').attr('required', true);
        formPayment.find('#bank_name').attr('required', true);
        formPayment.find('#account_holder_name').attr('required', false);
        formPayment.find('#account_number').attr('required', true);
        formPayment.find('#withdrawal_date').attr('required', true);
        formPayment.find('#withdrawal_time').attr('required', true);
    }else {
        formPayment.find('.group-bank').hide();
        formPayment.find('#payment_method').attr('required', false);
        formPayment.find('#bank_name').attr('required', false);
        formPayment.find('#account_holder_name').attr('required', false);
        formPayment.find('#account_number').attr('required', false);
        formPayment.find('#withdrawal_date').attr('required', false);
        formPayment.find('#withdrawal_time').attr('required', false);
    }

    selectCategory.on('change', function () {
        selectPaymentType.empty().append($('<option>'));

        if ($(this).val() === 'BILLING') {
            selectPaymentType.append($('<option>', {value: 'OB TPS'}).text('OB TPS'));
            selectPaymentType.append($('<option>', {value: 'OB TPS PERFORMA'}).text('OB TPS PERFORMA'));
            selectPaymentType.append($('<option>', {value: 'DISCOUNT'}).text('DISCOUNT'));
            selectPaymentType.append($('<option>', {value: 'DO'}).text('DO'));
            selectPaymentType.append($('<option>', {value: 'EMPTY CONTAINER REPAIR'}).text('EMPTY CONTAINER REPAIR'));
        } else {
            selectPaymentType.append($('<option>', {value: 'DRIVER'}).text('DRIVER'));
            selectPaymentType.append($('<option>', {value: 'DISPOSITION AND TPS OPERATIONAL'}).text('DISPOSITION AND TPS OPERATIONAL'));
        }
        selectPaymentType.append($('<option>', {value: 'AS PER BILL'}).text('AS PER BILL'));
        selectPaymentType.append($('<option>', {value: 'TOOLS AND EQUIPMENTS'}).text('TOOLS AND EQUIPMENTS'));
        selectPaymentType.append($('<option>', {value: 'MISC'}).text('MISC'));

        if(selectPaymentType.data('old')) {
            selectPaymentType.val(selectPaymentType.data('old'));
        }
    });

    // fallback for repopulate-form
    if(selectPaymentType.data('old')) {
        selectCategory.trigger('change');
    }

    selectPaymentType.on('change', function () {
        if(selectPaymentType.val()) {
            checkBookingData();

            if (selectPaymentType.val() === 'DO') {
                formPayment.find('#field-booking').hide();
                formPayment.find('#field-upload').show();
                selectBooking.val('').trigger('change');
                selectBooking.prop('required', false);
                selectUpload.prop('required', true);
            } else {
                formPayment.find('#field-booking').show();
                formPayment.find('#field-upload').hide();
                selectUpload.val('').trigger('change');
                selectBooking.prop('required', true);
                selectUpload.prop('required', false);
                if (selectPaymentType.val() === 'OB TPS PERFORMA') {
                    formPayment.find('#field-paid-by-customer').show();
                    formPayment.find('#field-payment-type').addClass('col-md-3').removeClass('col-md-6');
                } else {
                    formPayment.find('#field-paid-by-customer').hide();
                    formPayment.find('#field-payment-type').addClass('col-md-6').removeClass('col-md-3');
                    paidByCustomer.iCheck('uncheck');
                }
            }

            selectBooking.empty().append($('<option>')).prop("disabled", true);
            fetch(`${baseUrl}payment/ajax_get_assigned_booking?category=${selectCategory.val()}&type=${selectPaymentType.val()}`)
                .then(result => result.json())
                .then(bookings => {
                    selectBooking.prop("disabled", false);
                    bookings.forEach(row => {
                        selectBooking.append(
                            $('<option>', {value: row.id_booking})
                                .text(`${row.no_reference} - ${row.owner_name || row.customer_name}`)
                        );
                    });
                })
                .catch(console.log);
        }
    });

    paidByCustomer.on('ifChanged', function(e){
        if ($(this).is(':checked')) {
            selectAskPayment.val('').trigger('change').prop('disabled', true);
            inputPayment.prop('disabled', true);
            checkIncludeRealization.iCheck('uncheck').iCheck('disable');
            inputNoInvoice.prop('disabled', true);
            inputSettlementDate.prop('disabled', true);
        } else {
            selectAskPayment.prop('disabled', false);
            inputPayment.prop('disabled', false);
            checkIncludeRealization.iCheck('enable');
            inputNoInvoice.prop('disabled', false);
            inputSettlementDate.prop('disabled', false);
        }
    });

    // fallback for repopulate-form
    if(!formPayment.hasClass('edit')) {
        selectPaymentType.trigger('change');
    }

    function checkBookingData() {
        const bookingDetailData = formPayment.find('#booking-detail-data');
        bookingDetailData.find('select').prop('disabled', true);
        bookingDetailData.hide();
        if (selectPaymentType.val() === 'OB TPS' || selectPaymentType.val() === 'OB TPS PERFORMA') {
            bookingDetailData.empty();
            if (selectBooking.val()) {
                fetchBookingDetail(selectBooking.val());
            }
        }
    }

    function fetchBookingDetail(bookingId) {
        $.ajax({
            type: "GET",
            url: baseUrl + "payment/ajax_get_booking_detail_by_booking",
            data: {
                id_booking: bookingId,
                page: formPayment.hasClass('realization') ? 'payment/realization' : 'payment/create'
            },
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                if (data.trim() === '') {
                    formPayment.find('#booking-detail-data').html('<p class="text-center text-primary">No booking detail data found</p>');
                } else {
                    formPayment.find('#booking-detail-data').html(data);
                }
                formPayment.find('#booking-detail-data').show();
                formPayment.find('select').select2();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    selectBooking.on('change', function () {
        if($(this).val()) {
            $.ajax({
                type: "GET",
                url: baseUrl + "payment/ajax_get_payment_by_booking",
                data: {id_booking: $(this).val()},
                cache: true,
                headers: {
                    Accept: "text/html; charset=utf-8",
                    "Content-Type": "text/plain; charset=utf-8"
                },
                success: function (data) {
                    bookingPaymentWrapper.closest('.panel').show();
                    if (data.trim() === '') {
                        bookingPaymentWrapper.html('<p class="text-primary">No payment occurred before</p>');
                    } else {
                        bookingPaymentWrapper.html(data);
                    }
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText + ' ' + status + ' ' + error);
                }
            });
            checkBookingData();
        }
    });

    checkIncludeRealization.on('ifChanged', function () {
        if ($(this).is(':checked')) {
            formPayment.find('#field-payment-date').show();
            formPayment.find('#payment_date').prop('required', true);

            formPayment.find('.group-bank').hide();
            formPayment.find('#payment_method').attr('required', false);
            formPayment.find('#bank_name').attr('required', false);
            formPayment.find('#account_holder_name').attr('required', false);
            formPayment.find('#account_number').attr('required', false);
            formPayment.find('#withdrawal_date').attr('required', false);
            formPayment.find('#withdrawal_time').attr('required', false);
            
        } else {
            formPayment.find('#field-payment-date').hide();
            formPayment.find('#payment_date').val('').prop('required', false);
            if (selectAskPayment.val() === 'BANK') {
                formPayment.find('.group-bank').show();
                formPayment.find('#payment_method').attr('required', true);
                formPayment.find('#bank_name').attr('required', true);
                formPayment.find('#account_holder_name').attr('required', true);
                formPayment.find('#account_number').attr('required', true);
                formPayment.find('#withdrawal_date').attr('required', true);
                formPayment.find('#withdrawal_time').attr('required', true);
            }else {
                formPayment.find('.group-bank').hide();
                formPayment.find('#payment_method').attr('required', false);
                formPayment.find('#bank_name').attr('required', false);
                formPayment.find('#account_holder_name').attr('required', false);
                formPayment.find('#account_number').attr('required', false);
                formPayment.find('#withdrawal_date').attr('required', false);
                formPayment.find('#withdrawal_time').attr('required', false);
            }
        }
    });

    /**
     * Toggle send batch ask approval record rows
     * url: /payment
     */
    tablePayment.on('ifChanged', '.check-rows', function () {
        const totalChecked = tablePayment.find('.check-rows:checked').length;
        if (totalChecked > 0) {
            $('.batch-action').show();
        } else {
            $('.batch-action').hide();
        }
    });

    const modalAskApproval = $('#modal-ask-approval');
    $('#btn-ask-approval-batch').on('click', function (e) {
        e.preventDefault();

        const url = $(this).attr('href');
        const totalChecked = tablePayment.find('.check-rows:checked').length;

        const checkedPaymentId = [];
        const checkedRow = $(".check-rows:checked");
        checkedRow.each(function () {
            checkedPaymentId.push($(this).val());
        });
        modalAskApproval.find('form').attr('action', url);
        modalAskApproval.find('input[name=id]').val(checkedPaymentId.join(','));
        modalAskApproval.find('#total-payment-records').text(totalChecked);

        modalAskApproval.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
