$(function () {

    $('.btn-track-invoice').on('click', function (e) {
        e.preventDefault();

        var formTrackInvoice = $('#form-track-invoice');

        var bl = formTrackInvoice.find('[name=bl]').val();
        var containerNo = formTrackInvoice.find('[name=no_container]').val();
        var url = formTrackInvoice.attr('action');

        if(bl || containerNo) {
            var modal = $('#modal-questioner');
            modal.find('form').attr('action', url);
            modal.find('[name=bl]').val(bl);
            modal.find('[name=no_container]').val(containerNo);

            modal.modal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            alert('Please input either BL or container number');
        }

    });
});