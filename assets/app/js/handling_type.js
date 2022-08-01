$(function () {
    var formHandlingType = $('#form-handling-type');
    var totalColumns = formHandlingType.first().find('th').length;
    var tableDetailInputComponent = $('#table-detail-handling-component tbody');
    var buttonAddComponent = formHandlingType.find('#btn-add-component');
    var handlingComponentTemplate = $('#row-component-template').html();
    
    var tableDetailInputPhoto = $('#table-detail-attachment-photo tbody');
    var buttonAddPhoto = formHandlingType.find('#btn-add-photo');
    var handlingPhotoTemplate = $('#row-photo-template').html();
    var checkboxPhoto = formHandlingType.find('#photo');
    console.log(checkboxPhoto);
    checkboxPhoto.on('change', function (e) {
        e.preventDefault();

        console.log('asd');
        $(this).is(':checked');
        console.log($(this).is(':checked'));
    });

    buttonAddComponent.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailInputComponent.empty();
        }

        tableDetailInputComponent.append(handlingComponentTemplate);
        reorderItem();
    });

    tableDetailInputComponent.on('click', '.btn-remove-component', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputComponent.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Component</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputComponent.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('.select2').select2();
    }

    function getTotalItem() {
        return parseInt(tableDetailInputComponent.find('tr.row-component').length);
    }

    buttonAddPhoto.on('click', function (e) {
        e.preventDefault();
        if (getTotalItemPhoto() == 0) {
            tableDetailInputPhoto.empty();
        }

        tableDetailInputPhoto.append(handlingPhotoTemplate);
        reorderItemPhoto();
    });

    tableDetailInputPhoto.on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItemPhoto();
        if (getTotalItemPhoto() == 0) {
            tableDetailInputPhoto.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Attachment Photo</strong> to insert new record'))
            );
        }
    });

    function reorderItemPhoto() {
        tableDetailInputPhoto.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('.select2').select2();
    }

    function getTotalItemPhoto() {
        return parseInt(tableDetailInputPhoto.find('tr.row-photo').length);
    }

});