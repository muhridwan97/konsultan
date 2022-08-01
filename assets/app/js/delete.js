$(function () {
    var modalDelete = $('#modal-delete');
    var buttonDelete = modalDelete.find('[data-submit]');
    var buttonDismiss = modalDelete.find('[data-dismiss]');
    var form = modalDelete.find('form');

    buttonDelete.on('click', function () {
        form.submit();
    });

    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        if (!url) {
            url = $(this).attr('href');
        }
        form.attr('action', url);
        modalDelete.find('.delete-title').text($(this).data('title'));
        modalDelete.find('.delete-label').text($(this).data('label'));

        modalDelete.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    buttonDismiss.on('click', function () {
        form.attr('action', '#');
        form.find('.delete-title').text('');
        form.find('.delete-label').text('');
    });
});