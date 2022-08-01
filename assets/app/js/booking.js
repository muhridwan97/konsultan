$(function () {
    var tableBooking = $('#table-booking.table-ajax');
    var controlTemplate = $('#control-booking-template').html();
    tableBooking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search booking"
        },
        pageLength: 25,
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'booking/data?' + window.location.search.slice(1),
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
            {data: 'no_booking'},
            {data: 'booking_date'},
            {data: 'booking_type'},
            {data: 'booking_document'},
            {data: 'status'},
            {data: 'status_payout'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 1,
            render: function (data, type, full, meta) {
                return data + '<br><small class="text-muted">' + (full.outbound_type || 'Not set yet') + '</small>';
            }
        }, {
            targets: 3,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMM YYYY');
            }
        }, {
            targets: 4,
            render: function (data, type, full) {
                let label = `${data}: ${full.category}<br><small class="no-wrap">${full.no_reference}</small>`;
                if (full.category === 'OUTBOUND') {
                    if(full.type === 'EXPORT') {
                        (full.booking_references || []).forEach(booking => {
                            label += `<br><small class="text-muted no-wrap">REF: ${booking.no_ref_reference}</small>`;
                        });
                    } else {
                        label += `<br><small class="text-muted no-wrap">REF: ${full.no_reference_in}</small>`;
                    }
                }
                return label;
            }
        }, {
            targets: 5,
            render: function (data, type, full, meta) {
                var documents = '';
                if (full.upload_document) {
                    var uploadDocIds = full.upload_document.split(',');
                    var docTypes = full.document_type.split(';');
                    var docNos = full.booking_document.split(',');

                    uploadDocIds.forEach(function (value, index) {
                        documents += docTypes[index] + ": &nbsp; <a href='" + baseUrl + "upload_document/view/" + $.trim(value) + "'>" + docNos[index] + "</a><br>"
                    });
                }
                return documents;
            }
        }, {
            targets: 6,
            render: function (data) {
                var statusLabel = 'primary';
                if (data === 'BOOKED') {
                    statusLabel = 'default';
                } else if (data === 'COMPLETED') {
                    statusLabel = 'success';
                } else if (data === 'REJECTED') {
                    statusLabel = 'danger';
                }
                return "<span class='label label-" + statusLabel + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: 'type-payout',
            render: function (data, type, full) {
                data = data || 'NO CHECK';
                var statusLabel = 'primary';
                if (data === 'PENDING') {
                    statusLabel = 'danger';
                } else if (data === 'PARTIAL APPROVED') {
                    statusLabel = 'info';
                } else if (data === 'APPROVED') {
                    statusLabel = 'success';
                } else if (data === 'REJECTED') {
                    statusLabel = 'warning';
                }

                var statusPerforma = '';
                if (full.is_performa) {
                    statusPerforma = "<br><span class='label label-warning'>PERFORMA</span>"
                }
                return data ? "<span class='label label-" + statusLabel + "'>" + data + "</span>" + statusPerforma : '-';
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{category}}/g, full.category)
                    .replace(/{{status_payout}}/g, full.status_payout)
                    .replace(/{{payout_until_date}}/g, full.payout_until_date || '')
                    .replace(/{{no_booking}}/g, full.no_booking);

                control = $.parseHTML(control);

                if (full.status !== 'APPROVED') {
                    $(control).find('.action-complete').remove();
                    $(control).find('.action-revert-booked').remove();
                } else {
                    $(control).find('.action-delete').remove();
                }

                if (full.status !== 'BOOKED' && full.status !== 'REJECTED') {
                    $(control).find('.edit').remove();
                    $(control).find('.action-validate').remove();
                }

                if (full.status === 'COMPLETED') {
                    $(control).find('.action-edit').remove();
                    $(control).find('.action-delete').remove();
                } else {
                    $(control).find('.action-revert-complete').remove();
                }

                if (full.category === 'INBOUND') {
                    $(control).find('[data-authorize-in=""]').remove();
                    $(control).find('.action-validate-payout').remove();
                }

                if (full.status === 'COMPLETED' || full.category === 'INBOUND' || full.outbound_type === 'ACCOUNT RECEIVABLE' || ['PARTIAL APPROVED', 'APPROVED'].indexOf(full.status_payout) === -1) {
                    $(control).find('.action-payout-revert').remove();
                }

                if (full.status_payout === 'APPROVED' || full.outbound_type === 'ACCOUNT RECEIVABLE') {
                    $(control).find('.action-validate-payout').remove();
                }

                if (full.category === 'OUTBOUND') {
                    $(control).find('[data-authorize-out=""]').remove();
                }

                if (full.status === 'APPROVED') {
                    $(control).find('.action-validate').remove();
                }

                if (!(full.document_type || '').includes('SPPB') && full.branch_type === 'PLB') {
                    $(control).find('.action-validate').addClass('disabled');
                    $(control).find('.action-validate .btn-validate')
                        .addClass('disabled')
                        .prop('title', 'SPPB is needed to validate the booking');
                }

                return $('<div />').append($(control).clone()).html();
            }
        }],
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip({container: 'body', trigger: "hover"});
        }
    });

    /**
     * url: /booking/{index?}
     * Validating booking, all buttons share same class to be triggered .btn-validate,
     * we check data-validate attribute to detect which action we need to be performed.
     * for form logic see: validation.js
     */
    const modalValidation = $('#modal-validation');

    tableBooking.on('click', '.btn-validate', function (e) {
        e.preventDefault();

        const validate = $(this).data('validate');

        // reset state
        modalValidation.find('button[type=submit]').removeClass('btn-danger').removeClass('btn-success');
        modalValidation.find('.modal-body').find('.complete-alert').remove();
        modalValidation.find('button[type=submit]').prop('disabled', false);

        if (validate === 'approve') {
            modalValidation.find('.modal-title').text('Approve Booking');
            modalValidation.find('button[type=submit]').text('Approve').addClass('btn-success');
        } else if (validate === 'reject') {
            modalValidation.find('.modal-title').text('Reject Booking');
            modalValidation.find('button[type=submit]').text('Reject Booking').addClass('btn-danger');
        } else if (validate === 'complete') {
            modalValidation.find('.modal-title').text('Complete Booking');
            modalValidation.find('button[type=submit]')
                .prop('disabled', true)
                .text('Fetching status...')
                .addClass('btn-success');

            const bookingId = $(this).closest('.row-booking').data('id');
            const category = $(this).closest('.row-booking').data('category');

            const query = $.param({id_booking: bookingId, category: category});
            fetch(baseUrl + 'booking/ajax_check_booking_ready_to_complete?' + query)
                .then(result => result.json())
                .then((result) => {
                    modalValidation.find('button[type=submit]').text('Complete');
                    if (result) {
                        let tableDiscrepancy = '';
                        if (result.stock_discrepancies.length) {
                            let listRows = '';
                            result.stock_discrepancies.forEach(function(item, index) {
                                listRows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>
                                            ${item.source}<br>
                                            <small class="text-muted">${item.assembly_type}</small>
                                        </td>
                                        <td>
                                            ${item.goods_name}<br>
                                            <small class="text-muted">${item.no_goods}</small>
                                        </td>
                                        <td>${item.unit}</td>
                                        <td>${setCurrencyValue(Number(item.quantity_booking || 0), '', ',', '.')}</td>
                                        <td>${setCurrencyValue(Number(item.quantity_stock || 0), '', ',', '.')}</td>
                                        <td>${setCurrencyValue(Number(item.quantity_difference || 0), '', ',', '.')}</td>
                                    </tr>
                                `;
                            });
                            tableDiscrepancy = `                            
                                <table class="table table-condensed no-datatable responsive">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Source</th>
                                        <th style="width: 270px">Goods</th>
                                        <th>Unit</th>
                                        <th>Booking</th>
                                        <th>Stock</th>
                                        <th>Diff</th>
                                    </tr>
                                    </thead>
                                    <tbody>${listRows}</tbody>
                                </table>
                            `;
                        }
                        let completeContent = `
                            <ul class="list-group complete-alert">
                                <li class="list-group-item disabled">
                                    Status Booking Check
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.work_order_completed ? 'bg-green' : 'bg-red'}">
                                        ${result.work_order_completed ? 'YES' : 'NO'}
                                    </span>
                                    <span class="text-danger">All Job Completed (required)</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.work_order_gate_checked ? 'bg-green' : 'bg-red'}">
                                        ${result.work_order_gate_checked ? 'YES' : 'NO'}
                                    </span>
                                    <span class="text-danger">Has Job & Gate Checked (required)</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.work_order_validated ? 'bg-green' : 'bg-red'}">
                                        ${result.work_order_validated ? 'YES' : 'NO'}
                                    </span>
                                    <span class="text-danger">All Job Validated (required)</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.safe_conduct_security_checked ? 'bg-green' : 'bg-red'}">
                                        ${result.safe_conduct_security_checked ? 'YES' : 'NO'}
                                    </span>
                                    <span class="text-danger">Has Safe Conduct & Security Checked (required)</span>
                                </li>
                                <li class="list-group-item ${result.category === 'OUTBOUND' ? '' : 'hidden'}">
                                    <span class="badge ${result.safe_conduct_attachment ? 'bg-green' : 'bg-red'}">
                                        ${result.safe_conduct_attachment ? 'YES' : 'NO'}
                                    </span>
                                    <span class="text-danger">Has Handover Safe Conduct (required)</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.handling_converted ? 'bg-green' : 'bg-yellow'}">
                                        ${result.handling_converted ? 'YES' : 'NO'}
                                    </span>
                                    All Booking Data Converted To Handling
                                </li>
                                <li class="list-group-item">
                                    <span class="badge ${result.different_booking_to_work_order ? 'bg-yellow' : 'bg-green'}">
                                        ${result.different_booking_to_work_order ? 'YES' : 'NO'}
                                    </span>
                                    Different Booking To Job
                                </li>
                                <li class="list-group-item ${result.category === 'OUTBOUND' ? 'hidden' : ''}">
                                    <span class="badge ${result.stock_discrepancies.length ? 'bg-yellow' : 'bg-green'}">
                                        ${result.stock_discrepancies.length ? 'YES' : 'NO'}
                                    </span>
                                    <div class="text-danger">
                                        <strong>Discrepancy Booking To Stock</strong><br>
                                        <small>(Generate discrepancy handover if 'YES')</small>
                                    </div>
                                    ${tableDiscrepancy}
                                </li>
                            </ul>
                        `;
                        modalValidation.find('.modal-body .lead').first().after(completeContent);
                        if (result.category === 'OUTBOUND') {
                            if (result.work_order_completed && result.work_order_gate_checked && result.work_order_validated && result.safe_conduct_security_checked && result.safe_conduct_attachment) {
                                modalValidation.find('button[type=submit]').prop('disabled', false);
                            }
                        } else {
                            if (result.work_order_completed && result.work_order_gate_checked && result.work_order_validated && result.safe_conduct_security_checked) {
                                modalValidation.find('button[type=submit]').prop('disabled', false);
                            }
                        }
                    }
                })
                .catch(console.log);
        }
    });

    const modalValidatePayout = $('#modal-validate-payout');
    $('#table-booking').on('click', '.btn-validate-payout', function (e) {
        e.preventDefault();

        const labelBooking = $(this).data('label');
        const urlValidate = $(this).attr('href');
        const statusPayout = $(this).closest('.row-booking').data('status-payout');
        const payoutUntilDate = $(this).closest('.row-booking').data('payout-until-date');

        modalValidatePayout.find('form').attr('action', urlValidate);
        modalValidatePayout.find('#booking-title').text(labelBooking);
        modalValidatePayout.find('#payout_until_date').val(payoutUntilDate ? moment(payoutUntilDate).format('DD MMMM YYYY') : '');

        if (statusPayout === 'PARTIAL APPROVED') {
            modalValidatePayout.find('button[value="APPROVED"]').text('Approve Safe Conduct');
            modalValidatePayout.find('button[value="PARTIAL APPROVED"]').hide();
        } else {
            modalValidatePayout.find('button[value="APPROVED"]').text('Approve All');
            modalValidatePayout.find('button[value="PARTIAL APPROVED"]').show();
        }

        modalValidatePayout.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalValidatePayout.find('button[type="submit"]').on('click', function () {
        modalValidatePayout.find('input[name=status]').val($(this).val());
    });

    modalValidatePayout.find('form').on('submit', function () {
        $('button[type="submit"]').prop('disabled', true);
    });

    const modalRevertPayout = $('#modal-payout-revert');
    $('#table-booking').on('click', '.btn-revert-payout', function (e) {
        e.preventDefault();

        const bookingId = $(this).data('id');
        const labelBooking = $(this).data('label');
        const urlValidate = $(this).attr('href');
        const statusPayout = $(this).closest('.row-booking').data('status-payout');
        const payoutUntilDate = $(this).closest('.row-booking').data('payout-until-date');

        modalRevertPayout.find('form').attr('action', urlValidate);
        modalRevertPayout.find('input[name=id]').val(bookingId);
        modalRevertPayout.find('#booking-title').text(labelBooking);
        modalRevertPayout.find('#payout_until_date').val(payoutUntilDate ? moment(payoutUntilDate).format('DD MMMM YYYY') : '');

        if (statusPayout === 'APPROVED') {
            modalRevertPayout.find('button[value="PARTIAL APPROVED"]').show();
        } else {
            modalRevertPayout.find('button[value="PARTIAL APPROVED"]').hide();
        }

        modalRevertPayout.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    modalRevertPayout.find('button[type="submit"]').on('click', function () {
        modalRevertPayout.find('input[name=status]').val($(this).val());
    });

    modalRevertPayout.find('form').on('submit', function () {
        $('button[type="submit"]').prop('disabled', true);
    });

    // filter in booking index table
    const formBookingFilter = $('#form-booking-filter');
    formBookingFilter.find('#type').on('change', function () {
        formBookingFilter.submit();
    });
});