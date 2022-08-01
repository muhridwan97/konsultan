$(function () {

    var formBookingJob = $('#form-booking-job');
    var buttonCreateJob = $('#btn-create-job');
    var buttonsCheckIn = $('.btn-check-in');
    var buttonsCheckOut = $('.btn-check-out');

    buttonCreateJob.on('click', function (e) {
        e.preventDefault();

        var idBooking = $(this).data('id-booking');
        var idHandling = $(this).data('id-handling');
        var idSafeConduct = $(this).data('id-safe-conduct');
        var typeHandling = $(this).data('type');
        var urlCreateJob = $(this).attr('href');
        var modalCreateJob = $('#modal-create-job');
        modalCreateJob.find('input[name=id_booking]').val(idBooking);
        modalCreateJob.find('input[name=id_handling]').val(idHandling);
        modalCreateJob.find('input[name=id_safe_conduct]').val(idSafeConduct);
        modalCreateJob.find('#handling-title').html(typeHandling);
        modalCreateJob.find('form').attr('action', urlCreateJob);

        modalCreateJob.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    buttonsCheckIn.on('click', function (e) {
        e.preventDefault();

        var idWorkOrder = $(this).closest('.row-workorder').data('id');
        var noWorkOrder = $(this).closest('.row-workorder').data('no');
        var urlCheckIn = $(this).attr('href');

        var modalCheckInJob = $('#modal-gate-check-in');
        modalCheckInJob.find('#check-in-title').text(noWorkOrder);
        modalCheckInJob.find('input[name=id]').val(idWorkOrder);
        modalCheckInJob.find('form').attr('action', urlCheckIn);

        modalCheckInJob.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    buttonsCheckOut.on('click', function (e) {
        e.preventDefault();

        var idWorkOrder = $(this).closest('.row-workorder').data('id');
        var noWorkOrder = $(this).closest('.row-workorder').data('no');
        var customer = $(this).closest('.row-workorder').data('customer');
        var email = $(this).closest('.row-workorder').data('email');
        var urlCheckOut = $(this).attr('href');

        var modalCheckOut = $('#modal-gate-check-out');
        modalCheckOut.find('#check-out-title').text(noWorkOrder);
        modalCheckOut.find('#check-out-customer').text(customer);
        modalCheckOut.find('#check-out-email').text(email);
        modalCheckOut.find('input[name=id]').val(idWorkOrder);
        modalCheckOut.find('form').attr('action', urlCheckOut);

        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    initializeLoaderBookingJob();
    function initializeLoaderBookingJob() {
        formBookingJob.on('click', '.btn-take', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableSource = $(this).closest('#source-' + type + '-wrapper');
            var tableDestination = formBookingJob.find('#destination-' + type + '-wrapper');

            var rowSource = tableSource.find('[data-row=' + id + ']');
            rowSource.find('input[type=hidden]').attr('disabled', false);
            rowSource.find('#quantity').attr('type', 'number');
            rowSource.find('#quantity-label').addClass('hidden');
            rowSource.find('#tonnage').attr('type', 'number');
            rowSource.find('#tonnage-label').addClass('hidden');
            rowSource.find('#volume').attr('type', 'number');
            rowSource.find('#volume-label').addClass('hidden');
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

        formBookingJob.on('click', '.btn-return', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableDestination = $(this).closest('#destination-' + type + '-wrapper');
            var tableSource = formBookingJob.find('#source-' + type + '-wrapper');

            var rowDestination = tableDestination.find('[data-row=' + id + ']');
            rowDestination.find('input[type=hidden]').attr('disabled', true);
            rowDestination.find('#quantity').attr('type', 'hidden');
            rowDestination.find('#quantity-label').removeClass('hidden');
            rowDestination.find('#tonnage').attr('type', 'hidden');
            rowDestination.find('#tonnage-label').removeClass('hidden');
            rowDestination.find('#volume').attr('type', 'hidden');
            rowDestination.find('#volume-label').removeClass('hidden');
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

});