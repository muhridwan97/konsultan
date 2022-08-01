$(function () {
    var formBookingNews = $('#form-booking-news');
    var tableDetailInputBooking = formBookingNews.find('#table-detail-booking tbody');
    var buttonAddBooking = formBookingNews.find('.btn-add-booking');
    var bookingTemplate = $('#row-booking-template').html();
    var totalColumns = formBookingNews.find('th').length;

    function getTotalItem() {
        return parseInt(tableDetailInputBooking.find('tr.row-booking').length);
    }

    function reorderItem() {
        tableDetailInputBooking.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('select').select2();
    }

    buttonAddBooking.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailInputBooking.empty();
        }

        tableDetailInputBooking.append(bookingTemplate);
        reorderItem();
    });

    tableDetailInputBooking.on('click', '.btn-remove-booking', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputBooking.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Booking</strong> to insert new record'))
            );
        }
    });

    $('#table-booking-news').on('click', '.btn-delete-booking-news', function (e) {
        e.preventDefault();

        var idBookingNews = $(this).data('id');
        var labelBookingNews = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteBookingNews = $('#modal-delete-booking-news');
        modalDeleteBookingNews.find('form').attr('action', urlDelete);
        modalDeleteBookingNews.find('input[name=id]').val(idBookingNews);
        modalDeleteBookingNews.find('#booking-news-title').text(labelBookingNews);

        modalDeleteBookingNews.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    formBookingNews.find('#no_sprint').mask('S-XXXXX/KPU.XX/BD.XXXX/XXXX', {
        'translation': {
            X: {pattern: /[0-9]/}
        }
    });
});