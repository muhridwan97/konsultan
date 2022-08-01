$(function () {

    var tableShifting = $('#table-shifting');
    var totalColumns = tableShifting.first().find('th').length;
    var tableDetailShifting = tableShifting.find('tbody');
    var shiftingDetailTemplate = $('#row-shifting-item-template').html();
    var selectContainerGoods = $('#container_goods');

    tableShifting.on('click', '.btn-delete-shifting', function (e) {
        e.preventDefault();

        var idShifting = $(this).data('id');
        var labelShifting = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteShifting = $('#modal-delete-shifting');
        modalDeleteShifting.find('form').attr('action', urlDelete);
        modalDeleteShifting.find('input[name=id]').val(idShifting);
        modalDeleteShifting.find('#shifting-title').text(labelShifting);

        modalDeleteShifting.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableShifting.on('click', '.btn-delete-shifting-detail', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();

        reorderItem();
    });

    const modalSelectPosition = $('#modal-position-block');
    let inputPositionBlocks = null;

    tableShifting.on('click', '.btn-edit-block', function (e) {
        const inputPosition = $(this).closest('.form-group').find('#new_position');
        fetchPositionBlock.apply(inputPosition);
    });

    tableShifting.on('change', '.multi-position', fetchPositionBlock);

    function fetchPositionBlock(e, source) {
        const positionId = $(this).val();

        if (positionId && positionId != '0' && source != 'script') {
            inputPositionBlocks = $(this).closest('.input-group').find('#position_blocks');
            
            modalSelectPosition.modal({
                backdrop: 'static',
                keyboard: false
            });
            modalSelectPosition.find('.modal-body').text('Fetching position blocks...');
            
            fetch(`${baseUrl}position/ajax-get-position-block?id_position=${positionId}`)
                .then(result => result.json())
                .then(blocks => {
                    let blockHtml = '';
                    if (blocks && blocks.length) {
                        const selectedBlocks = inputPositionBlocks.val().split(',') || [];
                        blocks.forEach((block, index) => {
                            const isChecked = selectedBlocks.find(selectedBlock => selectedBlock == block.id);
                            blockHtml += `
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    <div class="checkbox icheck">
                                        <label for="block-${index}">
                                            <input type="checkbox" name="position_blocks[]" ${isChecked ? 'checked' : ''}
                                                id="block-${index}" value="${block.id}">
                                            ${block.position_block}
                                        </label>
                                    </div>
                                </div>
                            `
                        });
                        modalSelectPosition.find('.modal-body').html(`<div class="row">${blockHtml}</div>`);
                        modalSelectPosition.find('input').iCheck({
                            checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-blue',
                            increaseArea: '20%' // optional
                        });
                    } else {
                        modalSelectPosition.find('.modal-body').text('No position blocks available');
                    }
                })
                .catch(console.log);
        }
    }

    modalSelectPosition.on('submit', function (e) {
        e.preventDefault();
        const values = modalSelectPosition.find('[name="position_blocks[]"]:checked').map(function () {
            return $(this).val();
        }).get();

        if(inputPositionBlocks) {
            if(values && values.length) {
                inputPositionBlocks.val(values.join(','));
            }else{
                inputPositionBlocks.val('');
            }
        }
        modalSelectPosition.modal('hide');
    });

    tableShifting.on('click', '.btn-approve-shifting', function (e) {
        e.preventDefault();

        var idShifting = $(this).data('id');
        var labelShifting = $(this).data('label');
        var urlValidate = $(this).attr('href');

        var modalApproveShifting = $('#modal-approve-shifting');
        modalApproveShifting.find('form').attr('action', urlValidate);
        modalApproveShifting.find('input[id]').val(idShifting);
        modalApproveShifting.find('#shifting-title').text(labelShifting);

        modalApproveShifting.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    var dataContainerGoods = {};

    $(document).on('click', '.btn-add-record-shifting', function (e) {
        e.preventDefault();

        if (getTotalItem() === 0) {
            tableDetailShifting.empty();
        }

        /*
        var idContainerGoods = selectContainerGoods.select2('data')[0].id;
        var noContainerGoods = selectContainerGoods.select2('data')[0].text;
        var compoContainerGoods = idContainerGoods + "_" + noContainerGoods;

        if (!(compoContainerGoods in dataContainerGoods)) {
            dataContainerGoods[compoContainerGoods] = selectContainerGoods.select2('data')[0];
        }

        console.log(dataContainerGoods[compoContainerGoods]);

        var shiftingRowTemplate = shiftingDetailTemplate
            .replace(/{{container_goods_name}}/g, selectContainerGoods.find('option:selected').text())
            .replace(/{{last_position}}/g,
                dataContainerGoods[compoContainerGoods].last_position != null ?
                    dataContainerGoods[compoContainerGoods].last_position + "/" + (dataContainerGoods[compoContainerGoods].position_blocks != null ? dataContainerGoods[compoContainerGoods].position_blocks : '-') : "-" )
            .replace(/{{container_goods_type}}/g, dataContainerGoods[compoContainerGoods].container_goods_type)
            .replace(/{{id_booking}}/g, dataContainerGoods[compoContainerGoods].id_booking)
            .replace(/{{id_customer}}/g, dataContainerGoods[compoContainerGoods].id_customer)
            .replace(/{{id_container_goods}}/g, dataContainerGoods[compoContainerGoods].id_container_goods)
            .replace(/{{quantity}}/g, dataContainerGoods[compoContainerGoods].quantity)
            .replace(/{{id_unit}}/g, dataContainerGoods[compoContainerGoods].id_unit)
            .replace(/{{tonnage}}/g, dataContainerGoods[compoContainerGoods].tonnage)
            .replace(/{{tonnage_gross}}/g, dataContainerGoods[compoContainerGoods].tonnage_gross)
            .replace(/{{length}}/g, dataContainerGoods[compoContainerGoods].length)
            .replace(/{{width}}/g, dataContainerGoods[compoContainerGoods].width)
            .replace(/{{height}}/g, dataContainerGoods[compoContainerGoods].height)
            .replace(/{{volume}}/g, dataContainerGoods[compoContainerGoods].volume)

            .replace(/{{seal}}/g, dataContainerGoods[compoContainerGoods].seal)
            .replace(/{{no_pallet}}/g, dataContainerGoods[compoContainerGoods].no_pallet)
            .replace(/{{status}}/g, dataContainerGoods[compoContainerGoods].status)
            .replace(/{{status_danger}}/g, dataContainerGoods[compoContainerGoods].status_danger)
            .replace(/{{is_empty}}/g, dataContainerGoods[compoContainerGoods].is_empty)
            .replace(/{{is_hold}}/g, dataContainerGoods[compoContainerGoods].is_hold)
            .replace(/{{no_delivery_order}}/g, dataContainerGoods[compoContainerGoods].no_delivery_order)
            .replace(/{{ex_no_container}}/g, dataContainerGoods[compoContainerGoods].ex_no_container)
            .replace(/{{description}}/g, dataContainerGoods[compoContainerGoods].description);
         */

        var selectedOption = selectContainerGoods.find('option:selected');

        var shiftingRowTemplate = shiftingDetailTemplate
            .replace(/{{container_goods_name}}/g, selectedOption.text())
            .replace(/{{last_position}}/g, (selectedOption.data('last_position') || '-') + "/" + (selectedOption.data('position_blocks') || '-'))
            .replace(/{{container_goods_type}}/g, selectedOption.data('container_goods_type'))
            .replace(/{{id_booking}}/g, selectedOption.data('id_booking'))
            .replace(/{{id_customer}}/g, selectedOption.data('id_customer'))
            .replace(/{{id_container_goods}}/g, selectedOption.data('id_container_goods'))
            .replace(/{{quantity}}/g, selectedOption.data('quantity'))
            .replace(/{{id_unit}}/g, selectedOption.data('id_unit'))
            .replace(/{{tonnage}}/g, selectedOption.data('tonnage'))
            .replace(/{{tonnage_gross}}/g, selectedOption.data('tonnage_gross'))
            .replace(/{{length}}/g, selectedOption.data('length'))
            .replace(/{{width}}/g, selectedOption.data('width'))
            .replace(/{{height}}/g, selectedOption.data('height'))
            .replace(/{{volume}}/g, selectedOption.data('volume'))

            .replace(/{{seal}}/g, selectedOption.data('seal'))
            .replace(/{{no_pallet}}/g, selectedOption.data('no_pallet') || '')
            .replace(/{{status}}/g, selectedOption.data('status'))
            .replace(/{{status_danger}}/g, selectedOption.data('status_danger'))
            .replace(/{{is_empty}}/g, selectedOption.data('is_empty'))
            .replace(/{{is_hold}}/g, selectedOption.data('is_hold'))
            .replace(/{{no_delivery_order}}/g, selectedOption.data('no_delivery_order'))
            .replace(/{{ex_no_container}}/g, selectedOption.data('ex_no_container'))
            .replace(/{{description}}/g, selectedOption.data('description'));

        tableDetailShifting.append(shiftingRowTemplate);
        reorderItem();
    });

    function reorderItem() {
        tableDetailShifting.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        reinitializeSelect2Library();
    }

    function getTotalItem() {
        return parseInt(tableShifting.find('tr.row-shifting-item').length);
    }
});
