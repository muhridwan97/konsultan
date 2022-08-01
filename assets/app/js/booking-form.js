$(function () {
    const formBooking = $('#form-booking');
    const selectCategory = formBooking.find('#category');
    const selectBookingType = formBooking.find('#booking-type');
    const selectBookingDocument = formBooking.find('#booking-document');
    const selectCustomer = formBooking.find('#customer');
    const selectSupplier = formBooking.find('#supplier');
    const selectBookingIn = formBooking.find('#booking_in');

    const documentWrapper = $('#document-info-wrapper');
    const extensionWrapper = $('#extension-wrapper');
    const bookingReferenceWrapper = $('#booking-reference-wrapper');
    const inputDetailWrapper = $('#input-detail-wrapper');

    selectCategory.on('change', function () {
        formBooking.find('#no_reference').val('');
        formBooking.find('#reference_date').val('');
        selectCustomer.val('').trigger('change');
        selectSupplier.val('').trigger('change');

        // show or hide input detail wrapper
        setLayoutBookingType();

        // populate booking type by its category
        selectBookingType.empty().append($('<option>')).prop("disabled", true);
        fetch(`${baseUrl}booking-type/ajax-get-by-category?category=${selectCategory.val()}`)
            .then(result => result.json())
            .then(data => {
                selectBookingType.prop("disabled", false);
                data.forEach(row => {
                    selectBookingType.append(
                        $('<option>', {
                            value: row.id,
                            "data-type": row.type,
                        }).text(`${row.booking_type} (${row.category} - ${row.type})`)
                    );
                });
            })
            .catch(err => {
                console.log(err);
                selectBookingType.prop("disabled", false);
            });
    });

    function setLayoutBookingType() {
        const category = selectCategory.val();
        if (category) {
            inputDetailWrapper.show();

            // give a limitation that inbound must input and outbound take from stock
            if (category === 'OUTBOUND') {
                bookingReferenceWrapper.show();
                selectBookingIn.prop('disabled', false);

                $('.btn-add-goods').data('source', 'STOCK');
                $('.btn-add-container').data('source', 'STOCK');
                $('.btn-create-container').hide();
                $('.btn-create-goods').hide();
            } else {
                bookingReferenceWrapper.hide();
                selectBookingIn.prop('disabled', true);

                $('.btn-add-goods').data('source', 'INPUT');
                $('.btn-add-container').data('source', 'INPUT');
                $('.btn-create-container').show();
                $('.btn-create-goods').show();
            }
        } else {
            inputDetailWrapper.hide();
        }
    }

    if (selectCategory.val()) {
        setLayoutBookingType();
    }

    selectBookingType.on('change', function () {
        fetch(`${baseUrl}extension-field/ajax-get-by-booking-type?id_booking_type=${$(this).val()}`)
            .then(result => result.text())
            .then(data => {
                if (data.trim() === '') {
                    extensionWrapper.html('<p>No extension field needed</p>');
                } else {
                    extensionWrapper.html(data);

                    $('input').iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue',
                        increaseArea: '20%'
                    });
                    reinitializeDateLibrary();
                }
            })
            .catch(console.log);

        if (selectBookingType.find('option:selected').data('type') === "EXPORT") {
            selectBookingIn.prop({
                multiple: true,
                name: 'booking_in[]'
            });
        } else {
            selectBookingIn.prop({
                multiple: false,
                name: 'booking_in'
            });
        }
        selectBookingIn.select2();
    });

    if (selectBookingType.val() && !formBooking.hasClass('edit')) {
        selectBookingType.trigger('change');
    }

    selectCustomer.on('change', function () {
        const customerId = $(this).val();
        if (customerId) {
            fetchUploadedDocument();

            // if (selectCategory.val() === 'OUTBOUND') {
            //     selectBookingIn.prop('disabled', true).empty().append($('<option>'));
            //     fetch(`${baseUrl}booking/ajax_get_stock_booking_by_customer?id_customer=${customerId}&is_completed=1`)
            //         .then(result => result.json())
            //         .then(data => {
            //             selectBookingIn.prop("disabled", false);
            //             data.forEach(row => {
            //                 selectBookingIn.append(
            //                     $('<option>', {value: row.id}).text(`${row.no_booking} (${row.no_reference})`)
            //                 );
            //             });
            //         })
            //         .catch(err => {
            //             console.log(err);
            //             selectBookingIn.prop("disabled", false);
            //         });
            // }
        }
    });

    selectBookingDocument.on('change', function () {

        const query = {
            id_booking_type: selectBookingType.val(),
            id_upload: $(this).val(),
        };

        fetch(`${baseUrl}upload_document/ajax_upload_user_document?${$.param(query)}`)
            .then(result => result.json())
            .then(data => {
                documentWrapper.find('.data').remove();
                data.forEach(row => {
                    documentWrapper.append(
                        $('<li>', {class: 'list-group-item data'})
                            .append($('<strong>')
                                .text(row.document_type))
                            .append(' : ' + row.no_document)
                            .append($('<span>', {class: 'pull-right'})
                                .text(row.total_file + ' files'))
                    );

                    if (row.is_main_document) {
                        formBooking.find('#no_reference').val(row.no_document);
                        formBooking.find('#reference_date').val(moment(row.document_date).format('DD MMMM YYYY'));
                    }
                });
                documentWrapper.show();
            })
            .catch(console.log);

        if (selectCategory.val() === 'OUTBOUND') {
            $.ajax({
                url: baseUrl + 'booking/ajax_get_booking_in_by_id_upload',
                type: 'GET',
                data: {id_upload: $(this).val()},
                success: function (data) {
                    const customerId = selectCustomer.val();
                    selectBookingIn.prop('disabled', true).empty().append($('<option>'));

                    let bookingQuery = '';
                    data.forEach(function (booking) {
                        bookingQuery += ('&id_booking[]=' + booking.id);
                    });

                    fetch(`${baseUrl}booking/ajax_get_stock_booking_by_customer?id_customer=${customerId || null}${bookingQuery}&is_completed=1`)
                        .then(result => result.json())
                        .then(data => {
                            selectBookingIn.prop("disabled", false);
                            const selectedIds = [];
                            data.forEach(row => {
                                selectBookingIn.append(
                                    $('<option>', {value: row.id}).text(`${row.no_booking} (${row.no_reference})`)
                                );
                                selectedIds.push(row.id);
                            });
                            selectBookingIn.val(selectedIds).trigger('change');
                        })
                        .catch(err => {
                            console.log(err);
                            selectBookingIn.prop("disabled", false);
                        });
                }
            });
        }

    });

    if (selectBookingDocument.val() && !formBooking.hasClass('edit')) {
        selectBookingDocument.trigger('change');
    }

    function fetchUploadedDocument() {
        const query = {
            id_booking_type: selectBookingType.val(),
            id_customer: selectCustomer.val(),
            id_supplier: selectSupplier.val(),
            except: formBooking.hasClass('edit') ? selectBookingDocument.val() : ''
        };

        if (query.id_booking_type && query.id_customer) {
            documentWrapper.hide();
            selectBookingDocument.empty().append($('<option>')).prop("disabled", true);

            fetch(`${baseUrl}upload/ajax_get_uploads_by_booking_type?${$.param(query)}`)
                .then(result => result.json())
                .then(data => {
                    selectBookingDocument.prop("disabled", false);
                    data.forEach(data => {
                        selectBookingDocument.append(
                            $('<option>', {value: data.id, "data-supplier": data.id_person})
                                .text(data.description + ' - ' + data.no_upload + ' - ' + moment(data.created_at)
                                    .format('DD MMMM YYYY hh:mm A'))
                        );
                    });
                })
                .catch(err => {
                    console.log(err);
                    selectBookingDocument.prop("disabled", false);
                });
        }
    }

    selectBookingIn.on('change', function () {
        const bookingId = $(this).val();
        $('#modal-stock-goods').data('booking-id', bookingId);
        $('#modal-stock-container').data('booking-id', bookingId);

        $('#table-container').find('tbody').html(`
            <tr class="row-placeholder">
                <td colspan="9">No container data</td>
            </tr>
        `);

        $('#table-goods').find('tbody').html(`
            <tr class="row-placeholder">
                <td colspan="14">No goods data</td>
            </tr>
        `);

        // check if booking need invoice to perform next actions
        fetch(`${baseUrl}booking/ajax-get-status-hold-by/${bookingId}?hold_by_invoice=1`)
            .then(result => result.json())
            .then(data => {
                $('#booking-hold-message').remove();
                if (data.hold) {
                    $('#input-detail-wrapper').hide();
                    $(`<div id="booking-hold-message" class="alert alert-danger">${data.message}</div>`).insertAfter('#input-detail-wrapper');
                } else {
                    $('#input-detail-wrapper').show();
                }
            })
            .catch(err => {
                console.log(err);
            });

        fetch(`${baseUrl}booking/ajax_check_status_invoice?id_booking=${bookingId}`)
            .then(result => result.json())
            .then(data => {
                $('.invoice-required-message').remove();
                if (!data || data.status === 0) {
                    inputDetailWrapper.hide();
                    $( "<p class='text-danger lead invoice-required-message'>Invoice is required to perform this action!</p>" ).insertAfter('#booking-reference-wrapper');
                } else {
                    inputDetailWrapper.show();
                }
            });
    });

});