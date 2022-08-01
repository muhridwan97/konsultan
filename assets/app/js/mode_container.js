$(function () {
    var boxMode = $('.box-mode.containers');
    var prefixInput = boxMode.data('prefix');
    var totalColumns = boxMode.first().find('th').length;

    var tableDetailInputContainer = $('#table-detail-container tbody');
    var buttonAddContainer = boxMode.find('.btn-add-container');
    var containerTemplate = $('#row-container-template').html();

    buttonAddContainer.on('click', function (e) {
        e.preventDefault();
        checkModeOption(this);

        if (getTotalItem() == 0) {
            tableDetailInputContainer.empty();
        }

        tableDetailInputContainer.append(setPrefix(containerTemplate));
        reorderItem();
    });

    tableDetailInputContainer.on('click', '.btn-remove-container', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputContainer.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Container</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputContainer.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        reinitializeSelect2Library();
    }

    function checkModeOption(element) {
        boxMode = $(element).closest('.box-mode');
        prefixInput = boxMode.data('prefix');
        tableDetailInputContainer = boxMode.find('#table-detail-container tbody');
    }

    function getTotalItem() {
        return parseInt(tableDetailInputContainer.find('tr.row-container').length);
    }

    function setPrefix(template) {
        var newTemplate = $(template);
        newTemplate.find('[name]').each(function () {
            var itemName = $(this).attr('name');
            var itemId = $(this).attr('id');
            $(this).attr('name', prefixInput + itemName);
            $(this).attr('id', prefixInput + itemId);
        });
        return newTemplate;
    }
});