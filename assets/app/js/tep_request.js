$(function () {
    
    let lastCustomerId = null;
    let lastStockData = null;
    
    let activeRow = null;
    let activeTable = null;
    
    const formTepRequest = $('#form-tep-request');
    const selectCustomer = formTepRequest.find('#customer');
    const selectArmada = formTepRequest.find('#armada');
    const locationWrapper = formTepRequest.find('#location-wrapper');
    const slotWrapper = formTepRequest.find('#slot-wrapper');
    const inputSlot = formTepRequest.find('#slot');
    const inputLocation = formTepRequest.find('#location');
    const inputTepDateCustomer = formTepRequest.find('#tep_date');

    const formCreateTepRequest = $('#form-create-tep-request');
    const selectRequest = formCreateTepRequest.find('#tep_request');
    const tableGoodsSet = formCreateTepRequest.find('#table-goods');
    const inputTepDate = formCreateTepRequest.find('#tep_date');

    const goodsEditor = $('.goods-editor');
    const tableGoods = goodsEditor.find('#table-goods');
    /**
     * Take goods from stock
     * populate modal and button take from stock
     */
     const btnStockGoods = $('#btn-stock-goods');
     const modalStockGoods = $('#modal-stock-goods');
     const modalTakeStockGoods = $('#modal-take-stock-goods');
     const btnReloadStockGoods = modalStockGoods.find('#btn-reload-stock');
     const btnTakeAllStockGoods = modalStockGoods.find('#btn-take-all-stock');

     // populate inputs from modal take goods value from stock
     const inputQuantityTake = modalTakeStockGoods.find('#quantity');

     /**
     * Show modal that contain stock goods list data,
     * save last stock data for reduce server request,
     * give delay because we waiting parent modal to be closed.
     */
    btnStockGoods.on('click', function (e) {        
        e.preventDefault();
        activeTable = tableGoods;
        showModalStockGoods();
    });

    function showModalStockGoods(delay = 0) {
        const customerId = modalStockGoods.data('customer-id');

        if (customerId && customerId !== lastCustomerId) {
            getGoodsStock(customerId);
        } else {
            initGoodsStock(lastStockData);
        }

        setTimeout(() => {
            modalStockGoods.modal({
                backdrop: 'static',
                keyboard: false
            });
        }, delay);
    }
    /**
     * Fetch stock goods outbound request.
     * @param customerId
     * 
     */
     function getGoodsStock(customerId) {
        let stockUrl = `${baseUrl}transporter-entry-permit/ajax_get_stock_by_customer?id_customer=${customerId}`;

        modalStockGoods.find('tbody').html(`
            <tr><td colspan="10">Fetching stock goods...</td></tr>
        `);

        fetch(stockUrl)
            .then(result => result.json())
            .then(stock => {
                // save last stock data so we do not need to fetch again
                lastCustomerId = customerId;
                lastStockData = stock;
                initGoodsStock(lastStockData);
            })
            .catch(console.log);
    }

    /**
     * initialize goods stock list in table from source data.
     * @param stock
     */
     function initGoodsStock(stock) {        
        const goods = stock ? stock.goods : [];
        if (goods.length) {
            modalStockGoods.find('tbody').empty();

            // find taken stock
            const takenStock = Array.from(goodsEditor.find('.row-stock')).map((row) => {
                return {
                    id_goods: $(row).find('#id_goods').val(),
                    id_unit: $(row).find('#id_unit').val(),
                    id_upload: $(row).find('#id_upload').val(),
                    quantity: $(row).find('#quantity').val(),
                    no_reference: $(row).find('#no_reference').val(),
                };
            });

            // loop through the stock
            let order = 1;
            goods.forEach((item) => {
                let leftQuantity = item.stock_outbound;

                takenStock.forEach((taken, index) => {
                    if (taken.id_goods === item.id_goods && taken.id_unit === item.id_unit && taken.no_reference === (item.no_reference_outbound) && !taken['_taken']) {
                        leftQuantity -= taken.quantity;
                        takenStock[index]['_taken'] = true;
                    }
                });

                leftQuantity = roundVal(leftQuantity);

                // this is important, we clone the object so the stock data is not changed
                let itemData = {...item};
                itemData.stock_quantity = leftQuantity;

                if (leftQuantity > 0) {
                    const row = `
                        <tr class="${item._is_hold ? 'goods-list-hold text-muted' : ''}" ${item._is_hold ? 'title="This item is hold right now"' : ''}>
                            <td>${order++}</td>
                            <td id="no-reference">${itemData.no_reference_outbound}</td>
                            <td id="no-invoice">${itemData.no_invoice}</td>
                            <td id="no-bl">${itemData.no_bl}</td>
                            <td id="no-goods-label">${itemData.no_goods}</td>
                            <td id="goods-label">${itemData.goods_name}</td>
                            <td id="whey-number-label">${itemData.whey_number || '-'}</td>
                            <td id="unit-label">${itemData.unit}</td>
                            <td id="quantity-label">${setCurrencyValue(Number(leftQuantity || 0), '', ',', '.')}</td>
                            <td class="text-center sticky-col-right">
                                <input type="hidden" name="goods-data" id="goods-data" value="${encodeURIComponent(JSON.stringify(itemData))}">
                                <button class="btn btn-sm ${item._is_hold ? '' : 'btn-primary'} btn-take-goods" type="button" ${item._is_hold ? 'disabled' : ''}>
                                    TAKE
                                </button>
                            </td>
                        </tr>
                    `;
                    modalStockGoods.find('tbody').first().append(row);
                }

            });
        } else {
            modalStockGoods.find('tbody').html(`
                <tr><td colspan="10">No data available</td></tr>
            `);
        }
    }

    /**
     * Take goods from stock list, populate into new modal that contains
     * quantity, weight, volume and dimension selection.
     */
     modalStockGoods.on('click', '.btn-take-goods', function () {
        activeRowGoodsTake = $(this).closest('tr');

        let goods = $(this).closest('tr').find('#goods-data').val();
        goods = JSON.parse(decodeURIComponent(goods));

        modalTakeStockGoods.find('#label-goods').text(goods.goods_name);
        modalTakeStockGoods.find('#label-unit').text(goods.unit);

        inputQuantityTake.val(setCurrencyValue(Number(goods.stock_quantity || 0), '', ',', '.')).data('default', goods.stock_quantity);

        modalTakeStockGoods.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    btnTakeAllStockGoods.on('click', function () {
        $('#table-stock-goods').find('tbody tr:not(.goods-list-hold)').each(function (i, row) {

            let goods = $(row).find('#goods-data').val();
            goods = JSON.parse(decodeURIComponent(goods));

            inputQuantityTake.val(setCurrencyValue(Number(goods.stock_quantity || 0), '', ',', '.')).data('default', goods.stock_quantity);

            activeRowGoodsTake = $(row);
            modalTakeStockGoods.find('#btn-take-goods-stock').click();
        });
    });

    /**
     * Take goods from stock and put them into table goods list.
     * 1. populate inputs value
     * 2. calculate left stock by subtracting inputs value (update stock table, delete if quantity is 0 or less)
     * 3. add inputs that taken into goods table (depends on active table, see addGoods())
     */
     modalTakeStockGoods.find('#btn-take-goods-stock').on('click', function () {
        // get input goods are taken
        const quantity = getCurrencyValue(inputQuantityTake.val());

        // calculate left value from default stock
        const leftQuantity = roundVal(inputQuantityTake.data('default') - quantity);

        const inputs = Array.from(modalTakeStockGoods.find('input'));
        for (let input of inputs) {
            const title = $(input).attr('id').replace('_', ' ').toUpperCase();
            const max = Number($(input).data('default') || 0);
            const value = getCurrencyValue($(input).val() || 0);
            if (value > max || value < 0) {
                alert(`${title} must bellow than ${setCurrencyValue(max, '', ',', '.')} (the value should not below than 0)`);
                return;
            }
        }

        // put data into table goods (taken one)
        let goods = activeRowGoodsTake.find('#goods-data').val();
        goods = JSON.parse(decodeURIComponent(goods));
        goods.quantity = quantity;
        addGoods(goods, true);

        // set current stock data
        goods.stock_quantity = leftQuantity;

        activeRowGoodsTake.find('#goods-data').val(encodeURIComponent(JSON.stringify(goods)));

        // update left quantity data in stock
        if (leftQuantity <= 0) {
            activeRowGoodsTake.remove();
            activeRowGoodsTake = null;
        } else {
            activeRowGoodsTake.find('#quantity-label').text(setCurrencyValue(leftQuantity || 0, '', ',', '.'));
        }

        // close take goods modal
        modalTakeStockGoods.modal('hide');
    });

    function addGoods(goods, fromStock = false) {
        let rowLabel = 'row-header';
        let inputName = 'goods[]';

        let targetTable = activeTable;
        let ex_no_container = activeTable.find('#no_container_text').val();

        // remove placeholder if exist
        if (targetTable.find('.row-placeholder').length) {
            targetTable.find('.row-placeholder').remove();
        }

        const lastRow = targetTable.find('tbody tr').length;

        let control = `
            <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                <i class="ion-trash-b"></i>
            </button>
        `;

        // create new record and hidden input of table
        const row = `
            <tr class="${rowLabel}${fromStock ? ' row-stock' : ''}" data-id="${uniqueId()}">
                <td>${lastRow + 1}</td>
                <td id="no-reference">${goods.no_reference_outbound}</td>
                <td id="no-invoice">${goods.no_invoice}</td>
                <td id="no-bl">${goods.no_bl}</td>
                <td id="no-goods-label">${goods.no_goods}</td>
                <td id="goods-label">${goods.goods_name}</td>
                <td id="whey-number-label">${goods.whey_number || '-'}</td>
                <td id="unit-label">${goods.unit}</td>
                <td id="quantity-label">${setCurrencyValue(Number(goods.quantity || 0), '', ',', '.')}</td>
                <td class="sticky-col-right">
                    <input type="hidden" id="goods-data" value="${encodeURIComponent(JSON.stringify(goods))}">
                    <input type="hidden" name="${inputName}[id_goods]" id="id_goods" value="${goods.id_goods}">
                    <input type="hidden" name="${inputName}[quantity]" id="quantity" value="${goods.quantity}">
                    <input type="hidden" name="${inputName}[work_order_quantity]" id="work_order_quantity" value="${goods.work_order_quantity}">
                    <input type="hidden" name="${inputName}[id_unit]" id="id_unit" value="${goods.id_unit}">
                    <input type="hidden" name="${inputName}[id_upload]" id="id_upload" value="${goods.id_upload}">
                    <input type="hidden" name="${inputName}[goods_name]" id="goods_name" value="${goods.goods_name}">
                    <input type="hidden" name="${inputName}[no_invoice]" id="no_invoice" value="${goods.no_invoice}">
                    <input type="hidden" name="${inputName}[no_bl]" id="no_bl" value="${goods.no_bl}">
                    <input type="hidden" name="${inputName}[no_goods]" id="no_goods" value="${goods.no_goods}">
                    <input type="hidden" name="${inputName}[unit]" id="unit" value="${goods.unit}">
                    <input type="hidden" name="${inputName}[whey_number]" id="whey_number" value="${goods.whey_number}">
                    <input type="hidden" name="${inputName}[ex_no_container]" id="ex_no_container" value="${goods.ex_no_container}">
                    <input type="hidden" name="${inputName}[no_reference]" id="no_reference" value="${goods.no_reference_outbound || ''}">
                    <input type="hidden" name="${inputName}[hold_status]" id="hold_status" value="${goods.hold_status || ''}">
                    <input type="hidden" name="${inputName}[priority]" id="priority" value="${goods.priority || ''}">
                    <input type="hidden" name="${inputName}[priority_description]" id="priority_description" value="${goods.priority_description || ''}">
                    <input type="hidden" name="${inputName}[unload_location]" id="unload_location" value="${goods.unload_location || ''}">
                    ${control}
                </td>
            </tr>
        `;
        targetTable.find('tbody').first().append(row);
        reorderRow();
    }
    /**
     * update weight, volume, and dimension props by quantity, see calculateByQuantity()
     * update only if the inputs are readonly (because we can edit manually the value)
     */
     modalTakeStockGoods.find('#quantity').on('input', function () {
        const qtyDefault = inputQuantityTake.data('default');
        const qtyTake = getCurrencyValue(inputQuantityTake.val());
    });

    /**
     * Rounded floating point into nearest group precision.
     * @param value
     * @param precision
     * @returns {number}
     */
     function roundVal(value, precision = 3) {
        const multiplier = (function () {
            if (precision === 3) return 1000;

            let digit = '1';
            for (let i = 0; i < precision; i++) {
                digit += '0';
            }
            return Number(digit);
        })();
        return Math.round(value * multiplier) / multiplier;
    }

    /**
     * remove goods from button in modal goods editor,
     * remove table row and check if table is empty and reorder the rest if not.
     */
     $('.content-wrapper').on('click', '.btn-remove-goods', function (e) {
        e.preventDefault();

        if ($(this).parent('td').length) {
            activeRow = $(this).closest('tr');
            activeTable = $(this).closest('table');
        }

        // remove detail first if exist
        if (!activeRow.next('tr.row-detail')) {
            activeRow.next().remove();
        }

        // remove current row
        activeRow.remove();

        // remove empty table in sub row
        if (activeTable.attr('id') !== 'table-goods' && activeTable.find('tr.row-goods').length === 0) {
            activeTable.closest('tr').remove();
        }

        // add placeholder if empty or if not reorder the list
        let row = tableGoods.find('tbody tr');
        if (row.length === 0) {
            const placeholder = `<tr class="row-placeholder"><td colspan="10">No goods data</td></tr>`;
            tableGoods.find('tbody').html(placeholder);
        } else {
            reorderRow();
        }
    });
    /**
     * reorder the row, find out it a nested row of header row.
     */
     function reorderRow() {

        tableGoods.find('tr.row-header').each(function (index) {
            // recount header number
            $(this).children('td').first().html((index + 1).toString());

            // reorder index of inputs
            $(this).find('input[name]').each(function () {
                const pattern = new RegExp("goods[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'goods[' + index + ']');
                $(this).attr('name', attributeName);
            });
            

            // check if it has detail
            $(this).next('tr.row-detail').find('tr.row-goods').each(function (count) {
                // reorder detail number
                $(this).children('td').first().html((count + 1).toString());

                // reorder index of inputs
                $(this).find('input[name]').each(function () {
                    const patternParent = new RegExp("goods[([0-9]*\\)?]", "i");
                    const attributeNameParent = $(this).attr('name').replace(patternParent, 'goods[' + index + ']');
                    $(this).attr('name', attributeNameParent);

                    const pattern = new RegExp("\\[goods\\][([0-9]*\\)?]", "i");
                    const attributeName = $(this).attr('name').replace(pattern, '[goods][' + count + ']');
                    $(this).attr('name', attributeName);
                });
            });
        });

        setTimeout(function () {
            setTableViewport();
        }, 300);
    }
    
    /**
     * Reload stock data if there is differences in last stock from server.
     */
     btnReloadStockGoods.on('click', function () {
        const customerId = modalStockGoods.data('customer-id');
        if (customerId) {
            getGoodsStock(customerId);
        }
    });

    selectCustomer.on('change',function (e) {
        e.preventDefault();
        var customerId = $(this).val();
       
        modalStockGoods.data('customer-id', customerId);
        
        selectBooking = formTepRequest.find('#booking');
        selectBooking.empty();
        // formTepRequest.find('#aju').data("placeholder", "Fetching data...");
        const params = $.param({
            id_customer: customerId
        });

        fetch(`${baseUrl}transporter-entry-permit/ajax_get_available_sppb?${params}`)
            .then(result => result.json())
            .then(data => {
                selectBooking.val(data.length>0?'have':'');
            })
            .catch(err => {
                console.log(err);
                selectBooking.prop("disabled", false);
            });
    });

    selectArmada.on('change',function (e) {
        e.preventDefault();
        var armada = $(this).val();
       
        if (armada == 'TCI') {
            locationWrapper.show();
            slotWrapper.hide();
            inputLocation.prop('required',true);
            inputSlot.prop('required',false);
            inputTepDateCustomer.prop('required',false);
        }else{
            locationWrapper.hide();
            slotWrapper.show();
            inputLocation.prop('required',false);
            inputSlot.prop('required',true);
            inputTepDateCustomer.prop('required',true);
        }
    });

    selectRequest.on('change',function (e) {
        e.preventDefault();
        var requestId = $(this).val();
       
        // formTepRequest.find('#aju').data("placeholder", "Fetching data...");
        const params = $.param({
            id_request: requestId
        });
        tableGoodsSet.find('tbody').html(`
            <tr><td colspan="10">Fetching stock goods...</td></tr>
        `);
        let order = 1;
        let index = 0
        fetch(`${baseUrl}transporter-entry-permit/ajax_get_goods_by_id_request?${params}`)
            .then(result => result.json())
            .then(data => {
                let item = data.goods;
                tableGoodsSet.find('tbody').empty();
                item.forEach((goods) => {
                let row = `
                        <tr>
                            <td>${order++}
                            <input type="hidden" name="goods[${index}][id_goods]" id="id_goods" value="${goods.id_goods}">
                            <input type="hidden" name="goods[${index}][quantity]" id="quantity" value="${goods.quantity}">
                            <input type="hidden" name="goods[${index}][work_order_quantity]" id="work_order_quantity" value="${goods.work_order_quantity}">
                            <input type="hidden" name="goods[${index}][id_unit]" id="id_unit" value="${goods.id_unit}">
                            <input type="hidden" name="goods[${index}][id_upload]" id="id_upload" value="${goods.id_upload}">
                            <input type="hidden" name="goods[${index}][goods_name]" id="goods_name" value="${goods.goods_name}">
                            <input type="hidden" name="goods[${index}][no_invoice]" id="no_invoice" value="${goods.no_invoice}">
                            <input type="hidden" name="goods[${index}][no_bl]" id="no_bl" value="${goods.no_bl}">
                            <input type="hidden" name="goods[${index}][no_goods]" id="no_goods" value="${goods.no_goods}">
                            <input type="hidden" name="goods[${index}][unit]" id="unit" value="${goods.unit}">
                            <input type="hidden" name="goods[${index}][whey_number]" id="whey_number" value="${goods.whey_number}">
                            <input type="hidden" name="goods[${index}][ex_no_container]" id="ex_no_container" value="${goods.ex_no_container}">
                            <input type="hidden" name="goods[${index}][hold_status]" id="hold_status" value="${goods.hold_status || 'OK'}">
                            <input type="hidden" name="goods[${index}][unload_location]" id="unload_location" value="${goods.unload_location || ''}">
                            <input type="hidden" name="goods[${index}][priority]" id="priority" value="${goods.priority || ''}">
                            <input type="hidden" name="goods[${index}][priority_description]" id="priority_description" value="${goods.priority_description || ''}">
                            <input type="hidden" name="goods[${index++}][no_reference]" id="no_reference" value="${goods.no_reference || ''}">
                            </td>
                            <td id="no-reference">${goods.no_reference}</td>
                            <td id="no-invoice">${goods.no_invoice}</td>
                            <td id="no-bl">${goods.no_bl}</td>
                            <td id="no-goods-label">${goods.no_goods}</td>
                            <td id="goods-label">${goods.goods_name}</td>
                            <td id="whey-number-label">${goods.whey_number || '-'}</td>
                            <td id="unit-label">${goods.unit}</td>
                            <td id="quantity-label">${setCurrencyValue(Number(goods.quantity || 0), '', ',', '.')}</td>
                        </tr>
                    `;
                    tableGoodsSet.find('tbody').first().append(row);
                });
            })
            .catch(console.log);
    });

    inputTepDate.on('change',function (e) {
        e.preventDefault();
        var tepDate = $(this).val();
       
        const params = $.param({
            tep_date: tepDate
        });

        formCreateTepRequest.find('#date_remain').text(tepDate);
        fetch(`${baseUrl}transporter-entry-permit/ajax_slot_tep_by_date?${params}`)
            .then(result => result.json())
            .then(data => {
                formCreateTepRequest.find('#slot_remain').text(data.slot);
                formCreateTepRequest.find('#total_code').prop('max',data.slot);
                formCreateTepRequest.find('#tep_time').prop('min',data.min_time);
            })
            .catch(err => {
                console.log(err);
            });
    });

    formCreateTepRequest.find('#email_type').on('change', function () {
        if($(this).val() === 'INPUT') {
            formCreateTepRequest.find('#email-input-field').show();
            formCreateTepRequest.find('#input_email').prop('required', true);
            formCreateTepRequest.find('#email-type-field')
                .addClass('col-md-4')
                .removeClass('col-md-8');
        } else {
            formCreateTepRequest.find('#email-input-field').hide();
            formCreateTepRequest.find('#input_email').prop('required', false);
            formCreateTepRequest.find('#email-type-field')
                .addClass('col-md-8')
                .removeClass('col-md-4');
        }
    });

    formCreateTepRequest.find("#tep_before").on('change', function(){
        var tep_before = $(this).val();
        if(tep_before == "yes"){
            formCreateTepRequest.find("#tep-reference-field").show();
            formCreateTepRequest.find("#tep_reference").attr("required", true);
            formCreateTepRequest.find('#tep-before-field')
                .addClass('col-md-6')
                .removeClass('col-md-12');
        }else{
            formCreateTepRequest.find("#tep-reference-field").hide();
            formCreateTepRequest.find("#tep_reference").attr("required", false);
            formCreateTepRequest.find('#tep-before-field')
                .addClass('col-md-12')
                .removeClass('col-md-6');
        }

        var id_request = selectRequest.val();
        console.log(id_request);
        formCreateTepRequest.find('#tep_reference').data("placeholder", "Fetching data...");
        formCreateTepRequest.find('#tep_reference').select2();
        $.ajax({
            url: baseUrl + 'transporter_entry_permit/get_tep_reference_by_request',
            type: 'POST',
            data: {
                id_request: id_request
            },
            success: function (data) {
                console.log(data);
                formCreateTepRequest.find('#tep_reference').children('option').remove();
                data.forEach(function(data){
                    formCreateTepRequest.find('#tep_reference').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.tep_code+" ("+data.receiver_no_police+"-"+data.receiver_vehicle+")")); 
                  });
                if (data == '') {
                    formCreateTepRequest.find('#tep_reference').data("placeholder", "No data...");
                    formCreateTepRequest.find('#tep_reference').select2();
                }
            },
            error: function() { 
                alert("gagal memperoleh tep reference");
            }  
        });
    }); 

    //upload do file
    const formTEPReqInbound = $('#form-tep-request-inbound');
    const inputTepDateInbound = formTEPReqInbound.find('#tep_date');
    let doWrapper = $('#do-wrapper');
    let btnTEPReqInbound = formTEPReqInbound.find('button[type="submit"]');
    $('.upload-do').fileupload({
        url: baseUrl + 'transporter_entry_permit/upload_s3',
        dataType: 'json',
        done: function (e, data) {
            var inputFileParent = $(this).closest('.form-group');
            inputFileParent.find('.text-danger').remove();
            $.each(data.result, function (index, file) {
                if (file != null && file.status) {
                    inputFileParent.find('.uploaded-file')
                        .append($('<p/>', {class: 'text-muted text-ellipsis'})
                            .html('<a href="#" data-file="' + file.data.file_name + '" class="btn btn-danger btn-sm btn-delete-file">DELETE</a> &nbsp; ' + file.data.client_name));
                    inputFileParent.find('.upload-input-wrapper')
                        .append($('<input/>', {
                            type: 'hidden',
                            name: 'memo[]',
                            value: file.data.file_name
                        }));
                } else {
                    inputFileParent.find('.progress-bar')
                        .addClass('progress-bar-danger')
                        .text('Upload failed').css(
                        'width', '100%'
                    );
                    inputFileParent.find('.uploaded-file')
                        .append($(file.errors).addClass('text-danger'));
                }
            });
            checkButtonUpload(inputFileParent);
            btnTEPReqInbound.attr('disabled',false);
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                'width',
                progress + '%'
            ).text(progress + '%');
            btnTEPReqInbound.attr('disabled',true);
        },
        fail: function (e, data) {
            alert(data.textStatus);
            btnTEPReqInbound.attr('disabled',false);
        }
    });
    doWrapper.on('click', '.btn-delete-file', function (e) {
        e.preventDefault();
        var buttonDelete = $(this);
        var file = buttonDelete.data('file');
        $.ajax({
            url: baseUrl + 'upload_document_file/delete_temp_s3',
            type: 'POST',
            data: {
                file: file
            },
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                if (data.status || data.status == 'true') {
                    var inputFileParent = buttonDelete.closest('.form-group');
                    inputFileParent.find('input[value="' + file + '"]').remove();
                    buttonDelete.parent().remove();
                    checkButtonUpload(inputFileParent);
                    alert('File ' + file + ' is deleted');
                } else {
                    alert('Failed delete uploaded file');
                }
            }
        })
    });
    
    function checkButtonUpload(wrapper) {
        if (wrapper.find('.upload-input-wrapper').children().length) {
            wrapper.find('.button-file').text('More file');
        } else {
            wrapper.find('.button-file').text('Select file');
            wrapper.find('.progress-bar')
                .removeClass('progress-bar-danger')
                .addClass('progress-bar-success')
                .css(
                    'width', '0%'
                );
        }
    }

    formTEPReqInbound.on('submit', function () {

        //check upload document
        const requiredDocuments = $(this).find('.required-document');
        let hasDocument = true;
        if (requiredDocuments.length) {
            requiredDocuments.each(function (index, element) {
                if ($(element).find('.upload-input-wrapper').children().length === 0) {
                    hasDocument = false;
                }
            });
        }

        if (!hasDocument) {
            alert('Upload document DO/Memo');
            return false;
        }
        return true;
    });

    inputTepDateInbound.on('change',function (e) {
        e.preventDefault();
        var tepDate = $(this).val();
       
        const params = $.param({
            tep_date: tepDate
        });

        formTEPReqInbound.find('#date_remain').text(tepDate);
        fetch(`${baseUrl}transporter-entry-permit/ajax_slot_tep_by_date?${params}`)
            .then(result => result.json())
            .then(data => {
                formTEPReqInbound.find('#slot_remain').text(data.slot);
                formTEPReqInbound.find('#total_code').prop('max',data.slot);
                formTEPReqInbound.find('#tep_time').prop('min',data.min_time);
            })
            .catch(err => {
                console.log(err);
            });
    });

    inputTepDateCustomer.on('change',function (e) {
        e.preventDefault();
        var tepDate = $(this).val();
       
        const params = $.param({
            tep_date: tepDate
        });

        formTepRequest.find('#date_remain').text(tepDate);
        fetch(`${baseUrl}transporter-entry-permit/ajax_slot_tep_by_date?${params}`)
            .then(result => result.json())
            .then(data => {
                formTepRequest.find('#slot_remain').text(data.slot);
                formTepRequest.find('#total_code').prop('max',data.slot);
                formTepRequest.find('#tep_time').prop('min',data.min_time);
            })
            .catch(err => {
                console.log(err);
            });
    });

    var temp_tanggal = $('.requestDatepicker').data('min-date');
    var holidayDate = $('.requestDatepicker,.requestTciDatepicker').data('holiday-date');
    
    if(holidayDate == undefined || holidayDate == ''){
        holidayDate = [];
    }else{
        holidayDate = holidayDate.split(",");
    }
    var minTanggal = new Date(temp_tanggal);
    var today = new Date();
    
    var hariCek = today.getDay();
    var maxTanggal;
    switch (hariCek) {
        case 5:
            maxTanggal = new Date(today.setDate(today.getDate() + 3));
            break;
        case 6:
            maxTanggal = new Date(today.setDate(today.getDate() + 2));
            break;
        default:
            maxTanggal = new Date($('#tep_date').val());
            break;
    }
    
    $('.requestDatepicker').datepicker({
        format: "dd MM yyyy",
        beforeShowDay:  function(in_date) {
            let hari = in_date.getDay();
            let tanggal = in_date.getFullYear() + "-" + (((in_date.getMonth()+1)<10)? "0"+(in_date.getMonth()+1) : (in_date.getMonth()+1)) + "-" + ((in_date.getDate()<10)? "0"+in_date.getDate() : in_date.getDate());
            if (hari == 0 || $.inArray(tanggal, holidayDate) != -1) {
                return false;
            } else {
                return true;
            }
        },
        autoclose: true,
        startDate: minTanggal,
        endDate:maxTanggal
    });

    var minTanggalTci = new Date();
    $('.requestTciDatepicker').datepicker({
        format: "dd MM yyyy",
        beforeShowDay:  function(in_date) {
            let hari = in_date.getDay();
            let tanggal = in_date.getFullYear() + "-" + (((in_date.getMonth()+1)<10)? "0"+(in_date.getMonth()+1) : (in_date.getMonth()+1)) + "-" + ((in_date.getDate()<10)? "0"+in_date.getDate() : in_date.getDate());
            console.log(tanggal + " : "+$.inArray(tanggal, holidayDate))
            if (hari == 0 || $.inArray(tanggal, holidayDate) != -1) {
                return false;
            } else {
                return true;
            }
        },
        autoclose: true,
        startDate: minTanggalTci,
    });
    
});