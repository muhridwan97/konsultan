$(function () {
    var formTEPCheckIn = $('#form-modal-tep-check-in');
    var totalColumns = formTEPCheckIn.first().find('th').length;
    var tableDetailAdditionalGuest = $('#table-detail-additional-guest tbody');
    var buttonAddGuest = formTEPCheckIn.find('#btn-add-additional-guest');
    var additionalGuestTemplate = $('#row-additional-guest-template').html();

    // handling chassis type
    const selectChassisHandlingType = formTEPCheckIn.find('#chassis_handling_type');
    const inputNoChassis = formTEPCheckIn.find('#no_chassis');
    selectChassisHandlingType.on('change', function () {
        if ($(this).val() === 'drop-chassis') {
            inputNoChassis.prop('disabled', false);
        } else {
            inputNoChassis.val('').prop('disabled', true);
        }
    });

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
});