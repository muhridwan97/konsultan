$(function () {
    var tableWarehouseReceipt = $('#table-warehouse-receipt.table-ajax');
    var controlTemplate = $('#control-warehouse-receipt-template').html();

    tableWarehouseReceipt.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search warehouse receipt"
        },
        serverSide: true,
        processing: true,
        order: [[0, "desc"]],
        ajax: baseUrl + 'warehouse_receipt/warehouse_receipt_data',
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_warehouse_receipt'},
            {data: 'no_batch'},
            {data: 'customer_name'},
            {data: 'issuance_date'},
            {data: 'duration'},
            {data: 'total_tonnage'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 4,
            render: function (data, type, full, meta) {
                return moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: 6,
            render: function (data, type, full, meta) {
                return numberFormat(data / 1000, 2, ',', '.');
            }
        }, {
            targets: -2,
            render: function (data, type, full, meta) {
                var statusLabel = 'primary';
                if (data === 'PENDING') {
                    statusLabel = 'default';
                } else if (data === 'APPROVED') {
                    statusLabel = 'success';
                } else if (data === 'REJECTED') {
                    statusLabel = 'danger';
                } else if (data === 'EXPIRED') {
                    statusLabel = 'warning';
                }
                return "<span class='label label-" + statusLabel + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_warehouse_receipt}}/g, full.no_warehouse_receipt);

                control = $.parseHTML(control);
                if (full.status !== 'PENDING') {
                    $(control).find('.validate').remove();
                }
                return $('<div />').append($(control)).html();
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    tableWarehouseReceipt.on('click', '.btn-validate-warehouse-receipt', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlValidate = $(this).attr('href');

        var modalValidate = $('#modal-validate-warehouse-receipt');
        modalValidate.find('form').attr('action', urlValidate);
        modalValidate.find('input[name=id]').val(id);
        modalValidate.find('#warehouse-receipt-title').text(label);

        modalValidate.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    var formWarehouseReceipt = $('#form-warehouse-receipt');
    var selectCustomer = formWarehouseReceipt.find('#customer');
    var wrDivider = formWarehouseReceipt.find('#divider').val();
    var stockDataWrapper = formWarehouseReceipt.find('#stock-data-wrapper');
    var stockLoadingWrapper = formWarehouseReceipt.find('#stock-loading-wrapper');

    function fetchStockCustomer(id_customer) {
        stockDataWrapper.html('Fetching data, please wait...');

        $.ajax({
            type: 'GET',
            url: baseUrl + "work-order/ajax_get_stock_by_customer",
            data: {id_customer: id_customer},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                //console.log(JSON.stringify(data, null, 2));
                if (data.trim() == '') {
                    stockDataWrapper.html('<p class="text-danger">No stock data available</p>');
                    stockLoadingWrapper.hide();
                } else {
                    stockDataWrapper.html(data);
                    stockLoadingWrapper.show();
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });

        $('#destination-container-wrapper').find('tr[data-row]').remove();
        $('#destination-container-wrapper').find('tr#placeholder').show();

        $('#destination-item-wrapper').find('tr[data-row]').remove();
        $('#destination-item-wrapper').find('tr#placeholder').show();

        $('#total_items').val('0');
    }

    if (selectCustomer.val() != null && selectCustomer.val() !== '') {
        fetchStockCustomer(selectCustomer.val());
    }

    formWarehouseReceipt.find('#customer').on('change', function () {
        fetchStockCustomer($(this).val());
    });


    initializeLoader();

    function initializeLoader() {
        formWarehouseReceipt.on('click', '.btn-take-all', function () {
            var type = $(this).data('type');

            var tableSource = formWarehouseReceipt.find('#source-' + type + '-wrapper');
            var tableDestination = formWarehouseReceipt.find('#destination-' + type + '-wrapper');

            var rowSource = tableSource.find('tr[data-takeable=1]');
            rowSource.find('input[type=hidden]').attr('disabled', false);
            rowSource.find('.row-job').hide();
            tableDestination.append(rowSource);

            if (tableSource.find('tr[data-row]').length == 0) {
                tableSource.find('#placeholder').show();
            }
            if (tableDestination.find('tr[data-row]').length > 0) {
                tableDestination.find('#placeholder').hide();
            }

            tableDestination.find('.btn-take').text('Return').attr('class', 'btn btn-danger btn-block btn-return');
            $('#total_items').val(parseInt($('#total_items').val()) + rowSource.length);

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });

        formWarehouseReceipt.on('click', '.btn-take', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            stockLoadingWrapper.find('#stock-' + type + '-wrapper').show();

            var tableSource = $(this).closest('#source-' + type + '-wrapper');
            var tableDestination = formWarehouseReceipt.find('#destination-' + type + '-wrapper');

            var rowSource = tableSource.find('[data-row=' + id + ']');
            rowSource.find('input[type=hidden]').attr('disabled', false);
            rowSource.find('.row-job').hide();
            tableDestination.append(rowSource);

            if (tableSource.find('tr[data-row]').length == 0) {
                tableSource.find('#placeholder').show();
            }
            if (tableDestination.find('tr[data-row]').length > 0) {
                tableDestination.find('#placeholder').hide();
            }

            $(this).text('Return').attr('class', 'btn btn-danger btn-block btn-return');

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });

        formWarehouseReceipt.on('click', '.btn-return', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableDestination = $(this).closest('#destination-' + type + '-wrapper');
            var tableSource = formWarehouseReceipt.find('#source-' + type + '-wrapper');

            var rowDestination = tableDestination.find('[data-row=' + id + ']');
            rowDestination.find('input[type=hidden]').attr('disabled', true);
            rowDestination.find('.row-job').show();
            tableSource.append(rowDestination);

            if (tableDestination.find('tr[data-row]').length == 0) {
                tableDestination.find('#placeholder').show();
            }
            if (tableSource.find('tr[data-row]').length > 0) {
                tableSource.find('#placeholder').hide();
            }

            $(this).text('Take').attr('class', 'btn btn-primary btn-block btn-take');

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });
    }

    function reorderTable(table) {
        table.find('tr[data-row]').not('#placeholder').not('.skip-ordering')
            .each(function (index) {
                $(this).children('td').first().html(index + 1);
            });

        $('#total_items').val(stockLoadingWrapper.find('tr[data-row]').length);

        var totalWeight = 0;
        stockLoadingWrapper.find('[name="tonnages[]"]').each(function (key, input) {
            totalWeight += parseFloat($(input).val());
        });
        $('#total_tonnages').val((totalWeight / 1000));
        $('#total_wr').val(Math.ceil((totalWeight / 1000) / wrDivider));
    }

});