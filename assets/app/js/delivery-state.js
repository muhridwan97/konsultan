$(function () {
    const formDeliveryState = $('#form-delivery-state');
    const tableItemList = formDeliveryState.find('#table-item-list');
    const tableItemTaken = formDeliveryState.find('#table-item-taken');
    const modalAddGoods = $('#modal-add-goods');
    let activeRow = null;

    tableItemList.on('click', '.btn-take-item', function () {
        const row = $(this).closest('tr');
        const safeConductGoodsId = row.data('safe-conduct-goods-id');
        const noSafeConduct = row.data('no-safe-conduct');
        const goodsName = row.data('goods-name');
        const quantity = row.data('quantity');

        activeRow = row;

        modalAddGoods.find('#id_safe_conduct_goods').val(safeConductGoodsId);
        modalAddGoods.find('#no_safe_conduct').val(noSafeConduct);
        modalAddGoods.find('#goods').val(goodsName);
        modalAddGoods.find('#quantity').val(setNumeric(quantity));
        modalAddGoods.find('#description').val('');

        modalAddGoods.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalAddGoods.on('submit', function (e) {
        e.preventDefault();

        const safeConductGoodsId = modalAddGoods.find('#id_safe_conduct_goods').val();
        const noSafeConduct = modalAddGoods.find('#no_safe_conduct').val();
        const goodsName = modalAddGoods.find('#goods').val();
        const quantity = modalAddGoods.find('#quantity').val();
        const description = modalAddGoods.find('#description').val();
        const lastRow = tableItemTaken.find('tbody tr').not('.row-placeholder').length;

        if (getCurrencyValue(quantity) > Number(activeRow.data('quantity') || 0)) {
            alert('Quantity taken cannot more than ' + setNumeric(activeRow.data('quantity') || 0))
            return false;
        }

        if (getCurrencyValue(quantity) <= 0) {
            alert('Quantity is required')
            return false;
        }

        tableItemTaken.find('tbody').append(`
            <tr>
                <td class="text-center column-no">${lastRow + 1}</td>
                <td>${noSafeConduct}</td>
                <td>${goodsName}</td>
                <td>${quantity}</td>
                <td>${description || '-'}</td>
                <td class="text-center">
                    <input type="hidden" name="safe_conduct_goods[][id_safe_conduct_goods]" id="id_safe_conduct_goods" value="${safeConductGoodsId}">
                    <input type="hidden" name="safe_conduct_goods[][quantity]" id="quantity" value="${getCurrencyValue(quantity)}">
                    <input type="hidden" name="safe_conduct_goods[][description]" id="description" value="${description}">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                        <i class="ion-trash-b"></i>
                    </button>
                </td>
            </tr>
        `);

        if (activeRow != null) {
            const currentQuantity = Number(activeRow.data('quantity') || 0);
            const balance = currentQuantity - getCurrencyValue(quantity);
            $(activeRow).data('quantity', balance);
            $(activeRow).find('.label-quantity').text(setNumeric(balance));
            if (balance <= 0) {
                $(activeRow).hide();
            }
        }

        tableItemTaken.find('.row-placeholder').hide();

        reorderRow();

        modalAddGoods.modal('hide');
    });

    tableItemTaken.on('click', '.btn-remove-item', function () {
        $(this).closest('tr').remove();

        if (tableItemTaken.find('tbody tr').not('.row-placeholder').length === 0) {
            tableItemTaken.find('.row-placeholder').show();
        }

        const safeConductGoodsId = $(this).closest('tr').find('#id_safe_conduct_goods').val();
        const quantity = $(this).closest('tr').find('#quantity').val();

        const itemRow = tableItemList.find('[data-safe-conduct-goods-id="' + safeConductGoodsId + '"]');
        const currentQuantity = Number(itemRow.data('quantity') || 0);
        const balance = currentQuantity + Number(quantity);
        console.log(balance);
        $(itemRow).data('quantity', balance);
        $(itemRow).find('.label-quantity').text(setNumeric(balance));
        itemRow.show();

        reorderRow();
    });

    function reorderRow() {
        tableItemList.find('tbody tr').not('.row-placeholder').each(function (index, el) {
            // recount header number
            $(el).find('.column-no').text(index + 1);
        });

        tableItemTaken.find('tbody tr').not('.row-placeholder').each(function (index, el) {
            // recount header number
            $(el).find('.column-no').text(index + 1);

            // reorder index of inputs
            $(el).find('input[name]').each(function () {
                const pattern = new RegExp("safe_conduct_goods[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'safe_conduct_goods[' + index + ']');
                $(this).attr('name', attributeName);
            });
        });
    }

});