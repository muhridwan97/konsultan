$(function () {
    const tableHandling = $('#table-handling.table-ajax');
    const modalApproveHandling = $('#modal-approve-handling');
    const handlingComponentWrapper = modalApproveHandling.find('#component-wrapper');
    const controlTemplate = $('#control-handling-template').html();

    tableHandling.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search handling"
        },
        processing: true,
        search: {
            search: (getAllUrlParams().q === undefined ? '' : getAllUrlParams().q)
        },
        serverSide: true,
        ajax: baseUrl + 'handling/data',
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'customer_name'},
            {data: 'no_handling'},
            {data: 'handling_type'},
            {data: 'handling_date'},
            {data: 'handling_date_remaining'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 2,
            render: function (data, type, full, meta) {
                return data + '<br><a href="' + (baseUrl + 'booking/view/' + full.id_booking) + '">' + full.no_reference + '</a>';
            }
        }, {
            targets: 4,
            render: function (data, type, full, meta) {
                return moment(data).format('LL');
            }
        }, {
            targets: 5,
            render: function (data, type, full) {
                var remaining = full.handling_date_remaining + ' days remaining';
                var statusLabel = 'primary';
                if (full.handling_date_remaining < 0) {
                    remaining = 'Passed';
                    statusLabel = 'default';
                } else if (full.handling_date_remaining === 0) {
                    remaining = 'Today';
                    statusLabel = 'danger';
                } else if (full.handling_date_remaining === 1) {
                    remaining = 'Tomorrow';
                    statusLabel = 'warning';
                }
                return '<span class="label label-' + statusLabel + '">' + remaining + '</span>';
            }
        }, {
            targets: 6,
            render: function (data, type, full) {
                var statusLabel = 'default';
                if (full.status === 'APPROVED') {
                    statusLabel = 'success';
                } else if (full.status === 'REJECTED') {
                    statusLabel = 'danger';
                }
                return '<span class="label label-' + statusLabel + '">' + full.status + '</span>';
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full) {
                const allowApprove = full.status === 'REJECTED' || full.status === 'PENDING';
                const allowReject = full.status === 'APPROVED' || full.status === 'PENDING';
                // const allowEdit = full.status !== 'APPROVED';
                const allowDelete = full.status !== 'APPROVED';
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{id_handling_type}}/g, full.id_handling_type)
                    .replace(/{{customer_name}}/g, full.customer_name)
                    .replace(/{{customer_email}}/g, full.customer_email)
                    .replace(/{{no_handling}}/g, full.no_handling)
                    .replace(/{{resend_label}}/g, full.status === 'APPROVED' ? 'approve' : 'reject')
                    .replace(/{{allow_approve}}/g, allowApprove ? '' : 'hidden')
                    .replace(/{{allow_reject}}/g, allowReject ? '' : 'hidden')
                    // .replace(/{{allow_edit}}/g, allowEdit ? '' : 'hidden')
                    .replace(/{{allow_delete}}/g, allowDelete ? '' : 'hidden')
                    .replace(/{{permission}}/g, full.permission);
            }
        }]
    });

    tableHandling.on('click', '.btn-approve-handling', function (e) {
        e.preventDefault();

        var idHandling = $(this).closest('ul').data('id');
        var idHandlingType = $(this).closest('ul').data('id-handling-type');
        var labelHandling = $(this).closest('ul').data('label');
        var labelCustomer = $(this).closest('ul').data('customer');
        var labelEmail = $(this).closest('ul').data('email');
        var urlValidate = $(this).attr('href');

        modalApproveHandling.find('form').attr('action', urlValidate);
        modalApproveHandling.find('input[id]').val(idHandling);
        modalApproveHandling.find('#handling-title').text(labelHandling);
        modalApproveHandling.find('#handling-customer').text(labelCustomer);
        modalApproveHandling.find('#handling-email').text(labelEmail);

        handlingComponentWrapper.html('<p class="text-danger">Fetching handling component...</p>');
        modalApproveHandling.find('button[type=submit]').attr('disabled', true);
        $.ajax({
            type: 'GET',
            url: baseUrl + "handling/ajax_get_handling_component_form",
            data: {id_handling_type: idHandlingType},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                if (data.trim() == '') {
                    handlingComponentWrapper.html('<p class="text-danger">No handling component available</p>');
                } else {
                    handlingComponentWrapper.html(data);
                    handlingComponentWrapper.find('select').select2();
                }
                modalApproveHandling.find('button[type=submit]').attr('disabled', false);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });

        modalApproveHandling.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableHandling.on('click', '.btn-reject-handling', function (e) {
        e.preventDefault();

        var idHandling = $(this).closest('ul').data('id');
        var labelHandling = $(this).closest('ul').data('label');
        var labelCustomer = $(this).closest('ul').data('customer');
        var labelEmail = $(this).closest('ul').data('email');
        var urlValidate = $(this).attr('href');

        var modalRejectHandling = $('#modal-reject-handling');
        modalRejectHandling.find('form').attr('action', urlValidate);
        modalRejectHandling.find('input[id]').val(idHandling);
        modalRejectHandling.find('#handling-title').text(labelHandling);
        modalRejectHandling.find('#handling-customer').text(labelCustomer);
        modalRejectHandling.find('#handling-email').text(labelEmail);

        modalRejectHandling.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableHandling.on('click', '.btn-delete-handling', function (e) {
        e.preventDefault();

        var idHandling = $(this).closest('ul').data('id');
        var labelHandling = $(this).closest('ul').data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteHandling = $('#modal-delete-handling');
        modalDeleteHandling.find('form').attr('action', urlDelete);
        modalDeleteHandling.find('input[id]').val(idHandling);
        modalDeleteHandling.find('#handling-title').text(labelHandling);

        modalDeleteHandling.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    const formHandling = $('#form-handling');
    const selectCustomer = formHandling.find('#customer');
    const selectHandlingType = formHandling.find('#handling_type');
    const selectRefData = formHandling.find('#ref_data');
    const booking_stock = formHandling.find('#booking_stock');

    if(!booking_stock.val()){
        selectRefData.prop('disabled', true);
    }else{
        selectRefData.prop('disabled', false);
    }

    function fetchCustomerData() {
        selectHandlingType.prop('disabled', true).empty().append($('<option>'));
        selectRefData.prop('disabled', true).empty().append($('<option>'));

        fetch(`${baseUrl}handling_type/ajax_get_customer_handling_types?id_customer=${selectCustomer.val()}`)
            .then(result => result.json())
            .then(data => {
                selectHandlingType.prop("disabled", false);
                data.forEach(row => {
                    if (row.handling_type !== 'UNLOAD' && row.handling_type !== 'LOAD') {
                        selectHandlingType.append(
                            $('<option>', {value: row.id, "data-type": row.category})
                                .text(`${row.handling_type} (${row.category})`)
                        );
                    }
                });
            })
            .catch(err => {
                console.log(err);
                selectHandlingType.prop("disabled", false);
            });


        fetch(`${baseUrl}booking/ajax_get_stock_booking_by_customer?id_customer=${selectCustomer.val()}`)
            .then(result => result.json())
            .then(data => {
                selectRefData.prop("disabled", false);
                data.forEach(row => {
                    selectRefData.append(
                        $('<option>', {value: row.id, "data-type": row.category})
                            .text(`${row.no_booking} (${row.no_reference})`)
                    );
                });
            })
            .catch(err => {
                console.log(err);
                selectRefData.prop("disabled", false);
            });
    }

    selectCustomer.on('change', fetchCustomerData);

    selectRefData.on('change', fetchRefData);

    function fetchRefData() {
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
    }

    formHandling.on('submit', function () {
        const containerTaken = $('#table-container').find('tbody tr:not(.row-placeholder)').length;
        const goodsTaken = $('#table-goods').find('tbody tr:not(.row-placeholder)').length;

        if (selectHandlingType.find("option:selected").data('type') === 'WAREHOUSE') {
            if (containerTaken === 0 && goodsTaken === 0) {
                //alert('Select an item first!');
                //return false;
            }
        }
        formHandling.find('button[type=submit]').prop('disabled', true).html('Submitted...')
    });

});