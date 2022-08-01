$(function () {

    const formBookingImport = $('#form-booking-import');
    const selectCategory = formBookingImport.find('#category');
    const selectBookingDocument = formBookingImport.find('#booking-document');
    const selectBookingIn = formBookingImport.find('#booking_in');
    const documentWrapper = formBookingImport.find('#document-info-wrapper');
    const bookingReferenceWrapper = formBookingImport.find('#booking-reference-wrapper');
    const packageWrapper = formBookingImport.find('#package-wrapper');
    const checkCreatePackage = formBookingImport.find('#create_package');

    selectCategory.on('change', function () {
        if ($(this).val() === 'INBOUND') {
            packageWrapper.show();
            bookingReferenceWrapper.hide();
            bookingReferenceWrapper.find('select').prop('required', false);
        } else {
            packageWrapper.hide();
            packageWrapper.find('#create_package').iCheck('uncheck');
            bookingReferenceWrapper.show();
            bookingReferenceWrapper.find('select').prop('required', true);
        }

        documentWrapper.hide();
        selectBookingDocument.empty().append($('<option>')).prop("disabled", true);
        const query = {category: $(this).val()};
        fetch(`${baseUrl}upload/ajax_get_uploads_by_booking_type?${$.param(query)}`)
            .then(result => result.json())
            .then(data => {
                selectBookingDocument.prop("disabled", false);
                data.forEach(row => {
                    const label = row.name + ' - ' + row.description + ' - ' + moment(row.created_at).format('DD MMMM YYYY hh:mm A');
                    const newOption = new Option(label, row.id, false, false);
                    newOption.id_customer = row.id_person;

                    selectBookingDocument.append(newOption);
                });
            })
            .catch(err => {
                console.log(err);
                selectBookingDocument.prop("disabled", false);
            });
    });

    selectBookingDocument.on('change', function () {
        // fetch detail document
        const query = {id_upload: $(this).val(),};
        fetch(`${baseUrl}upload_document/ajax_upload_user_document?${$.param(query)}`)
            .then(result => result.json())
            .then(data => {
                documentWrapper.find('.data').remove();
                data.forEach(row => {
                    documentWrapper.append(
                        $('<li>', {class: 'list-group-item data'})
                            .append($('<strong>').text(row.document_type))
                            .append(' : ' + row.no_document)
                            .append($('<span>', {class: 'pull-right'}).text(row.total_file + ' files'))
                    );

                    if (row.is_main_document) {
                        formBookingImport.find('#no_reference').val(row.no_document);
                        formBookingImport.find('#reference_date').val(moment(row.document_date).format('DD MMMM YYYY'));
                    }
                });
                documentWrapper.show();
            })
            .catch(console.log);

        // fetch stock booking in
        const customerId = selectBookingDocument.select2('data')[0].element.id_customer;
        if (customerId) {
            if (selectCategory.val() === 'OUTBOUND') {
                selectBookingIn.prop('disabled', true).empty().append($('<option>'));
                fetch(`${baseUrl}booking/ajax_get_stock_booking_by_customer?id_customer=${customerId}`)
                    .then(result => result.json())
                    .then(data => {
                        selectBookingIn.prop("disabled", false);
                        data.forEach(row => {
                            const label = `${row.no_booking} (${row.no_reference})`;
                            const newOption = new Option(label, row.id, false, false);
                            selectBookingIn.append(newOption);
                        });
                    })
                    .catch(err => {
                        console.log(err);
                        selectBookingIn.prop("disabled", false);
                    });
            }
        }
    });

    const modalConfirm = $('#modal-confirm');
    modalConfirm.find('#btn-yes').on('click', function () {

    });
    modalConfirm.find('#btn-no').on('click', function () {
        modalConfirm.modal('hide');
    });

    let isSubmit = false;
    formBookingImport.on('submit', function (event) {
        if (checkCreatePackage.is(':checked') && !isSubmit) {
            showConfirm('Create Item Package', 'Are you sure want to create package? it will create master goods by quantity package!', function (e, modalConfirm, button) {
                isSubmit = true;
                formBookingImport.submit();
                $('span#loader').css('display', 'block');
                if ($(window).width() > 768 ) {
                    $('p#loader').text('Loading, please wait...');
                }
            });
            isSubmit = false;
            return false;
        }
        return true;
    });
});