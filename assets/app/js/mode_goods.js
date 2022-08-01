$(function () {
    var boxMode = $('.box-mode.goods');
    var prefixInput = boxMode.data('prefix');
    var totalColumns = boxMode.first().find('th').length;

    var tableDetailInputItem = $('#table-detail-item tbody');
    var buttonAddItem = boxMode.find('.btn-add-item');
    var itemTemplate = $('#row-item-template').html();

    buttonAddItem.on('click', function (e) {
        e.preventDefault();
        checkModeOption(this);

        if (getTotalItem() == 0) {
            tableDetailInputItem.empty();
        }
        tableDetailInputItem.append(setPrefix(itemTemplate));
        reorderItem();
    });

    tableDetailInputItem.on('click', '.btn-remove-item', function (e) {
        e.preventDefault();
        checkModeOption(this);

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputItem.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Item</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputItem.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        reinitializeSelect2Library();
    }

    function checkModeOption(element) {
        boxMode = $(element).closest('.box-mode');
        prefixInput = boxMode.data('prefix');
        tableDetailInputItem = boxMode.find('#table-detail-item tbody');
    }

    function getTotalItem() {
        return parseInt(tableDetailInputItem.find('tr.row-item').length);
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