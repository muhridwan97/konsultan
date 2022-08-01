$(function () {
    var tablePallet = $('#table-pallet.table-ajax');
    var controlTemplate = $('#control-pallet-template').html();

    tablePallet.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search pallet"
        },
        serverSide: true,
        processing: true,
        order: [[0, "desc"]],
        ajax: baseUrl + 'pallet/pallet_data',
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_pallet'},
            {data: 'batch'},
            {data: 'description'},
            {data: 'no_booking'},
            {data: 'created_at'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: -2,
            render: function (data, type, full, meta) {
                return moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_pallet}}/g, full.no_pallet)
                    .replace(/{{batch}}/g, full.batch)
                    .replace(/{{batch_label}}/g, 'Batch ' + full.batch);
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-pallet').on('click', '.btn-delete-pallet', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var url = $(this).attr('href');

        var modalDelete = $('#modal-delete-pallet');
        modalDelete.find('form').attr('action', url);
        modalDelete.find('input[name=id]').val(id);
        modalDelete.find('input[name=batch]').val(0);
        modalDelete.find('#pallet-title').text(label);

        modalDelete.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-pallet').on('click', '.btn-delete-pallet-batch', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var url = $(this).attr('href');

        var modalDelete = $('#modal-delete-pallet');
        modalDelete.find('form').attr('action', url);
        modalDelete.find('input[name=id]').val(id);
        modalDelete.find('input[name=batch]').val(1);
        modalDelete.find('#pallet-title').text(label);

        modalDelete.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    var formPallet = $('#form-pallet');
    var selectGenerateMethod = formPallet.find('#type');
    var rawInputWrapper = formPallet.find('#raw-wrapper');
    var bookingInputWrapper = formPallet.find('#booking-wrapper');
    var bookingSelect = formPallet.find('#booking');

    selectGenerateMethod.on('change', function(){
        if($(this).val() === 'RAW'){
            rawInputWrapper.show();
            bookingInputWrapper.hide();
            bookingInputWrapper.find('input').attr('disabled', true);
            rawInputWrapper.find('input').removeAttr('disabled');
            rawInputWrapper.find('textarea').removeAttr('disabled');
        } else {
            rawInputWrapper.hide();
            bookingInputWrapper.show();
            rawInputWrapper.find('input').attr('disabled', true);
            rawInputWrapper.find('textarea').attr('disabled', true);
            bookingInputWrapper.find('input').removeAttr('disabled');
        }
    });

    bookingSelect.on('change', function(){
        $.ajax({
            type: "GET",
            url: baseUrl + "pallet/ajax_get_booking_form",
            data: {id_booking: $(this).val()},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                formPallet.find('#booking-pallet-form').html(data);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });
});