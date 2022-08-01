$(function () {
    const formSecurityPhotoType = $('#form-security-photo-type');
    const buttonAddPhoto = formSecurityPhotoType.find('.btn-add-photo');
    const rowPhotoTemplate = $('#row-photo-template').html();

    buttonAddPhoto.on('click', function (e) {
        e.preventDefault();

        const tablePhoto = $($(this).data('target'));
        const inputName = $(this).data('name');

        if (tablePhoto.find('tr.row-photo').length === 5) {
            alert('Maximum photo each steps are 5 items');
            return;
        }

        tablePhoto.find('.row-placeholder').hide();

        const newRow = $.parseHTML(rowPhotoTemplate);
        $(newRow).find('.input-photo-title').prop('name', inputName + '[]')

        tablePhoto.find('tbody').append($(newRow));
        reorderItemPhoto(tablePhoto);
    });

    $('.table-photo').on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();

        const tablePhoto = $(this).closest('table');
        const row = $(this).closest('tr');

        row.remove();

        if (tablePhoto.find('tr.row-photo').length === 0) {
            tablePhoto.find('.row-placeholder').show();
        }
        reorderItemPhoto(tablePhoto);
    });

    function reorderItemPhoto(tablePhoto) {
        tablePhoto.find('tr.row-photo').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
    }

});