$(function () {

    /**
     * url: /booking-rating/{index?}
     * Show booking rating form.
     */
    const tableBookingRating = $('#table-booking-rating');
    const modalRating = $('#modal-rating-booking');

    tableBookingRating.on('click', '.btn-rate', function (e) {
        e.preventDefault();

        const url = $(this).prop('href');
        const noReference = $(this).closest('.row-booking').data('no-reference');
        const rating = $(this).closest('.row-booking').data('rating');
        const description = $(this).closest('.row-booking').data('description');

        modalRating.find('form').prop('action', url);
        modalRating.find('#rate_' + rating).iCheck('check');
        modalRating.find('#booking-title').text(noReference);
        modalRating.find('#description').val(description);

        modalRating.find('.rating-star[data-star]').removeClass('fa-star').removeClass('fa-star-o');
        for (let i = 1; i <= 5; i++) {
            if(i <= rating) {
                modalRating.find('.rating-star[data-star="' + i + '"]').addClass('fa-star');
            } else {
                modalRating.find('.rating-star[data-star="' + i + '"]').addClass('fa-star-o');
            }
        }

        modalRating.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});