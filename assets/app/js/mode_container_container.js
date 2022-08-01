$(function () {
    var boxMode = $('.box-mode.containers-containers');
    var prefixInput = boxMode.data('prefix');
    var totalColumns = boxMode.first().find('th').length;

    var tableDetailInputContainerContainer = $('#table-detail-container-container > tbody');
    var buttonAddContainerContainer = boxMode.find('.btn-add-container-container');
    var containerContainerTemplate = $('#row-container-container-template').html();
    var containerTemplate = $('#row-container-child-template').html();

    buttonAddContainerContainer.on('click', function (e) {
        e.preventDefault();
        checkModeOption(this);

        if (getTotalItem() == 0) {
            tableDetailInputContainerContainer.empty();
        }

        tableDetailInputContainerContainer.append(setPrefix(containerContainerTemplate));
        reorderItem();
    });

    tableDetailInputContainerContainer.on('click', '.btn-add-container-item', function (e) {
        e.preventDefault();

        var containerItemWrapper = $(this).closest('.row-container').next().find('tbody');

        if (getTotalContainerItem(containerItemWrapper) == 0) {
            containerItemWrapper.empty();
        }
        containerItemWrapper.append(containerTemplate);
        reorderItem();
    });

    tableDetailInputContainerContainer.on('click', '.btn-remove-container', function (e) {
        e.preventDefault();
        var containerItem = $(this).closest('tbody');
        $(this).closest('tr').remove();

        reorderContainerItem(containerItem);
        if (getTotalContainerItem(containerItem) == 0) {
            containerItem.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Container</strong> to add item to container'))
            );
        }
    });

    tableDetailInputContainerContainer.on('click', '.btn-remove-container-container', function (e) {
        e.preventDefault();

        var rowContainer = $(this).closest('tr');
        var rowContainerChildren = rowContainer.next();
        rowContainer.remove();
        rowContainerChildren.remove();

        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputContainerContainer.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Container Master</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputContainerContainer.find('tr.row-head').each(function (index) {
            $(this).children('td').first().html(index + 1);
            $(this).find('[name]').each(function (i) {
                var attributeName = $(this).attr('name').replace(/containers\[([0-9]{0,})?]/i, 'containers[' + index + ']');
                $(this).attr('name', attributeName);
            });

            $(this).next().find('tbody').first().find('tr.row-item').each(function (counter) {
                $(this).children('td').first().html(counter + 1);
                $(this).find('[name]').each(function (i) {
                    var attributeName = $(this).attr('name').replace(/containers\[([0-9]{0,})?]/i, 'containers[' + index + ']');
                    $(this).attr('name', attributeName);
                });
            });
        });
        $('select').select2();
    }

    function reorderContainerItem(wrapper) {
        wrapper.find('tr.row-item').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('select').select2();
    }

    function checkModeOption(element) {
        boxMode = $(element).closest('.box-mode');
        prefixInput = boxMode.data('prefix');
        tableDetailInputContainerContainer = boxMode.find('#table-detail-container-container > tbody');
    }

    function getTotalItem() {
        return parseInt(tableDetailInputContainerContainer.find('tr.row-container').length);
    }

    function getTotalContainerItem(wrapper) {
        return parseInt(wrapper.find('tr.row-item').length);
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