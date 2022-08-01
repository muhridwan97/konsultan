$(function () {
    var boxMode = $('.box-mode.containers-goods');
    var prefixInput = boxMode.data('prefix');
    var totalColumns = boxMode.first().find('th').length;

    var tableDetailInputContainerGoods = $('#table-detail-container-goods > tbody');
    var buttonAddContainerGoods = boxMode.find('.btn-add-container-goods');
    var containerGoodsTemplate = $('#row-container-goods-template').html();
    var itemTemplate = $('#row-container-item-template').html();

    buttonAddContainerGoods.on('click', function (e) {
        e.preventDefault();
        checkModeOption(this);

        if (getTotalItem() == 0) {
            tableDetailInputContainerGoods.empty();
        }

        tableDetailInputContainerGoods.append(setPrefix(containerGoodsTemplate));
        reorderItem();
    });

    tableDetailInputContainerGoods.on('click', '.btn-add-container-item', function (e) {
        e.preventDefault();

        var row = $(this).closest('tr');
        var containerItemWrapper = row.find('tbody');
        if(row.hasClass('row-container')) {
            containerItemWrapper = row.next().find('tbody');
        }

        //var containerItemWrapper = $(this).closest('.row-container').next().find('tbody');

        if (getTotalContainerItem(containerItemWrapper) == 0) {
            containerItemWrapper.empty();
        }
        containerItemWrapper.append(itemTemplate);
        reorderItem();
    });

    tableDetailInputContainerGoods.on('click', '.btn-remove-container-item', function (e) {
        e.preventDefault();
        var containerItem = $(this).closest('tbody');
        $(this).closest('tr').remove();

        reorderContainerItem(containerItem);
        if (getTotalContainerItem(containerItem) == 0) {
            containerItem.append(
                $('<tr>').append($('<td>', {
                    colspan: 9,
                    class: 'text-center'
                }).html('Click <strong>Add New Item</strong> to add item to container'))
            );
        }
    });

    tableDetailInputContainerGoods.on('click', '.btn-remove-container-goods', function (e) {
        e.preventDefault();

        var rowContainer = $(this).closest('tr');
        var rowGoods = rowContainer.next();
        rowContainer.remove();
        rowGoods.remove();

        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputContainerGoods.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Container Goods</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputContainerGoods.find('tr.row-head').each(function (index) {
            $(this).children('td').first().html(index + 1);
            $(this).find('[name]').each(function(i){
                var attributeName = $(this).attr('name').replace(/containers\[([0-9]{0,})?]/i, 'containers['+index+']');
                $(this).attr('name', attributeName);
            });

            $(this).next().find('tbody').first().find('tr.row-item').each(function (counter) {
                $(this).children('td').first().html(counter + 1);
                $(this).find('[name]').each(function(i){
                    var attributeName = $(this).attr('name').replace(/containers\[([0-9]{0,})?]/i, 'containers['+index+']');
                    $(this).attr('name', attributeName);
                });
            });
        });
        reinitializeSelect2Library();
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
        tableDetailInputContainerGoods = boxMode.find('#table-detail-container-goods > tbody');
    }

    function getTotalItem() {
        return parseInt(tableDetailInputContainerGoods.find('tr.row-container').length);
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