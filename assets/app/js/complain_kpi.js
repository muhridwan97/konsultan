$(function () {
    const formComplainKpi = $('#form-complain-kpi');
    var totalColumns = formComplainKpi.first().find('th').length;
    var tableDetailInputWhatsapp = $('#table-detail-whatsapp tbody');
    var buttonAddWhatsapp = formComplainKpi.find('#btn-add-whatsapp');
    var handlingWhatsappTemplate = $('#row-whatsapp-template').html();
    var wrapperReminder = $('#wrapper-reminder');
    var buttonAddReminder = formComplainKpi.find('#btn-add-reminder');
    var reminderTemplate = $('#row-reminder-template').html();

    buttonAddWhatsapp.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailInputWhatsapp.empty();
        }

        tableDetailInputWhatsapp.append(handlingWhatsappTemplate);
        reorderItem();
    });

    tableDetailInputWhatsapp.on('click', '.btn-remove-whatsapp', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputWhatsapp.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Group</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputWhatsapp.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('.select2').select2();
    }

    function getTotalItem() {
        return parseInt(tableDetailInputWhatsapp.find('tr.row-whatsapp').length);
    }    

    buttonAddReminder.on('click', function (e) {
        e.preventDefault();

        if (getTotalReminder() == 0) {
            wrapperReminder.empty();
        }
        wrapperReminder.append(reminderTemplate);
        reorderReminder();
    });

    function getTotalReminder() {
        return parseInt(wrapperReminder.find('div.row-reminder').length);
    }   
    
    function reorderReminder() {
        wrapperReminder.find('div.row-reminder').each(function (index) {
            $(this).find('label').first().html("Reminder time " + (index + 2) + " <span class='text-muted'>(hour time like 18 for 18:00)</span>");
        });
        $('.select2').select2();
    }

    wrapperReminder.on('click', '.btn-remove-reminder', function (e) {
        e.preventDefault();

        $(this).closest('div.row-reminder').remove();
        reorderReminder();
    });
});