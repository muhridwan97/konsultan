$(function () {

    /**
     * Manage booking import preview from XML data.
     * url: /booking-import/preview?import=1&upload=469&file=NWQzZTc1MGE4MzFjMy54bWw=
     */

    // fix from select2's search not working in modal
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
    };

    const formBookingPreview = $('#form-booking-preview');
    const inputEditor = $('.input-editor');
    const tableContainer = inputEditor.find('#table-container');
    const modalContainerInput = $('#modal-container-input');
    const modalContainerView = $('#modal-container-view');
    const modalInfo = $('#modal-info');

    const tableGoods = $('#table-goods');
    const modalGoodsInput = $('#modal-goods-input');
    const modalStockGoods = $('#modal-stock-goods');
    const modalTakeStockGoods = $('#modal-take-stock-goods');
    const tableStockGoods = $('#table-stock-goods');
    const modalStockGoodsTableList = $('#modal-stock-goods-table-list');
    const tableStockGoodsTableList = $('#table-stock-goods-table-list');
    const createPackage = getParameterByName('create_package') || false;

    let activeRow = null;
    let activeTable = null;

    // rename stock button
    $('#btn-stock-goods').text('Take XML Goods');

    tableContainer.on('click', '.btn-add-detail-from-list', function (e) {
        activeTable = tableContainer;
        activeRow = $(this).closest('tr');

        modalStockGoodsTableList.find('#label-reference').text(formBookingPreview.find('#no_reference').val());
        tableStockGoodsTableList.find('tbody').empty();
        tableGoods.find('.row-header').each(function (index, row) {
            let goodsRow = $(row).clone();
            goodsRow.find('button').remove();
            goodsRow.find('.sticky-col-right').append(`<button class="btn btn-sm btn-primary btn-take-goods-row" type="button">TAKE</button>`);
            tableStockGoodsTableList.find('tbody').append(goodsRow);
        });
        modalStockGoodsTableList.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableContainer.on('click', '.btn-remove-goods-row', function (e) {
        activeTable = $(this).closest('table');
        const header = $(this).closest('.row-header');
        const detail = header.next();

        let rowId = $(this).closest('.row-header').data('id');

        // return name key
        header.find('input[name]').each(function () {
            const patternParent = new RegExp("^containers[([0-9]*\\)?]\\[goods\\]", "i");
            const attributeNameParent = $(this).attr('name').replace(patternParent, 'goods');
            $(this).attr('name', attributeNameParent);
        });

        header.find('.btn-remove-goods-row').remove();
        header.find('button').show();
        tableGoods.find('tbody').first().append(header);

        if (detail.hasClass('row-detail')) {
            detail.find('input[name]').each(function () {
                const patternParent = new RegExp("^containers[([0-9]*\\)?]\\[goods\\]", "i");
                const attributeNameParent = $(this).attr('name').replace(patternParent, 'goods');
                $(this).attr('name', attributeNameParent);
            });

            detail.find('button').show();
            tableGoods.find('tbody').first().append(detail);
        }

        let headerGoods = tableGoods.find('.row-header[data-id=' + rowId + ']');
        headerGoods.find('#ex-no-container-label').text('-');
        headerGoods.find('#ex_no_container').val('');

        // remove empty table in sub row
        if (activeTable.find('.row-header').length === 0) {
            activeTable.closest('tr').remove();
        }
        reorderRow();
    });

    modalStockGoodsTableList.on('click', '.btn-take-goods-row', function () {
        // create header table in nested current table for goods
        if (!activeRow.next().hasClass('row-detail')) {
            const tableNested = `
                <tr class="row-detail">
                    <td></td>
                    <td colspan="17">
                        <table class="table table-condensed table-bordered no-datatable responsive">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Ex Container</th>
                                <th>Unit Weight (Kg)</th>
                                <th>Total Weight (Kg)</th>
                                <th>Unit Gross (Kg)</th>
                                <th>Total Gross (Kg)</th>
                                <th>Unit Volume (M<sup>3</sup>)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Position</th>
                                <th>Pallet</th>
                                <th>Is Hold</th>
                                <th>Status</th>
                                <th>Danger</th>
                                <th class="sticky-col-right">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </td>
                </tr>
            `;
            $(tableNested).insertAfter(activeRow);
        }
        // set active table to nested table, so code bellow works as expected
        activeTable = activeRow.next().find('table');
        // find selected row in goods table
        const row = $(this).closest('.row-header');
        const rowId = row.data('id');
        const header = tableGoods.find('.row-header[data-id=' + rowId + ']');
        const detail = header.next();

        // find out if has detail
        header.find('button').hide();
        header.find('.sticky-col-right').append(`<button class="btn btn-sm btn-danger btn-remove-goods-row" type="button"><i class="ion-trash-b"></i></button>`);
        if (detail.hasClass('row-detail')) {
            activeTable.find('tbody').first().append(header);
            detail.find('button').hide();
            activeTable.find('tbody').first().append(detail);
        } else {
            activeTable.find('tbody').first().append(header);
        }
        
        // add ex-container
        let exContainer = activeRow.find('#container-label').text().split('-');
        if (exContainer.length) {
            exContainer = exContainer[0].trim();
            activeTable.find('.row-header[data-id=' + rowId + ']').find('#ex-no-container-label').text(exContainer);
            activeTable.find('.row-header[data-id=' + rowId + ']').find('#ex_no_container').val(exContainer);
        }

        row.remove();
        reorderRow();
    });

    /**
     * reorder the row, find out it a nested row of header row.
     */
    function reorderRow() {
        tableContainer.find('> tbody > tr.row-header').each(function (index) {
            // recount header number
            $(this).children('td').first().html((index + 1).toString());

            // reorder index of inputs
            $(this).find('input[name]').each(function () {
                const pattern = new RegExp("containers[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'containers[' + index + ']');
                $(this).attr('name', attributeName);
            });

            // check if it has detail goods
            $(this).next('tr.row-detail').find('> td > table > tbody > tr.row-header').each(function (count) {
                $(this).children('td').first().html((count + 1).toString());

                // reorder index of inputs
                $(this).find('input[name]').each(function () {
                    //const patternParent = new RegExp("^containers[([0-9]*\\)?]\\[goods\\][([0-9]*\\)?]", "i");
                    //const attributeNameParent = $(this).attr('name').replace(patternParent, 'containers[' + index + '][goods][' + count + ']');
                    //$(this).attr('name', attributeNameParent);
                });

                // replace prefix input name
                $(this).find('input[name]').each(function () {
                    const patternParent = new RegExp("^goods[([0-9]*\\)?]", "i");
                    const attributeNameParent = $(this).attr('name').replace(patternParent, 'containers[' + index + '][goods][' + count + ']');
                    $(this).attr('name', attributeNameParent);
                });

                // replace prefix goods in goods
                $(this).next('tr.row-detail').find('tr.row-goods').each(function (countInner) {
                    $(this).find('input[name]').each(function () {
                        const patternParent = new RegExp("^goods[([0-9]*\\)?]", "i");
                        const attributeNameParent = $(this).attr('name').replace(patternParent, 'containers[' + index + '][goods][' + count + ']');
                        $(this).attr('name', attributeNameParent);
                    });
                })
            });

        });

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
     * Edit container data to dialog container form.
     */
    tableContainer.on('click', '.btn-edit-container', function (e) {
        e.preventDefault();

        activeRow = $(this).closest('tr');
        activeTable = $(this).closest('table');

        const createWrapper = $('#container-create-wrapper');
        const listField = $('#container-list-field');

        if (activeRow.find('#id_container').val()) {
            createWrapper.hide();
            createWrapper.find('input').val('').prop('required', false);
            createWrapper.find('select').val('').trigger('change').prop('required', false);
            listField.show();
            listField.find('select').prop('required', true);
            modalContainerInput.removeClass('create-container').addClass('select-container');
        } else {
            createWrapper.show();
            createWrapper.find('input').prop('required', true);
            createWrapper.find('select').prop('required', true);
            listField.hide();
            listField.find('select').val('').trigger('change').prop('required', false);
            modalContainerInput.addClass('create-container').removeClass('select-container');
        }

        const dataContainer = {
            id: activeRow.find('#id_container').val(),
            text: activeRow.find('#container-label').text(),
            size: activeRow.find('#size').val(),
            type: activeRow.find('#type').val(),
            shipping_line: activeRow.find('#shipping_line').val()
        };
        const selectContainer = modalContainerInput.find('#no_container');
        if (selectContainer.find("option[value='" + dataContainer.id + "']").length) {
            selectContainer.val(dataContainer.id).trigger('change');
        } else {
            const newOption = new Option(dataContainer.text, dataContainer.id, true, true);
            newOption.size = dataContainer.size;
            newOption.type = dataContainer.type;
            newOption.shipping_line = dataContainer.shipping_line;
            selectContainer.append(newOption).trigger('change');
        }

        const dataPosition = {
            id: activeRow.find('#id_position').val(),
            text: activeRow.find('#position-label').text(),
        };
        const selectPosition = modalContainerInput.find('#position');
        if (selectPosition.find("option[value='" + dataPosition.id + "']").length) {
            selectPosition.val(dataPosition.id).trigger('change', ['script']);
        } else {
            const newOption = new Option(dataPosition.text, dataPosition.id, true, true);
            selectPosition.append(newOption).trigger('change', ['script']);
        }

        const dataShippingLine = {
            id: activeRow.find('#id_shipping_line').val(),
            text: activeRow.find('#shipping_line').val(),
        };
        const selectShippingLine = modalContainerInput.find('#shipping_line');
        if (selectShippingLine.find("option[value='" + dataShippingLine.id + "']").length) {
            selectShippingLine.val(dataShippingLine.id).trigger('change');
        } else {
            const newOption = new Option(dataShippingLine.text, dataShippingLine.id, true, true);
            selectShippingLine.append(newOption).trigger('change');
        }

        modalContainerInput.find('#no_container_label').val(activeRow.find('#no_container').val());
        modalContainerInput.find('#type').val(activeRow.find('#type').val()).trigger('change');
        modalContainerInput.find('#size').val(activeRow.find('#size').val()).trigger('change');
        modalContainerInput.find('#position_blocks').val(activeRow.find('#id_position_blocks').val());
        modalContainerInput.find('#seal').val(activeRow.find('#seal').val());
        modalContainerInput.find('#length').val(setCurrencyValue(Number(activeRow.find('#length_payload').val()), '', ',', '.'));
        modalContainerInput.find('#width').val(setCurrencyValue(Number(activeRow.find('#width_payload').val()), '', ',', '.'));
        modalContainerInput.find('#height').val(setCurrencyValue(Number(activeRow.find('#height_payload').val()), '', ',', '.'));
        modalContainerInput.find('#volume').val(setCurrencyValue(Number(activeRow.find('#volume_payload').val()), '', ',', '.'));
        modalContainerInput.find('#is_hold').val(activeRow.find('#is_hold').val()).trigger('change');
        modalContainerInput.find('#is_empty').val(activeRow.find('#is_empty').val()).trigger('change');
        modalContainerInput.find('#status').val(activeRow.find('#status').val()).trigger('change');
        modalContainerInput.find('#status_danger').val(activeRow.find('#status_danger').val()).trigger('change');
        modalContainerInput.find('#description').val(activeRow.find('#description').val());

        modalContainerInput.removeClass('create').addClass('edit');
        modalContainerInput.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableContainer.on('click', '.btn-view-container', function (e) {
        e.preventDefault();

        activeRow = $(this).closest('tr');

        const containerLabel = activeRow.find('#no_container').val() + ' (' + activeRow.find('#size').val() + ' - ' + activeRow.find('#type').val() + ')';
        modalContainerView.find('#no_container').text(containerLabel);
        modalContainerView.find('#seal').text(activeRow.find('#seal').val());
        modalContainerView.find('#length').text(setCurrencyValue(Number(activeRow.find('#length_payload').val()), '', ',', '.'));
        modalContainerView.find('#width').text(setCurrencyValue(Number(activeRow.find('#width_payload').val()), '', ',', '.'));
        modalContainerView.find('#height').text(setCurrencyValue(Number(activeRow.find('#height_payload').val()), '', ',', '.'));
        modalContainerView.find('#volume').text(setCurrencyValue(Number(activeRow.find('#volume_payload').val()), '', ',', '.'));
        modalContainerView.find('#position').text(activeRow.find('#position').val() || '-');
        modalContainerView.find('#is_hold').text(activeRow.find('#is_hold').val());
        modalContainerView.find('#is_empty').text(activeRow.find('#is_empty').val());
        modalContainerView.find('#status').text(activeRow.find('#status').val());
        modalContainerView.find('#status_danger').text(activeRow.find('#status_danger').val());
        modalContainerView.find('#payload').text(activeRow.find('#payload_volume').val() || '-');
        modalContainerView.find('#description').text(activeRow.find('#description').val() || '-');
        modalContainerView.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    /**
     * Update container from submitted data in modal goods form.
     */
    modalContainerInput.on('submit', function (e) {
        e.preventDefault();

        const shippingLineData = modalContainerInput.find('#shipping_line').select2('data')[0];
        const containerData = modalContainerInput.find('#no_container').select2('data')[0];
        const positionData = modalContainerInput.find('#position').select2('data')[0];

        // input for creating new container
        const shippingLineId = modalContainerInput.find('#shipping_line').val();
        const noContainer = modalContainerInput.find('#no_container_label').val();
        const type = modalContainerInput.find('#type').val();
        const size = modalContainerInput.find('#size').val();

        // grab input from the modal form
        const shippingLine = containerData.element.shipping_line || containerData.shipping_line || shippingLineData.name;
        const containerId = modalContainerInput.find('#no_container').val();
        const seal = modalContainerInput.find('#seal').val();
        const positionId = modalContainerInput.find('#position').val();
        const positionBlocksId = modalContainerInput.find('#position_blocks').val();
        const isHold = modalContainerInput.find('#is_hold').val();
        const isEmpty = modalContainerInput.find('#is_empty').val();
        const status = modalContainerInput.find('#status').val();
        const statusDanger = modalContainerInput.find('#status_danger').val();
        const length = modalContainerInput.find('#length').val() || 0;
        const width = modalContainerInput.find('#width').val() || 0;
        const height = modalContainerInput.find('#height').val() || 0;
        const volume = modalContainerInput.find('#volume').val() || 0;
        const description = modalContainerInput.find('#description').val();

        // table row label
        activeRow.find('#shipping-line-label').html(shippingLine || '<span class="text-danger">CREATE NEW</span>');
        activeRow.find('#container-label').text(containerData.no_container || noContainer);
        if (size) {
            activeRow.find('#container-size-label').text(size);
        }
        if (type) {
            activeRow.find('#container-type-label').text(type);
        }
        activeRow.find('#seal-label').text(seal || '-');
        activeRow.find('#position-label').text(positionData.text || '-');
        activeRow.find('#volume-payload-label').text(`${volume} (${length} x ${width} x ${height})`);

        //input hidden
        activeRow.find('#id_shipping_line').val(shippingLineId);
        activeRow.find('#shipping_line').val(shippingLine);
        activeRow.find('#id_container').val(containerId || '');
        activeRow.find('#no_container').val(containerData.no_container || noContainer);
        activeRow.find('#size').val(size);
        activeRow.find('#type').val(type);
        activeRow.find('#seal').val(seal);
        activeRow.find('#position').val(positionData.text);
        activeRow.find('#id_position').val(positionId);
        activeRow.find('#id_position_blocks').val(positionBlocksId);
        activeRow.find('#length_payload').val(getCurrencyValue(length));
        activeRow.find('#width_payload').val(getCurrencyValue(width));
        activeRow.find('#height_payload').val(getCurrencyValue(height));
        activeRow.find('#volume_payload').val(getCurrencyValue(volume));
        activeRow.find('#is_hold').val(isHold);
        activeRow.find('#is_empty').val(isEmpty);
        activeRow.find('#status').val(status);
        activeRow.find('#status_danger').val(statusDanger);
        activeRow.find('#description').val(description);

        modalContainerInput.modal('hide');
    });

    /**
     * Extended functionality from tally editor.
     */
    tableGoods.on('click', '.btn-edit-goods', function (e) {
        e.preventDefault();

        if ($(this).closest('tr').hasClass('row-stock')) {
            lockGoodsInputs(true);
        } else {
            lockGoodsInputs(false);
        }
    });

    $('.btn-add-goods').on('click', function (e) {
        e.preventDefault();
        lockGoodsInputs(false);
    });

    tableGoods.on('click', '.btn-add-detail', function (e) {
        lockGoodsInputs(false);
    });

    function lockGoodsInputs($isLocked = false) {
        modalGoodsInput.find('#quantity').prop('readonly', $isLocked);
        modalGoodsInput.find('#unit').prop('disabled', $isLocked);
        modalGoodsInput.find('#position').prop('disabled', $isLocked);
        modalGoodsInput.find('#status_danger').prop('disabled', $isLocked);
        modalGoodsInput.find('#is_hold').prop('disabled', $isLocked);
        modalGoodsInput.find('#status').prop('disabled', $isLocked);
        modalGoodsInput.find('#no_pallet').prop('readonly', $isLocked);
        setTimeout(function () {
            modalGoodsInput.find('#ex_no_container').prop('readonly', $isLocked);
        }, 200);
        modalGoodsInput.find('#description').prop('readonly', $isLocked);
    }

    // Add package total weight
    if (createPackage) {
        modalTakeStockGoods.find('#btn-take-goods-stock').on('click', function () {
            calculatePackageWeight();
        });

        tableGoods.on('click', '.btn-remove-goods', function (e) {
            calculatePackageWeight();
        });

        function calculatePackageWeight() {
            tableGoods.find('tbody tr.row-header').each((index, row) => {
                let totalWeight = 0;
                if ($(row).next('tr.row-detail')) {
                    $(row).next('tr.row-detail').find('tbody tr').each((innerIndex, innerRow) => {
                        totalWeight += Number($(innerRow).find('#unit_weight').val() * $(innerRow).find('#quantity').val());
                    });
                }
                $(row).find('#weight').val(totalWeight);
                $(row).find('#unit-weight-label').text(setNumeric(totalWeight));
                $(row).find('#total-weight-label').text(setNumeric(totalWeight));
            });
        }

        modalStockGoods.find('#btn-reload-stock').click();
    }

    /**
     * Calculate volume payload when input value in length, width and height fields is typed.
     */
    modalContainerInput.find('#length, #width, #height').on('input', function () {
        calculateVolume(modalContainerInput);
    });

    /**
     * Calculate volume payload from length payload, width payload, height payload.
     */
    function calculateVolume(wrapper) {
        // calculate when volume input has state readonly because we don't want replace user's volume
        if (wrapper.find('#volume').prop('readonly')) {
            const length = getCurrencyValue(wrapper.find('#length').val());
            const width = getCurrencyValue(wrapper.find('#width').val());
            const height = getCurrencyValue(wrapper.find('#height').val());

            // for precaution just check if all is number otherwise just put in zero
            if (!isNaN(length) && !isNaN(width) && !isNaN(height)) {
                const volume = roundVal(length * width * height);
                wrapper.find('#volume').val(setCurrencyValue(volume, '', ',', '.'));
            } else {
                wrapper.find('#volume').val(0);
            }
        }
    }

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
     * Check the container and goods is completed.
     * 1. Validate hidden input if new container already set the shipping line, type and size (seal is optional)
     * 2. Also new goods data set the hs number and goods type
     */
    formBookingPreview.on('submit', function () {
        const incompleteContainers = [];

        if (getParameterByName('category') === 'INBOUND') {
            tableContainer.find('> tbody > tr.row-header').not('.row-placeholder').each((index, row) => {
                if (!$(row).find('#id_container').val()) {
                    const shippingLineId = $(row).find('#id_shipping_line').val();
                    const type = $(row).find('#type').val();
                    const size = $(row).find('#size').val();
                    if (!shippingLineId || !type || !size) {
                        incompleteContainers.push($(row).find('#no_container').val());
                    }
                }
            });
        }

        let message = 'Please complete ';
        if (incompleteContainers.length) {
            message += 'the container ' + incompleteContainers + ' (shipping line, type or size)';
        }

        if (incompleteContainers.length) {
            modalInfo.find('#message-info').html(message);
            modalInfo.modal({
                backdrop: 'static',
                keyboard: false
            });
            return false;
        }

        if(tableStockGoods.find('tbody tr').length > 0) {
            modalInfo.find('#message-info').html('XML goods data still available to be taken!');
            modalInfo.modal({
                backdrop: 'static',
                keyboard: false
            });
            return false;
        }

        return true;
    });

});
