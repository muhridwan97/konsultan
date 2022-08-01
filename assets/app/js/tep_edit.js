$(function () {
    var formTEPEdit = $('#form-edit-tep');
    var totalColumns = formTEPEdit.first().find('th').length;
    var tableDetailAdditionalGuest = $('#table-detail-additional-guest tbody');
    var buttonAddGuest = formTEPEdit.find('#btn-add-additional-guest');
    var additionalGuestTemplate = $('#row-additional-guest-template').html();

    buttonAddGuest.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailAdditionalGuest.empty();
        }

        tableDetailAdditionalGuest.append(additionalGuestTemplate);
        reorderItem();
    });

    tableDetailAdditionalGuest.on('click', '.btn-remove-guest', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailAdditionalGuest.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Additional Guest</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailAdditionalGuest.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
    }

    function getTotalItem() {
        return parseInt(tableDetailAdditionalGuest.find('tr.row-additional-guest-template').length);
    }
    const booking_id = $("#id_booking_security").val();

    if ( (booking_id != undefined) &&  (booking_id != null) && (booking_id != '') ){
        
        $('#modal-stock-goods').data('booking-id', booking_id).find('.modal-title').text('Booking Goods');
        $('#modal-stock-container').data('booking-id', booking_id).find('.modal-title').text('Booking Container');

        const query = {
            id_booking: booking_id,
        };
        $('.tally-editor').data('stock-url', `${baseUrl}safe-conduct/ajax_get_booking_data?${$.param(query)}`);

        $('#table-goods').find('tbody').html(`
            <tr class="row-placeholder">
                <td colspan="14">No goods data</td>
            </tr>
        `);

    }
});