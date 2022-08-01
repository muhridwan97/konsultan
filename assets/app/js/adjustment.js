$(function () {
    var formAdjustment = $('#form-adjustment');
    var adjustmentType = formAdjustment.find('#adjustment_type');
    var select2Warehouse = formAdjustment.find('.select2Warehouse');
    var adjustmentDate = formAdjustment.find('#adjustment_date');
    var panelContainer = formAdjustment.find('#panel-container');
    var panelGoods = formAdjustment.find('#panel-goods');

    var tableContainer = formAdjustment.find('#table-container tbody');
    var tableGoods = formAdjustment.find('#table-goods tbody');

    var panelAdjustment = formAdjustment.find('#panel-adjustment');
    var inputsWrapper = $('#inputs-wrapper');

    var tableAdjustment = formAdjustment.find('#table-adjustment tbody');
    var buttonAdjust = tableAdjustment.find('#btn-adjust');

    adjustmentDate.on('changeDate', function () {
        ajax_adjustment();
    });

    select2Warehouse.on('change', function () {
        ajax_adjustment();
    });

    adjustmentType.on('change', function () {
        ajax_adjustment();
    });

    function ajax_adjustment() {
        if (adjustmentType.val() != '' && select2Warehouse.val() != '' && adjustmentDate.val() != '') {
            $.ajax({
                type: "GET",
                url: baseUrl + "stock_adjustment/ajax_get_stock",
                data: {
                    type: adjustmentType.val(),
                    warehouse: select2Warehouse.val(),
                    adjustmentDate: adjustmentDate.val()
                },
                cache: true,
                accepts: {
                    text: "application/json"
                },
                success: function (data) {
                    console.log(JSON.stringify(data, null, 2));
                    tableContainer.empty();
                    tableGoods.empty();
                    totalItem = 0;

                    if (data.length == 0) {
                        tableContainer.append($('<tr>').append($('<td>', {colspan: 7}).html('<p class="text-danger">No stock data available</p>')));
                        tableGoods.append($('<tr>').append($('<td>', {colspan: 8}).html('<p class="text-danger">No stock data available</p>')));
                    } else {
                        if (adjustmentType.val() == 'C') {
                            getDataStockContainer(data);
                        } else if (adjustmentType.val() == 'G') {
                            getDataStockGoods(data);
                        }
                    }

                    if (adjustmentType.val() == 'C') {
                        panelContainer.show();
                        panelGoods.hide();
                    } else if (adjustmentType.val() == 'G') {
                        panelContainer.hide();
                        panelGoods.show();
                    }
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText + ' ' + status + ' ' + error);
                }
            });
        }
    }

    function getDataStockContainer(data) {
        $.each(data, function (index, value) {
            // $('input[name=work_order]').val(value.id_work_order);
            tableContainer.append($('<tr>', {
                    'data-id': value.id,
                    'data-quantity': value.quantity
                })
                    .append($('<td>').html((index + 1)))
                    .append($('<td>').html(value.no_container))
                    .append($('<td>').html(value.container_size))
                    .append($('<td>').html(value.container_type))
                    .append($('<td>').html(value.seal))
                    .append($('<td>').html(value.position))
                    .append($('<td>').html(value.no_booking))
            );
            totalItem++;
        });
    }

    function getDataStockGoods(data) {
        $.each(data, function (index, value) {
            // $('input[name=work_order]').val(value.id_work_order);
            tableGoods.append($('<tr>', {
                    'data-id': value.id,
                    'data-quantity': value.quantity
                })
                    .append($('<td>').html((index + 1)))
                    .append($('<td>').html(value.goods_name))
                    .append($('<td>').html(value.quantity))
                    .append($('<td>').html(value.unit))
                    .append($('<td>').html(value.tonnage))
                    .append($('<td>').html(value.volume))
                    .append($('<td>').html(value.position))
                    .append($('<td>').html(value.owner_name))
            );
            totalItem++;
        });
    }

    tableContainer.on('click', '#detail_item', function (e) {
        alert("coba");
    });

    tableContainer.on('click', '.btn-remove-item', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        totalItem--;
        reorderItem();

        if (totalItem == 0) {
            tableContainer.append(
                $('<tr>').append($('<td>', {
                    colspan: 9,
                    class: 'text-center'
                }).html('Select Delivery Order To Check Stock'))
            );
        }
    });

    function reorderItem() {
        tableContainer.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
    }

    panelAdjustment.on('click', '.btn-edit-adjustment', function () {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var quantity = row.data('quantity');

        inputsWrapper.append($('<input>', {
            type: 'hidden',
            value: id,
            name: 'old_ids[]'
        }));

        row.find('.column-quantity').html($('<input>', {
            type: 'number',
            value: quantity,
            class: 'form-control',
            name: 'old_quantities[]',
            step: 'any',
            min: '0',
            required: true
        }));

        row.find('.column-action').html($('<button>', {
            class: 'btn btn-danger btn-cancel-adjustment',
            type: 'button'
        }).html('CANCEL'));
    });

    panelAdjustment.on('click', '.btn-cancel-adjustment', function () {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var quantity = row.data('quantity');

        inputsWrapper.find('input[value=' + id + ']').remove();
        row.find('.column-quantity').html(quantity);

        row.find('.column-action').html($('<button>', {
            class: 'btn btn-warning btn-edit-adjustment',
            type: 'button'
        }).html('EDIT'));
    });

    tableAdjustment.on('click', '.btn-adjust', function (e) {
        e.preventDefault();

        var rowAdjust = $(this).closest('tr');

        if (tableContainer.length) {
            tableContainer.append(rowAdjust)
        } else {
            tableGoods.append(rowAdjust);
        }

        rowAdjust.find('input[type=hidden]').attr('disabled', false);
        rowAdjust.find('div[class=hidden]').removeClass('hidden');
        rowAdjust.find('#position').attr('disabled', false);
        rowAdjust.find('#unit').attr('disabled', false);
        rowAdjust.find('#position-label').hide();
        rowAdjust.find('#reason-column').removeClass('hidden');
        rowAdjust.find('#reason').attr('disabled', false);

        if (tableContainer.length) {
            rowAdjust.find('#seal').attr('type', 'text');
            rowAdjust.find('#seal-label').hide();
        } else {
            rowAdjust.find('#quantity').attr('type', 'number');
            rowAdjust.find('#quantity-label').hide();
            rowAdjust.find('#unit').attr('type', 'text');
            rowAdjust.find('#unit-label').hide();
        }

        $(this).removeClass('btn-primary');
        $(this).addClass('btn-danger');
        $(this).text('Cancel');

        if (tableContainer.length) {
            reorderTable(tableContainer);
        } else {
            reorderTable(tableGoods);
        }
        reorderTable(tableAdjustment);
    });

    tableContainer.on('click', '.btn-adjust', function (e) {
        e.preventDefault();

        var rowAdjust = $(this).closest('tr');
        tableAdjustment.append(rowAdjust);

        rowAdjust.find('input[type=hidden]').attr('disabled', true);
        rowAdjust.find('#seal').attr('type', 'hidden');
        rowAdjust.find('#position').attr('type', 'hidden');
        rowAdjust.find('#seal-label').show();
        rowAdjust.find('#position-label').show();
        rowAdjust.find('#reason-column').addClass('hidden');
        rowAdjust.find('#reason').attr('disabled', true);

        // button adjust
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-primary');
        $(this).text('Adjust');

        reorderTable(tableContainer);
        reorderTable(tableAdjustment);
    });

    tableGoods.on('click', '.btn-adjust', function (e) {
        e.preventDefault();

        var rowAdjust = $(this).closest('tr');
        tableAdjustment.append(rowAdjust);

        rowAdjust.find('input[type=hidden]').attr('disabled', true);
        rowAdjust.find('#seal').attr('type', 'hidden');
        rowAdjust.find('#position').attr('type', 'hidden');
        rowAdjust.find('#seal-label').show();
        rowAdjust.find('#position-label').show();
        rowAdjust.find('#reason-column').addClass('hidden');
        rowAdjust.find('#reason').attr('disabled', true);

        // button adjust
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-primary');
        $(this).text('Adjust');

        reorderTable(tableContainer);
        reorderTable(tableAdjustment);
    });

    function reorderTable(table) {
        table.find('tr').not('#placeholder').not('.skip-ordering')
            .each(function (index) {
                $(this).children('td').first().html(index + 1);
            });
    }

    tableAdjustment.on('click', '.btn-approve-adjustment', function (e) {
        e.preventDefault();

        var idAdjustment = $(this).data('id');
        var labelAdjustment = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalApproveAdjustment = $('#modal-approve-adjustment');
        modalApproveAdjustment.find('form').attr('action', urlDelete);
        modalApproveAdjustment.find('input[id]').val(idAdjustment);
        modalApproveAdjustment.find('#adjustment-title').text(labelAdjustment);

        modalApproveAdjustment.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableAdjustment.on('click', '.btn-delete-adjustment', function (e) {
        e.preventDefault();

        var idAdjustment = $(this).data('id');
        var labelAdjustment = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteAdjustment = $('#modal-delete-adjustment');
        modalDeleteAdjustment.find('form').attr('action', urlDelete);
        modalDeleteAdjustment.find('input[id]').val(idAdjustment);
        modalDeleteAdjustment.find('#adjustment-title').text(labelAdjustment);

        modalDeleteAdjustment.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});