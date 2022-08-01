$(function () {
    var boxMode = $('.box-mode.goods-goods');
    var prefixInput = boxMode.data('prefix');
    var totalColumns = boxMode.first().find('th').length;

    var tableDetailInputGoodGood = $('#table-detail-goods-goods > tbody');
    var buttonAddGoodItem = boxMode.find('.btn-add-goods-goods');
    var goodsGoodsTemplate = $('#row-goods-goods-template').html();
    var goodsTemplate = $('#row-goods-item-template').html();

    buttonAddGoodItem.on('click', function (e) {
        e.preventDefault();
        checkModeOption(this);

        if (getTotalGoods() == 0) {
            tableDetailInputGoodGood.empty();
        }

        tableDetailInputGoodGood.append(setPrefix(goodsGoodsTemplate));
        reorderGoods();
    });

    tableDetailInputGoodGood.on('click', '.btn-add-goods-item', function (e) {
        e.preventDefault();

        var goodsItemWrapper = $(this).closest('.row-goods').next().find('tbody');

        if (getTotalGoodsItem(goodsItemWrapper) == 0) {
            goodsItemWrapper.empty();
        }
        goodsItemWrapper.append(goodsTemplate);
        reorderGoods();
    });

    tableDetailInputGoodGood.on('click', '.btn-remove-goods-item', function (e) {
        e.preventDefault();
        var goodsItem = $(this).closest('tbody');
        $(this).closest('tr').remove();

        reorderGoodsItem(goodsItem);
        if (getTotalGoodsItem(goodsItem) == 0) {
            goodsItem.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Item</strong> to add item'))
            );
        }
    });

    tableDetailInputGoodGood.on('click', '.btn-remove-goods', function (e) {
        e.preventDefault();

        var rowContainer = $(this).closest('tr');
        var rowContainerChildren = rowContainer.next();
        rowContainer.remove();
        rowContainerChildren.remove();

        reorderGoods();
        if (getTotalGoods() == 0) {
            tableDetailInputGoodGood.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Goods</strong> to insert new record'))
            );
        }
    });

    function reorderGoods() {
        tableDetailInputGoodGood.find('tr.row-head').each(function (index) {
            $(this).children('td').first().html(index + 1);
            $(this).find('[name]').each(function (i) {
                var attributeName = $(this).attr('name').replace(/goods\[([0-9]{0,})?]/i, 'goods[' + index + ']');
                $(this).attr('name', attributeName);
            });

            $(this).next().find('tbody').first().find('tr.row-item').each(function (counter) {
                $(this).children('td').first().html(counter + 1);
                $(this).find('[name]').each(function (i) {
                    var attributeName = $(this).attr('name').replace(/goods\[([0-9]{0,})?]/i, 'goods[' + index + ']');
                    $(this).attr('name', attributeName);
                });
            });
        });
        $('select').select2();
    }

    function reorderGoodsItem(wrapper) {
        wrapper.find('tr.row-item').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('select').select2();
    }

    function checkModeOption(element) {
        boxMode = $(element).closest('.box-mode');
        prefixInput = boxMode.data('prefix');
        tableDetailInputGoodGood = boxMode.find('#table-detail-goods-goods > tbody');
    }

    function getTotalGoods() {
        return parseInt(tableDetailInputGoodGood.find('tr.row-goods').length);
    }

    function getTotalGoodsItem(wrapper) {
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