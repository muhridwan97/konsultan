(function(){
    var currentScript = $(document.currentScript).data('editor') || 1;

$(function () {
    const tallyEditor = $('.tally-editor.editor-' + currentScript) || $('.tally-editor');
    const btnAddContainer = tallyEditor.find('.btn-add-container');
    const btnAddGoods = tallyEditor.find('.btn-add-goods');
    const tableContainer = tallyEditor.find('#table-container');
    const tableGoods = tallyEditor.find('#table-goods');
    const modalContainerInput = $('#modal-container-input');
    const modalGoodsInput = $('#modal-goods-input');

    let lastBookingId = null;
    let lastStockData = null;

    let activeRow = null;
    let activeTable = null;
    let addDetailInActiveTable = false;

    const workOrderId = $('#work_order_id');
    const statusPage = $('#status_page');
    const isEditJob = statusPage.val() === 'EDIT_JOB' && workOrderId;

    // fix from select2's search not working in modal
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
    };

    /**
     * DATA SOURCE
     * -------------------------------------------------------------------
     * INPUT : show input form, hide button take from stock
     * STOCK : prefer show modal stock instead the form
     * BOTH : by default show input form, and show take from stock as well
     */
   
    const overtime_date_container = moment(modalContainerInput.find('#overtime_date').val(), 'YYYY-DD-MM HH:mm:ss', true).isValid() == true ? modalContainerInput.find('#overtime_date').val() : moment(modalContainerInput.find('#overtime_date').val(),'DD-MM-YYYY HH:mm:ss').format('YYYY-DD-MM HH:mm:ss');
    modalContainerInput.find('#overtime_date').daterangepicker({
        timePicker: true,
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss'
        },
        singleDatePicker: true,
        showDropdowns: true,
        drops: "up",
        startDate: overtime_date_container,
        endDate: overtime_date_container,

    });

    btnAddContainer.on('click', function (e) {
        e.preventDefault();

        activeTable = tableContainer;
        addDetailInActiveTable = false;

        if ($(this).data('source') === 'INPUT') {
            btnStockContainer.hide();
        } else {
            btnStockContainer.show();
        }

        modalContainerInput.find('#overtimeDate').show();
        modalContainerInput.find('#overtime_date').attr('readonly',true);
        modalContainerInput.find('#overtime_date').val(moment($("#form-tally-check").find("#datetime").val()).format('DD-MM-YYYY HH:mm:ss'));
        modalContainerInput.find('#handlingtype').val($('#handling_type').val());
        modalContainerInput.find('#overtime-status-field').off('mouseenter');
        modalContainerInput.find('#overtime-status-field').off('mouseleave');
        modalContainerInput.find('#overtime_status').off('select2:selecting');

        if ($('#status_page').val() == 'EDIT_JOB') {
            if (modalContainerInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {

                modalContainerInput.find('#overtime1').prop('disabled', false);
                modalContainerInput.find('#overtime2').prop('disabled', false);
                modalContainerInput.find('#normal').prop('disabled', false);
                modalContainerInput.find('#overtime_status').select2();
                modalContainerInput.find('#overtime_status').attr('disabled', false);
                modalContainerInput.find('#overtime_date').prop('disabled', false);
                modalContainerInput.find('#overtime_date').attr('required', true);
                modalContainerInput.find('#overtimeDate').show();

            } else {

                var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                if ($('#first_overtime').val() !== false) {
                    modalContainerInput.find('#overtime_status').attr('disabled', false);
                    if (firstOvertime > timeNow) {
                        modalContainerInput.find('#overtime1').prop('disabled', true);
                        modalContainerInput.find('#overtime2').prop('disabled', true);
                        modalContainerInput.find('#overtime_status').val("NORMAL").trigger("change");
                    } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                        modalContainerInput.find('#normal').attr('disabled', true);
                        modalContainerInput.find('#overtime2').attr('disabled', true);
                        modalContainerInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                    } else if ((secondOvertime < timeNow)) {
                        modalContainerInput.find('#normal').attr('disabled', true);
                        modalContainerInput.find('#overtime1').attr('disabled', true);
                        modalContainerInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                    }
                    modalContainerInput.find('#overtime_status').select2();
                }
            }
        } else {
            var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
            var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
            var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

            if ($('#first_overtime').val() !== false) {
                modalContainerInput.find('#overtime_status').attr('disabled', false);
                if (firstOvertime > timeNow) {
                    modalContainerInput.find('#overtime1').prop('disabled', true);
                    modalContainerInput.find('#overtime2').prop('disabled', true);
                    modalContainerInput.find('#overtime_status').val("NORMAL").trigger("change");
                } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                    modalContainerInput.find('#normal').attr('disabled', true);
                    modalContainerInput.find('#overtime2').attr('disabled', true);
                    modalContainerInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                } else if ((secondOvertime < timeNow)) {
                    modalContainerInput.find('#normal').attr('disabled', true);
                    modalContainerInput.find('#overtime1').attr('disabled', true);
                    modalContainerInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                }
                modalContainerInput.find('#overtime_status').select2();
            }

        }

        if ($(this).data('source') === 'STOCK') {
            showModalStockContainer();
        } else {
            clearContainerInput();
            modalContainerInput.removeClass('edit').addClass('create');
            modalContainerInput.find('.btn-remove-container').hide();
            modalContainerInput.modal({
                backdrop: 'static',
                keyboard: false
            });
        }

    });

    btnAddGoods.on('click', function (e) {
        e.preventDefault();

        activeTable = tableGoods;
        addDetailInActiveTable = false;

        if ($(this).data('source') === 'INPUT') {
            btnStockGoods.hide();
        } else {
            btnStockGoods.show();
        }

        modalGoodsInput.find('#overtimeDate').show();
        modalGoodsInput.find('#overtime_date').attr('readonly',true);
        modalGoodsInput.find('#overtime_date').val(moment($("#form-tally-check").find("#datetime").val()).format('DD-MM-YYYY HH:mm:ss'));
        modalGoodsInput.find('#handlingtype').val($('#handling_type').val());
        modalGoodsInput.find('#multiplierGoods').val($('#multiplier_goods').val());

        if ($('#status_page').val() == 'EDIT_JOB') {
            if (modalGoodsInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {
                modalGoodsInput.find('#overtime1').prop('disabled', false);
                modalGoodsInput.find('#overtime2').prop('disabled', false);
                modalGoodsInput.find('#normal').prop('disabled', false);
                modalGoodsInput.find('#overtime_status').select2();
                modalGoodsInput.find('#overtime_status').attr('disabled', false);
                modalGoodsInput.find('#overtime_date').prop('disabled', false);
                modalGoodsInput.find('#overtimeDate').show();

            } else {
                var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                if ($('#first_overtime').val() !== false) {
                    modalGoodsInput.find('#overtime_status').attr('disabled', false);
                    if (firstOvertime > timeNow) {
                        modalGoodsInput.find('#overtime1').prop('disabled', true);
                        modalGoodsInput.find('#overtime2').prop('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                    } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime2').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                    } else if ((secondOvertime < timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime1').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                    }
                    modalGoodsInput.find('#overtime_status').select2();
                }

            }

        } else {
            var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
            var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
            var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

            if ($('#first_overtime').val() !== false) {
                modalGoodsInput.find('#overtime_status').attr('disabled', false);
                if (firstOvertime > timeNow) {
                    modalGoodsInput.find('#overtime1').prop('disabled', true);
                    modalGoodsInput.find('#overtime2').prop('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                    modalGoodsInput.find('#normal').attr('disabled', true);
                    modalGoodsInput.find('#overtime2').attr('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                } else if ((secondOvertime < timeNow)) {
                    modalGoodsInput.find('#normal').attr('disabled', true);
                    modalGoodsInput.find('#overtime1').attr('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                }
                modalGoodsInput.find('#overtime_status').select2();
            }

        }

        if ($(this).data('source') === 'STOCK') {
            showModalStockGoods();
        } else {
            clearGoodsInput();

            const sourceExNoContainer = $('#source_ex_no_container').val();
            if (sourceExNoContainer) {
                modalGoodsInput.find('#ex_no_container').val(sourceExNoContainer).prop('readonly', true);
            } else {
                modalGoodsInput.find('#ex_no_container').prop('readonly', false);
            }
            modalGoodsInput.removeClass('edit').addClass('create');
            modalGoodsInput.find('.btn-remove-goods').hide();
            modalGoodsInput.modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    });
    
    tableContainer.on('click', '.btn-edit-container', function (e) {
        e.preventDefault();

        btnStockContainer.hide();

        activeRow = $(this).closest('tr');

        const dataContainer = {
            id: activeRow.find('#id_container').val(),
            text: activeRow.find('#container-label').text(),
        };
        const selectContainer = modalContainerInput.find('#no_container');
        if (selectContainer.find("option[value='" + dataContainer.id + "']").length) {
            selectContainer.val(dataContainer.id).trigger('change');
        } else {
            const newOption = new Option(dataContainer.text, dataContainer.id, true, true);
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

        $('input[name="overtime_date"]').daterangepicker({
            timePicker: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            singleDatePicker: true,
            showDropdowns: true,
            drops: "up",
            startDate: activeRow.find('#overtime_date').val(),
            endDate: activeRow.find('#overtime_date').val(),
        });

        modalContainerInput.find('#overtime1').prop('disabled', false);
        modalContainerInput.find('#overtime2').prop('disabled', false);
        modalContainerInput.find('#normal').prop('disabled', false);
        modalContainerInput.find('#overtime_status').select2();

        modalContainerInput.find('#id_booking_reference').val(activeRow.find('#id_booking_reference').val());
        modalContainerInput.find('#position_blocks').val(activeRow.find('#id_position_blocks').val());
        modalContainerInput.find('#seal').val(activeRow.find('#seal').val());
        modalContainerInput.find('#length_payload').val(setCurrencyValue(Number(activeRow.find('#length_payload').val()), '', ',', '.'));
        modalContainerInput.find('#width_payload').val(setCurrencyValue(Number(activeRow.find('#width_payload').val()), '', ',', '.'));
        modalContainerInput.find('#height_payload').val(setCurrencyValue(Number(activeRow.find('#height_payload').val()), '', ',', '.'));
        modalContainerInput.find('#volume_payload').val(setCurrencyValue(Number(activeRow.find('#volume_payload').val()), '', ',', '.'));
        modalContainerInput.find('#is_hold').val(activeRow.find('#is_hold').val()).trigger('change');
        modalContainerInput.find('#is_empty').val(activeRow.find('#is_empty').val()).trigger('change');
        modalContainerInput.find('#status').val(activeRow.find('#status').val()).trigger('change');
        modalContainerInput.find('#status_danger').val(activeRow.find('#status_danger').val()).trigger('change');
        modalContainerInput.find('#description').val(activeRow.find('#description').val());
        modalContainerInput.find('#handlingtype').val($('#handling_type').val());

        if ($('#status_page').val() == 'EDIT_JOB') {
            if (modalContainerInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {

                modalContainerInput.find('#overtime1').prop('disabled', false);
                modalContainerInput.find('#overtime2').prop('disabled', false);
                modalContainerInput.find('#normal').prop('disabled', false);
                modalContainerInput.find('#overtime_status').select2();
                modalContainerInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalContainerInput.find('#overtime_status').attr('disabled', false);
                modalContainerInput.find('#overtime_date').val(activeRow.find('#overtime_date').val());
                modalContainerInput.find('#overtime_date').prop('disabled', false);
                modalContainerInput.find('#overtime_date').attr('required', true);
                modalContainerInput.find('#overtimeDate').show();

            } else {
                modalContainerInput.find('#overtimeDate').hide();
                if (activeRow.find('#overtime_status_exists').val() !== "0" && activeRow.find('#overtime_status_exists').val() !== 'not_exists') {
                    modalContainerInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                    modalContainerInput.find('#overtime_status').attr('disabled', true);
                } else {
                    var date = new Date();
                    modalContainerInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                    modalContainerInput.find('#overtime_status').attr('disabled', false);

                    var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                    var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                    var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                    if ($('#first_overtime').val() !== false) {
                        modalContainerInput.find('#overtime_status').attr('disabled', false);
                        if (firstOvertime > timeNow) {
                            modalContainerInput.find('#overtime1').prop('disabled', true);
                            modalContainerInput.find('#overtime2').prop('disabled', true);
                            modalContainerInput.find('#overtime_status').val("NORMAL").trigger("change");
                        } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                            modalContainerInput.find('#normal').attr('disabled', true);
                            modalContainerInput.find('#overtime2').attr('disabled', true);
                            modalContainerInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                        } else if ((secondOvertime < timeNow)) {
                            modalContainerInput.find('#normal').attr('disabled', true);
                            modalContainerInput.find('#overtime1').attr('disabled', true);
                            modalContainerInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                        }
                        modalContainerInput.find('#overtime_status').select2();
                    }
                }
            }

        } else {
            modalContainerInput.find('#overtimeDate').hide();
            if (activeRow.find('#overtime_status_exists').val() !== "0" && activeRow.find('#overtime_status_exists').val() !== 'not_exists') {
                modalContainerInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalContainerInput.find('#overtime_status').attr('disabled', true);
            } else {
                var date = new Date();
                modalContainerInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalContainerInput.find('#overtime_status').attr('disabled', false);

                var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                if ($('#first_overtime').val() !== false) {
                    modalContainerInput.find('#overtime_status').attr('disabled', false);
                    if (firstOvertime > timeNow) {
                        modalContainerInput.find('#overtime1').prop('disabled', true);
                        modalContainerInput.find('#overtime2').prop('disabled', true);
                        modalContainerInput.find('#overtime_status').val("NORMAL").trigger("change");
                    } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                        modalContainerInput.find('#normal').attr('disabled', true);
                        modalContainerInput.find('#overtime2').attr('disabled', true);
                        modalContainerInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                    } else if ((secondOvertime < timeNow)) {
                        modalContainerInput.find('#normal').attr('disabled', true);
                        modalContainerInput.find('#overtime1').attr('disabled', true);
                        modalContainerInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                    }
                    modalContainerInput.find('#overtime_status').select2();
                }
            }
        }

        modalContainerInput.removeClass('create').addClass('edit');
        modalContainerInput.find('.btn-remove-container').show();
        modalContainerInput.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableContainer.on('click', '.btn-add-detail', function (e) {
        e.preventDefault();

        activeTable = tableContainer;
        activeRow = $(this).closest('tr');
        addDetailInActiveTable = true;

        // add from detail row button
        if (activeRow.hasClass('row-detail')) {
            activeRow = activeRow.prev();
        }

        modalGoodsInput.find('#overtimeDate').show();
        modalGoodsInput.find('#overtime_date').attr('readonly',true);
        modalGoodsInput.find('#overtime_date').val(moment($("#form-tally-check").find("#datetime").val()).format('DD-MM-YYYY HH:mm:ss'));
        modalGoodsInput.find('#handlingtype').val($('#handling_type').val());
        modalGoodsInput.find('#multiplierGoods').val($('#multiplier_goods').val());

        if ($('#status_page').val() == 'EDIT_JOB') {
            if (modalGoodsInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {
                modalGoodsInput.find('#overtime1').prop('disabled', false);
                modalGoodsInput.find('#overtime2').prop('disabled', false);
                modalGoodsInput.find('#normal').prop('disabled', false);
                modalGoodsInput.find('#overtime_status').select2();
                modalGoodsInput.find('#overtime_status').attr('disabled', false);
                modalGoodsInput.find('#overtime_date').prop('disabled', false);
                modalGoodsInput.find('#overtimeDate').show();
            } else {
                var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                if ($('#first_overtime').val() !== false) {
                    modalGoodsInput.find('#overtime_status').attr('disabled', false);
                    if (firstOvertime > timeNow) {
                        modalGoodsInput.find('#overtime1').prop('disabled', true);
                        modalGoodsInput.find('#overtime2').prop('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                    } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime2').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                    } else if ((secondOvertime < timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime1').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                    }
                    modalGoodsInput.find('#overtime_status').select2();
                }

            }

        } else {
            var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
            var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
            var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

            if ($('#first_overtime').val() !== false) {
                modalGoodsInput.find('#overtime_status').attr('disabled', false);
                if (firstOvertime > timeNow) {
                    modalGoodsInput.find('#overtime1').prop('disabled', true);
                    modalGoodsInput.find('#overtime2').prop('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                    modalGoodsInput.find('#normal').attr('disabled', true);
                    modalGoodsInput.find('#overtime2').attr('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                } else if ((secondOvertime < timeNow)) {
                    modalGoodsInput.find('#normal').attr('disabled', true);
                    modalGoodsInput.find('#overtime1').attr('disabled', true);
                    modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                }
                modalGoodsInput.find('#overtime_status').select2();
            }

        }

        modalGoodsInput.find('#ex_no_container').prop('readonly', false);

        clearGoodsInput();

        // set pre fill ex container
        let exContainer = activeRow.find('#container-label a').text().split('-');
        if (exContainer.length) {
            exContainer = exContainer[0].trim();
            modalGoodsInput.find('#ex_no_container').val(exContainer).prop('readonly', true);
        }

        if (btnAddGoods.data('source') === 'INPUT') {
            btnStockGoods.hide();
        } else {
            btnStockGoods.show();
        }

        modalGoodsInput.removeClass('edit').addClass('create');
        modalGoodsInput.find('.btn-remove-goods').hide();
        modalGoodsInput.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('.table-editor-wrapper').on('click', '.btn-edit-goods', function (e) {
        e.preventDefault();

        clearGoodsInput();

        btnStockGoods.hide();

        modalGoodsInput.find('#ex_no_container').prop('readonly', false);
        modalGoodsInput.find('#handlingtype').val($('#handling_type').val());
        modalGoodsInput.find('#multiplierGoods').val($('#multiplier_goods').val());
        modalGoodsInput.find('.btn-manual-edit').tooltip('hide').attr('data-original-title', 'Edit manual the value');
        modalGoodsInput.find('.btn-manual-edit').html(`<i class="ion-compose"></i>`);
        
        activeRow = $(this).closest('tr');
        activeTable = $(this).closest('table');

        let goods = activeRow.find('#goods-data').val() || '{}';
        goods = JSON.parse(decodeURIComponent(goods));

        const dataGoods = {
            id: activeRow.find('#id_goods').val(),
            text: activeRow.find('#goods-label').text(),
        };
        const selectGoods = modalGoodsInput.find('#goods');
        if (selectGoods.find("option[value='" + dataGoods.id + "']").length) {
            selectGoods.val(dataGoods.id).trigger('change');
        } else {
            const newOption = new Option(dataGoods.text, dataGoods.id, true, true);
            selectGoods.append(newOption).trigger('change');
        }
        modalGoodsInput.find('#goods').data('data', goods);

        const dataPosition = {
            id: activeRow.find('#id_position').val(),
            text: activeRow.find('#position-label').text(),
        };
        const selectPosition = modalGoodsInput.find('#position');
        if (selectPosition.find("option[value='" + dataPosition.id + "']").length) {
            selectPosition.val(dataPosition.id).trigger('change', ['script']);
        } else {
            const newOption = new Option(dataPosition.text, dataPosition.id, true, true);
            selectPosition.append(newOption).trigger('change', ['script']);
        }

        $('input[name="overtime_date"]').daterangepicker({
            timePicker: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            singleDatePicker: true,
            showDropdowns: true,
            drops: "up",
            startDate: activeRow.find('#overtime_date').val(),
            endDate: activeRow.find('#overtime_date').val(),
        });

        modalGoodsInput.find('#overtime1').prop('disabled', false);
        modalGoodsInput.find('#overtime2').prop('disabled', false);
        modalGoodsInput.find('#normal').prop('disabled', false);
        modalGoodsInput.find('#overtime_status').select2();

        modalGoodsInput.find('#id_booking_reference').val(activeRow.find('#id_booking_reference').val());
        modalGoodsInput.find('#position_blocks').val(activeRow.find('#id_position_blocks').val());
        modalGoodsInput.find('#quantity').val(setCurrencyValue(Number(activeRow.find('#quantity').val()), '', ',', '.')).data('default', goods.quantity);
        modalGoodsInput.find('#unit').val(activeRow.find('#id_unit').val()).trigger('change');
        modalGoodsInput.find('#weight').val(setCurrencyValue(Number(activeRow.find('#unit_weight').val()), '', ',', '.')).data('default', goods.unit_weight);
        modalGoodsInput.find('#gross_weight').val(setCurrencyValue(Number(activeRow.find('#unit_gross_weight').val()), '', ',', '.')).data('default', goods.unit_gross_weight);
        if ($('#handling_type').val() == 'STRIPPING' && activeRow.find('#quantity').val() == '') {
            modalGoodsInput.find('#length').val('0').data('default', goods.unit_length);
            modalGoodsInput.find('#width').val('0').data('default', goods.unit_width);
            modalGoodsInput.find('#height').val('0').data('default', goods.unit_height);
            modalGoodsInput.find('#volume').val('0').data('default', goods.unit_volume);
        }else{
            modalGoodsInput.find('#length').val(setCurrencyValue(Number(activeRow.find('#unit_length').val()), '', ',', '.')).data('default', goods.unit_length);
            modalGoodsInput.find('#width').val(setCurrencyValue(Number(activeRow.find('#unit_width').val()), '', ',', '.')).data('default', goods.unit_width);
            modalGoodsInput.find('#height').val(setCurrencyValue(Number(activeRow.find('#unit_height').val()), '', ',', '.')).data('default', goods.unit_height);
            modalGoodsInput.find('#volume').val(setNumeric(Number(activeRow.find('#unit_volume').val()))).data('default', goods.unit_volume);
        }
        
        modalGoodsInput.find('#is_hold').val(activeRow.find('#is_hold').val()).trigger('change');
        modalGoodsInput.find('#status').val(activeRow.find('#status').val()).trigger('change');
        modalGoodsInput.find('#status_danger').val(activeRow.find('#status_danger').val()).trigger('change');
        modalGoodsInput.find('#ex_no_container').val(activeRow.find('#ex_no_container').val());
        modalGoodsInput.find('#no_pallet').val(activeRow.find('#no_pallet').val());
        modalGoodsInput.find('#whey_number').val(activeRow.find('#whey_number').val());
        modalGoodsInput.find('#description').val(activeRow.find('#description').val());

        if ($('#status_page').val() == 'EDIT_JOB') {

            if (modalContainerInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {
                modalGoodsInput.find('#overtime1').prop('disabled', false);
                modalGoodsInput.find('#overtime2').prop('disabled', false);
                modalGoodsInput.find('#normal').prop('disabled', false);
                modalGoodsInput.find('#overtime_status').select2();
                modalGoodsInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalGoodsInput.find('#overtime_status').attr('disabled', false);
                modalGoodsInput.find('#overtime_date').val(activeRow.find('#overtime_date').val());
                modalGoodsInput.find('#overtime_date').prop('disabled', false);
                modalGoodsInput.find('#overtimeDate').show();

            } else {
                modalGoodsInput.find('#overtimeDate').hide();
                if (activeRow.find('#overtime_status_exists').val() !== "0" && activeRow.find('#overtime_status_exists').val() !== 'not_exists') {
                    modalGoodsInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                    modalGoodsInput.find('#overtime_status').attr('disabled', true);

                } else {
                    modalGoodsInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                    modalGoodsInput.find('#overtime_status').attr('disabled', false);

                    var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                    var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                    var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                    if ($('#first_overtime').val() !== false) {
                        modalGoodsInput.find('#overtime_status').attr('disabled', false);
                        if (firstOvertime > timeNow) {
                            modalGoodsInput.find('#overtime1').prop('disabled', true);
                            modalGoodsInput.find('#overtime2').prop('disabled', true);
                            modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                        } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                            modalGoodsInput.find('#normal').attr('disabled', true);
                            modalGoodsInput.find('#overtime2').attr('disabled', true);
                            modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                        } else if ((secondOvertime < timeNow)) {
                            modalGoodsInput.find('#normal').attr('disabled', true);
                            modalGoodsInput.find('#overtime1').attr('disabled', true);
                            modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                        }
                        modalGoodsInput.find('#overtime_status').select2();
                    }
                }

            }

        } else {
            modalGoodsInput.find('#overtimeDate').hide();
            if (activeRow.find('#overtime_status_exists').val() !== "0" && activeRow.find('#overtime_status_exists').val() !== 'not_exists') {
                modalGoodsInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalGoodsInput.find('#overtime_status').attr('disabled', true);

            } else {
                modalGoodsInput.find('#overtime_status').val(activeRow.find('#overtime_status').val()).trigger('change');
                modalGoodsInput.find('#overtime_status').attr('disabled', false);

                var timeNow = moment($('#timer').val(), "HH:mm:ss").format("HH") == "00" ? moment($('#timer').val(), "24:mm:ss").valueOf() : moment($('#timer').val(), "HH:mm:ss").valueOf(); //milliseconds
                var firstOvertime = moment($('#first_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds
                var secondOvertime = moment($('#second_overtime').val(), "HH:mm:ss").valueOf(); //milliseconds

                if ($('#first_overtime').val() !== false) {
                    modalGoodsInput.find('#overtime_status').attr('disabled', false);
                    if (firstOvertime > timeNow) {
                        modalGoodsInput.find('#overtime1').prop('disabled', true);
                        modalGoodsInput.find('#overtime2').prop('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("NORMAL").trigger("change");
                    } else if ((firstOvertime < timeNow) && (secondOvertime > timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime2').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 1").trigger("change");
                    } else if ((secondOvertime < timeNow)) {
                        modalGoodsInput.find('#normal').attr('disabled', true);
                        modalGoodsInput.find('#overtime1').attr('disabled', true);
                        modalGoodsInput.find('#overtime_status').val("OVERTIME 2").trigger("change");
                    }
                    modalGoodsInput.find('#overtime_status').select2();
                }
            }
        }

        modalGoodsInput.find('#ex_no_container').prop('readonly', true);
        if (activeTable.attr('id') === 'table-goods') {
            modalGoodsInput.find('#ex_no_container').prop('readonly', false);
        }

        modalGoodsInput.removeClass('create').addClass('edit');
        modalGoodsInput.find('.btn-remove-goods').show();
        modalGoodsInput.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableGoods.on('click', '.btn-add-detail', function (e) {
        e.preventDefault();

        activeTable = tableGoods;
        activeRow = $(this).closest('tr');
        addDetailInActiveTable = true;

        // add from detail row button
        if (activeRow.hasClass('row-detail')) {
            activeRow = activeRow.prev();
        }

        modalGoodsInput.find('#ex_no_container').prop('readonly', false);

        clearGoodsInput();


        if (btnAddGoods.data('source') === 'INPUT') {
            btnStockGoods.hide();
        } else {
            btnStockGoods.show();
        }

        modalGoodsInput.removeClass('edit').addClass('create');
        modalGoodsInput.find('.btn-remove-goods').hide();
        modalGoodsInput.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    /**
     * Update container information when selecting no container.
     */
    modalContainerInput.find('#no_container').on('change', function () {
        let containerData = modalContainerInput.find('#no_container').select2('data')[0];
        if (!containerData || !containerData.no_container) {
            containerData = modalContainerInput.find('#no_container').data('data');
        }

        if (containerData) {
            // MAKE SURE ANOTHER CONTAINER IF EXIST WITH SAME ID IN TABLE GET THE LATEST ATTRIBUTES
            // FOR IN CASE, USER UPDATE MASTER CONTAINER DATA
            $('.table-editor-wrapper').find('tr').each((index, row) => {
                const input = $(row).find('#id_container');
                if ($(input).val() == containerData.id) {
                    $(row).find('#container-label a').text(containerData.no_container + ' - ' + containerData.size);
                }
            });
        }
    });

    modalContainerInput.on('submit', function (e) {
        e.preventDefault();

        let containerData = modalContainerInput.find('#no_container').select2('data')[0];
        const positionData = modalContainerInput.find('#position').select2('data')[0];
        if (!containerData || !containerData.no_container) {
            let dataInput = modalContainerInput.find('#no_container').data('data');
            if (dataInput) {
                containerData = dataInput;
            }
        }
        const overtimeData = getCurrentOvertimeStatus(modalContainerInput.find('#overtime_date').val() || null);

        const bookingReferenceId = modalContainerInput.find('#id_booking_reference').val();
        const containerId = modalContainerInput.find('#no_container').val();
        const seal = modalContainerInput.find('#seal').val();
        const positionId = modalContainerInput.find('#position').val();
        const positionBlocksId = modalContainerInput.find('#position_blocks').val();
        const isHold = modalContainerInput.find('#is_hold').val();
        const isEmpty = modalContainerInput.find('#is_empty').val();
        const status = modalContainerInput.find('#status').val();
        const statusDanger = modalContainerInput.find('#status_danger').val();
        const description = modalContainerInput.find('#description').val();
        const length_payload = modalContainerInput.find('#length_payload').val();
        const width_payload = modalContainerInput.find('#width_payload').val();
        const height_payload = modalContainerInput.find('#height_payload').val();
        const volume_payload = modalContainerInput.find('#volume_payload').val();
        const overtime_status = modalContainerInput.find('#overtime_status').val() || overtimeData.status;
        const overtime_date = modalContainerInput.find('#overtime_date').val() || overtimeData.date;
        const handling_type = modalContainerInput.find('#handlingtype').val();

        if (volume_payload == false && (handling_type !== false && handling_type === "STRIPPING")) {
            alert('Save failed, Payload cannot be null or empty !');
            return false;
        }

        if (handling_type !== false && (handling_type === "STRIPPING" || handling_type === "UNLOAD" || handling_type === "LOAD") && positionId == 0) {
            alert("Please, select position type !");
            return false;
        }

        if (modalContainerInput.hasClass('create')) { // form create container

            if ($('#status_page').val() == 'EDIT_JOB') { // from history tally page
                if (modalContainerInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') { //validate edit role
                    const container = {
                        id_container: containerData.id,
                        no_container: containerData.no_container,
                        container_size: containerData.size,
                        seal: seal,
                        id_position: positionId,
                        id_position_blocks: positionBlocksId,
                        position: positionData.position,
                        length_payload: length_payload != undefined ? getCurrencyValue(length_payload) : 0,
                        width_payload: width_payload != undefined ? getCurrencyValue(width_payload) : 0,
                        height_payload: height_payload != undefined ? getCurrencyValue(height_payload) : 0,
                        volume_payload: volume_payload != undefined ? getCurrencyValue(volume_payload) : 0,
                        is_empty: isEmpty,
                        is_hold: isHold,
                        status: status,
                        status_danger: statusDanger,
                        description: description,
                        overtime_status: overtime_status,
                        overtime_date: overtime_date,
                    };
                    addContainer(container, false);
                } else { // not validate edit role
                    const container = {
                        id_container: containerData.id,
                        no_container: containerData.no_container,
                        container_size: containerData.size,
                        seal: seal,
                        id_position: positionId,
                        id_position_blocks: positionBlocksId,
                        position: positionData.position,
                        length_payload: length_payload != undefined ? getCurrencyValue(length_payload) : 0,
                        width_payload: width_payload != undefined ? getCurrencyValue(width_payload) : 0,
                        height_payload: height_payload != undefined ? getCurrencyValue(height_payload) : 0,
                        volume_payload: volume_payload != undefined ? getCurrencyValue(volume_payload) : 0,
                        is_empty: isEmpty,
                        is_hold: isHold,
                        status: status,
                        status_danger: statusDanger,
                        description: description,
                        overtime_status: overtime_status,
                        overtime_date: overtime_date,
                    };
                    addContainer(container, false);
                }

            } else { // from queue/check tally page

                const container = {
                    id_container: containerData.id,
                    no_container: containerData.no_container,
                    container_size: containerData.size,
                    seal: seal,
                    id_position: positionId,
                    id_position_blocks: positionBlocksId,
                    position: positionData.position,
                    length_payload: length_payload != undefined ? getCurrencyValue(length_payload) : 0,
                    width_payload: width_payload != undefined ? getCurrencyValue(width_payload) : 0,
                    height_payload: height_payload != undefined ? getCurrencyValue(height_payload) : 0,
                    volume_payload: volume_payload != undefined ? getCurrencyValue(volume_payload) : 0,
                    is_empty: isEmpty,
                    is_hold: isHold,
                    status: status,
                    status_danger: statusDanger,
                    description: description,
                    overtime_status: overtime_status,
                    overtime_date: overtime_date,
                };
                addContainer(container, false);
            }

        } else { // form edit container (from history tally page or check page)

            lengthPayload = length_payload != undefined ? length_payload : 0;
            widthPayload = width_payload != undefined ? width_payload : 0;
            heightPayload = height_payload != undefined ? height_payload : 0;
            volumePayload = volume_payload != undefined ? volume_payload : 0;

            //for table container information
            activeRow.find('#container-label a').attr('href', `${baseUrl}container/view/${containerData.id}`).text(containerData.text || (containerData.no_container + ' - ' + containerData.size));
            activeRow.find('#position-label').text(positionData.text);
            activeRow.find('#seal-label').text(seal || '-');
            activeRow.find('#is-empty-label').text(isEmpty === '1' ? 'EMPTY' : 'FULL');
            activeRow.find('#is-hold-label').text(isHold === '1' ? 'YES' : 'NO');
            activeRow.find('#status-label').text(status);
            activeRow.find('#status-danger-label').text(statusDanger);
            activeRow.find('#volume-payload-label').text(`${volumePayload} (${lengthPayload}x${widthPayload}x${heightPayload})`);
            activeRow.find('#status-overtime-label').text(overtime_status === '0' ? '-' : overtime_status);
            activeRow.find('#date-overtime-label').text(overtime_status === '0' ? '-' : (moment(overtime_date,'DD-MM-YYYY HH:mm:ss', true).isValid() == true ? overtime_date : moment(overtime_date,'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY HH:mm:ss')));

            //input hidden container
            activeRow.find('#id_booking_reference').val(bookingReferenceId);
            activeRow.find('#id_container').val(containerId);
            activeRow.find('#seal').val(seal);
            activeRow.find('#id_position').val(positionId);
            activeRow.find('#id_position_blocks').val(positionBlocksId);
            activeRow.find('#length_payload').val(getCurrencyValue(lengthPayload));
            activeRow.find('#width_payload').val(getCurrencyValue(widthPayload));
            activeRow.find('#height_payload').val(getCurrencyValue(heightPayload));
            activeRow.find('#volume_payload').val(getCurrencyValue(volumePayload));
            activeRow.find('#is_hold').val(isHold);
            activeRow.find('#is_empty').val(isEmpty);
            activeRow.find('#status').val(status);
            activeRow.find('#status_danger').val(statusDanger);
            activeRow.find('#description').val(description);
            activeRow.find('#overtime_status').val(overtime_status);
            activeRow.find('#overtime_date').val(overtime_date);

            // set ex container to goods detail
            let exContainer = containerData.no_container;
            if (exContainer == undefined) {
                exContainer = activeRow.find('#no_container_text').val();
            }
            activeRow.next('tr.row-detail').find('.row-goods').each(function () {
                $(this).find('#ex-no-container-label').text(exContainer);
                $(this).find('#ex_no_container').val(exContainer)
            });
        }

        if ($('#status_page').val() == undefined) {
            $('.status-overtime-label').hide();
            $('.date-overtime-label').hide();
        } else {
            $('.status-overtime-label').show();
            $('.date-overtime-label').show();
        }
        modalContainerInput.modal('hide');
    });

    function addContainer(container, fromStock = false, allowEdit = false) {
        // remove placeholder if exist
        if (tableContainer.find('.row-placeholder').length) {
            tableContainer.find('.row-placeholder').remove();
        }

        // create container row
        const lastRow = tableContainer.find('tbody tr').length;
        const withDetail = tableContainer.data('with-detail');

        let controlDetail = '';
        if (withDetail === 1) {
            controlDetail = `
                <button class="btn btn-sm btn-primary btn-add-detail" type="button">
                    <i class="ion-plus"></i>
                </button>
            `;
        }
        let control = `
            ${controlDetail}
            <button class="btn btn-sm btn-primary btn-edit-container" type="button">
                <i class="ion-compose"></i>
            </button>
        `;

        if (fromStock && !allowEdit) {
            control = `
                ${controlDetail}
                <button class="btn btn-sm btn-danger btn-remove-container" type="button">
                    <i class="ion-trash-b"></i>
                </button>
            `
        }

        const row = `
                <tr class="row-header${(fromStock && !allowEdit) ? ' row-stock' : ''}">
                    <td>${lastRow + 1}</td>
                    <td id="container-label">
                        <a href="${baseUrl}container/view/${container.id_container}" target="_blank">
                            ${container.no_container} - ${container.container_size || container.size}
                        </a>
                    </td>
                    <td id="seal-label">${container.seal || '-'}</td>
                    <td id="position-label">${container.position || '-'}</td>
                    <td id="volume-payload-label">${setCurrencyValue(Number(container.volume_payload || 0), '', ',', '.')} (${setCurrencyValue(Number(container.length_payload || 0), '', ',', '.')} x ${setCurrencyValue(Number(container.width_payload || 0), '', ',', '.')} x ${setCurrencyValue(Number(container.height_payload || 0), '', ',', '.')})</td>
                    <td id="is-empty-label">${container.is_empty === '1' ? 'EMPTY' : 'FULL'}</td>
                    <td id="is-hold-label">${container.is_hold === '1' ? 'YES' : 'NO'}</td>
                    <td id="status-label">${container.status}</td>
                    <td id="status-danger-label">${container.status_danger}</td>
                    <td id="status-overtime-label" class="status-overtime-label">${container.overtime_status === '0' ? '-' : container.overtime_status}</td>
                    <td id="date-overtime-label" class="date-overtime-label" style="width:125px;">${container.overtime_status === '0' ? '-' : container.overtime_date}</td>
                    <td class="sticky-col-right">
                        <input type="hidden" name="containers[][id_reference]" id="id_reference" value="${container.id || ''}">
                        <input type="hidden" name="containers[][id_booking_reference]" id="id_booking_reference" value="${container.id_booking_reference || ''}">
                        <input type="hidden" name="containers[][id_container]" id="id_container" value="${container.id_container}">
                        <input type="hidden" name="containers[][seal]" id="seal" value="${container.seal}">
                        <input type="hidden" name="containers[][id_position]" id="id_position" value="${container.id_position}">
                        <input type="hidden" name="containers[][id_position_blocks]" id="id_position_blocks" value="${container.id_position_blocks || ''}">
                        <input type="hidden" name="containers[][is_hold]" id="is_hold" value="${container.is_hold}">
                        <input type="hidden" name="containers[][is_empty]" id="is_empty" value="${container.is_empty}">
                        <input type="hidden" name="containers[][status]" id="status" value="${container.status}">
                        <input type="hidden" name="containers[][status_danger]" id="status_danger" value="${container.status_danger}">
                        <input type="hidden" name="containers[][description]" id="description" value="${container.description}">
                        <input type="hidden" name="containers[][length_payload]" id="length_payload" value="${container.length_payload}">
                        <input type="hidden" name="containers[][width_payload]" id="width_payload" value="${container.width_payload}">
                        <input type="hidden" name="containers[][height_payload]" id="height_payload" value="${container.height_payload}">
                        <input type="hidden" name="containers[][volume_payload]" id="volume_payload" value="${container.volume_payload}">
                        <input type="hidden" name="containers[][overtime_status]" id="overtime_status" value="${container.overtime_status}">
                        <input type="hidden" name="containers[][overtime_status_exists]" id="overtime_status_exists" value="not_exists">
                        <input type="hidden" name="containers[][workOrderContainerId]" id="workOrderContainerId" value="">
                        <input type="hidden" name="containers[][overtime_date]" id="overtime_date" value="${container.overtime_status == '0' ? '' : container.overtime_date}">
                        ${control}
                    </td>
                </tr>
            `;
        tableContainer.find('tbody').first().append(row);
        reorderRow();
        let handlingType = modalContainerInput.find('#handlingtype').val();
        if (handlingType==3) {//if stripping
            activeTable = tableContainer;
            activeRow = tableContainer.find('tbody tr:last');
            addDetailInActiveTable = true;

            // add from detail row button
            if (activeRow.hasClass('row-detail')) {
                activeRow = activeRow.prev();
            }
            let stockUrl = `${baseUrl}work-order/ajax_get_goods_in_container?id_booking=${container.id_booking}&id_container=${container.id_container}`;
        
            fetch(stockUrl)
                .then(result => result.json())
                .then(stock => {
                    stock.forEach((stock) => {
                    // save last stock data so we do not need to fetch again
                        let goods = {
                            id_goods: stock.id_goods,
                            no_goods: stock.no_goods,
                            goods_name: stock.goods_name,
                            quantity: parseInt(stock.quantity),
                            unit: stock.unit,
                            whey_number: stock.whey_number,
                            id_unit: stock.id_unit,
                            weight: stock.weight,
                            gross_weight: stock.unit_gross_weight,
                            length: stock.unit_length,
                            width: stock.unit_width,
                            height: stock.unit_height,
                            volume: stock.unit_volume,
                            position: stock.position,
                            id_position: stock.id_position,
                            id_position_blocks: "",
                            is_hold: stock.is_hold,
                            status: stock.status,
                            status_danger: stock.status_danger,
                            no_pallet: stock.no_pallet,
                            ex_no_container: stock.ex_no_container,
                            description: stock.description,
                        };
                        addGoods(goods, false);
                    });
                })
                .catch(console.log);
        }
        
    }

    function addGoods(goods, fromStock = false, allowEdit = false) {
        let rowLabel = 'row-header';
        let inputName = 'goods[]';

        let targetTable = activeTable;
        let ex_no_container = activeTable.find('#no_container_text').val();
        // check if user insert nested goods in container table
        // this block statement is an adjustment to insert
        if (addDetailInActiveTable) {
            rowLabel = 'row-goods';
            inputName = 'containers[][goods][]';
            if(tableGoods === activeTable) {
                rowLabel = 'row-goods';
                inputName = 'goods[][goods][]';
            }

            // create header table in nested current table for goods
            if (!activeRow.next().hasClass('row-detail')) {
                let overtimeColumn = '';
                if($('#status_page').length) {
                    overtimeColumn = `
                        <th class="status-overtime-title">Overtime Status</th>
                        <th class="date-overtime-title">Overtime Date</th>
                    `;
                }
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
                                    <th>Whey Number</th>
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
                                    ${overtimeColumn}
                                    <th class="sticky-col-right">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <button class="btn btn-sm btn-primary btn-block btn-add-detail visible-xs" type="button">
                                ADD ITEM GOODS
                            </button>
                        </td>
                    </tr>
                `;
                $(tableNested).insertAfter(activeRow);
            }

            // set active table to nested table, so code bellow works as expected
            targetTable = activeRow.next().find('table');
        }

        // remove placeholder if exist
        if (targetTable.find('.row-placeholder').length) {
            targetTable.find('.row-placeholder').remove();
        }

        const lastRow = targetTable.find('tbody tr').length;
        const withDetail = tableGoods.data('with-detail');

        // create goods row
        let controlDetail = '';
        if (!addDetailInActiveTable && withDetail === 1) {
            controlDetail = `
                <button class="btn btn-sm btn-primary btn-add-detail" type="button">
                    <i class="ion-plus"></i>
                </button>
            `;
        }
        let control = `
            ${controlDetail}
            <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                <i class="fa ion-compose"></i>
            </button>
            <button class="btn btn-sm btn-info btn-photo-goods" type="button">
                <i class="fa ion-image"></i>
            </button>
        `;

        if (fromStock && !allowEdit) {
            control = `
                <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                    <i class="ion-trash-b"></i>
                </button>
            `
        }

        //generate no pallet
        let pallet1 = goods.no_pallet;
        var newPallet;
        // console.log(goods.ex_no_container);
        if(!goods.ex_no_container && goods.ex_no_container!== ''){
            // console.log('masuk');
            var newPallet = pallet1.replace(/-/g, ex_no_container);
        }
        // create new record and hidden input of table
        const row = `
            <tr class="${rowLabel}${(fromStock && !allowEdit) ? ' row-stock' : ''}" data-id="${uniqueId()}">
                <td>${lastRow + 1}</td>
                <td id="goods-label">
                    <a href="${baseUrl}goods/view/${goods.id_goods}" target="_blank">
                        ${goods.goods_name}
                    </a>
                </td>
                <td id="quantity-label">${setCurrencyValue(Number(goods.quantity || 0), '', ',', '.')}</td>
                <td id="unit-label">${goods.unit || '-'}</td>
                <td id="whey-number-label">${goods.whey_number || '-'}</td>
                <td id="ex-no-container-label">${goods.ex_no_container ? goods.ex_no_container : ex_no_container || '-'}</td>
                <td id="unit-weight-label">${setCurrencyValue(Number(goods.weight || 0), '', ',', '.')}</td>
                <td id="total-weight-label">${setCurrencyValue(Number(goods.weight || 0) * Number(goods.quantity || 0), '', ',', '.')}</td>
                <td id="unit-gross-weight-label">${setCurrencyValue(Number(goods.gross_weight || 0), '', ',', '.')}</td>
                <td id="total-gross-weight-label">${setCurrencyValue(Number(goods.gross_weight || 0) * Number(goods.quantity || 0), '', ',', '.')}</td>
                <td id="unit-volume-label">${setNumeric(goods.volume || 0)} (${setCurrencyValue(Number(goods.length || 0), '', ',', '.')} x ${setCurrencyValue(Number(goods.width || 0), '', ',', '.')} x ${setCurrencyValue(Number(goods.height || 0), '', ',', '.')})</td>
                <td id="total-volume-label">${setNumeric(Number(goods.volume || 0) * Number(goods.quantity || 0), '', ',', '.')}</td>
                <td id="position-label">${goods.position || '-'}</td>
                <td id="no-pallet-label">${newPallet ? newPallet : goods.no_pallet || '-'}</td>
                <td id="is-hold-label">${goods.is_hold === '1' ? 'YES' : 'NO'}</td>
                <td id="status-label">${goods.status}</td>
                <td id="status-danger-label">${goods.status_danger}</td>
                <td id="status-overtime-label" class="status-label">${goods.overtime_status === '0' ? '-' : goods.overtime_status}</td>
                <td id="date-overtime-label" class="date-label">${goods.overtime_status === '0' ? '-' : goods.overtime_date}</td>
                <td class="sticky-col-right">
                    <input type="hidden" id="goods-data" value="${encodeURIComponent(JSON.stringify(goods))}">
                    <input type="hidden" name="${inputName}[id_booking_reference]" id="id_booking_reference" value="${goods.id_booking_reference || ''}">
                    <input type="hidden" name="${inputName}[id_goods]" id="id_goods" value="${goods.id_goods}">
                    <input type="hidden" name="${inputName}[quantity]" id="quantity" value="${goods.quantity}">
                    <input type="hidden" name="${inputName}[id_unit]" id="id_unit" value="${goods.id_unit}">
                    <input type="hidden" name="${inputName}[whey_number]" id="whey_number" value="${goods.whey_number}">
                    <input type="hidden" name="${inputName}[unit_weight]" id="unit_weight" value="${goods.weight}">
                    <input type="hidden" name="${inputName}[unit_gross_weight]" id="unit_gross_weight" value="${goods.gross_weight}">
                    <input type="hidden" name="${inputName}[unit_length]" id="unit_length" value="${goods.length}">
                    <input type="hidden" name="${inputName}[unit_width]" id="unit_width" value="${goods.width}">
                    <input type="hidden" name="${inputName}[unit_height]" id="unit_height" value="${goods.height}">
                    <input type="hidden" name="${inputName}[unit_volume]" id="unit_volume" value="${goods.volume}">
                    <input type="hidden" name="${inputName}[no_pallet]" id="no_pallet" value="${goods.no_pallet}">
                    <input type="hidden" name="${inputName}[id_position]" id="id_position" value="${goods.id_position}">
                    <input type="hidden" name="${inputName}[id_position_blocks]" id="id_position_blocks" value="${goods.id_position_blocks || ''}">
                    <input type="hidden" name="${inputName}[is_hold]" id="is_hold" value="${goods.is_hold}">
                    <input type="hidden" name="${inputName}[status]" id="status" value="${goods.status}">
                    <input type="hidden" name="${inputName}[status_danger]" id="status_danger" value="${goods.status_danger}">
                    <input type="hidden" name="${inputName}[ex_no_container]" id="ex_no_container" value="${goods.ex_no_container || ''}">
                    <input type="hidden" name="${inputName}[description]" id="description" value="${goods.description}">
                    <input type="hidden" name="${inputName}[id_reference]" id="id_reference" value="${goods.id || goods.no_pallet || goods.id_work_order_goods || goods.id_goods || ''}">
                    <input type="hidden" name="${inputName}[overtime_status]" id="overtime_status" value="${goods.overtime_status}">
                    <input type="hidden" name="${inputName}[overtime_date]" id="overtime_date" value="${goods.overtime_status === '0' ? '' : goods.overtime_date}">
                    <input type="hidden" name="${inputName}[overtime_status_exists]" id="overtime_status_exists" value="not_exists">
                    <input type="hidden" name="${inputName}[workOrderGoodsId]" id="workOrderGoodsId" value="">
                    <input type="hidden" name='${inputName}[temp_photos]' id="temp_photos" value="">
                    <input type="hidden" name='${inputName}[temp_photo_descriptions]' id="temp_photo_descriptions" value="">
                    ${control}
                </td>
            </tr>
        `;
        targetTable.find('tbody').first().append(row);
        reorderRow();

        checkToDisableTakeGoodsStock();
    }

    /**
     * Update goods information when selecting item goods.
     */
    modalGoodsInput.find('#goods').on('change', function () {
        let goodsData = modalGoodsInput.find('#goods').select2('data')[0];
        if (!goodsData || !goodsData.name) {
            goodsData = modalGoodsInput.find('#goods').data('data');
        }

        if (goodsData) {
            // FORCE TALLYMAN TO INPUT IN SOME HANDLING TYPE
            if ($('#handling_type').val() == 'STRIPPING' || $('#handling_type').val() == 'UNLOAD') {
                modalGoodsInput.find('#weight').val(setNumeric(goodsData.unit_weight));
                modalGoodsInput.find('#gross_weight').val(setNumeric(goodsData.unit_gross_weight));
                modalGoodsInput.find('#length').val('');
                modalGoodsInput.find('#width').val('');
                modalGoodsInput.find('#height').val('');
                modalGoodsInput.find('#volume').val('');
                modalGoodsInput.find('#source-weight').html('Master Weight <b class="text-danger">' + setNumeric(goodsData.unit_weight) + '</b>');
                modalGoodsInput.find('#source-gross-weight').html('Master Gross Weight <b class="text-danger">' + setNumeric(goodsData.unit_gross_weight) + '</b>');
                modalGoodsInput.find('#source-length').html('Master Length <b class="text-danger">' + setNumeric(goodsData.unit_length) + '</b>');
                modalGoodsInput.find('#source-width').html('Master Width <b class="text-danger">' + setNumeric(goodsData.unit_width) + '</b>');
                modalGoodsInput.find('#source-height').html('Master Height <b class="text-danger">' + setNumeric(goodsData.unit_height) + '</b>');
                modalGoodsInput.find('#source-volume').html('Master Volume <b class="text-danger">' + setNumeric(goodsData.unit_volume) + '</b>');
                if (goodsData.id_unit) {
                    modalGoodsInput.find('#unit').val(goodsData.id_unit).trigger('change');
                }
                if (goodsData.status) {
                    modalGoodsInput.find('#status').val(goodsData.status).trigger('change');
                }
                if (goodsData.status_danger) {
                    modalGoodsInput.find('#status_danger').val(goodsData.status_danger).trigger('change');
                }
                if (goodsData.is_hold) {
                    modalGoodsInput.find('#is_hold').val(goodsData.is_hold).trigger('change');
                }

                // hide source dimension if filtered goods only from booking
                if ($('#handling_type').val() == 'STRIPPING') {
                    if (modalGoodsInput.find('#filter_goods_booking').is(":checked")) {
                        modalGoodsInput.find('#source-length').hide();
                        modalGoodsInput.find('#source-width').hide();
                        modalGoodsInput.find('#source-height').hide();
                    }
                }
            } else {
                modalGoodsInput.find('#weight').val(setNumeric(goodsData.unit_weight));
                modalGoodsInput.find('#gross_weight').val(setNumeric(goodsData.unit_gross_weight));
                modalGoodsInput.find('#length').val(setNumeric(goodsData.unit_length));
                modalGoodsInput.find('#width').val(setNumeric(goodsData.unit_width));
                modalGoodsInput.find('#height').val(setNumeric(goodsData.unit_height));
                modalGoodsInput.find('#volume').val(setNumeric(goodsData.unit_volume));
                modalGoodsInput.find('#whey_number').val(goodsData.whey_number);
            }

            // MAKE SURE ANOTHER GOODS WITH SAME ID IN TABLE GET THE LATEST UNIT WEIGHT AND VOLUME ATTRIBUTES
            // FOR IN CASE, USER UPDATE MASTER GOODS DATA
            $('.table-editor-wrapper').find('tr td.sticky-col-right').each((index, row) => {
                const input = $(row).find('#id_goods');
                if ($(input).val() == goodsData.id) {
                    const quantityRow = $(row).find('#quantity').val();

                    $(row).find('#unit_weight').val(goodsData.unit_weight);
                    $(row).find('#unit_gross_weight').val(goodsData.unit_gross_weight);
                    $(row).find('#unit_length').val(goodsData.unit_length);
                    $(row).find('#unit_width').val(goodsData.unit_width);
                    $(row).find('#unit_height').val(goodsData.unit_height);
                    $(row).find('#unit_volume').val(goodsData.unit_volume);

                    $(row).find('#unit-weight-label').text(setNumeric(goodsData.unit_weight));
                    $(row).find('#total-weight-label').text(setNumeric(Number(goodsData.unit_weight || 0) * quantityRow));
                    $(row).find('#unit-gross-weight-label').text(setNumeric(goodsData.unit_gross_weight));
                    $(row).find('#total-gross-weight-label').text(setNumeric(Number(goodsData.unit_gross_weight || 0) * quantityRow));
                    $(row).find('#unit-volume-label').text(`${setNumeric(goodsData.unit_volume)} (${setNumeric(goodsData.unit_length)}x${setNumeric(goodsData.unit_width)}x${setNumeric(goodsData.unit_height)})`);
                    $(row).find('#total-volume-label').text(setNumeric(Number(goodsData.unit_volume || 0) * quantityRow));
                }
            });
        }
    });

    modalGoodsInput.find('#filter_goods_booking').on('ifChanged', function () {
        if ($(this).is(":checked")) {
            modalGoodsInput.find('#goods').val('').trigger('change');
            modalGoodsInput.find('#goods').data('url', $(this).data('url-booking'));
            if ($('#handling_type').val() == 'STRIPPING') {
                modalGoodsInput.find('#weight').val('').prop('readonly', true);
                modalGoodsInput.find('#gross_weight').val('').prop('readonly', true);
                modalGoodsInput.find('#source-weight b').text('0');
                modalGoodsInput.find('#source-gross-weight b').text('0');
            }
        } else {
            modalGoodsInput.find('#goods').data('url', $(this).data('url-default'));
            if ($('#handling_type').val() == 'STRIPPING') {
                //modalGoodsInput.find('#weight').prop('readonly', false);
                //modalGoodsInput.find('#gross_weight').prop('readonly', false);
            }
        }
    });

    modalGoodsInput.on('submit', function (e) {
        e.preventDefault();

        let goodsData = modalGoodsInput.find('#goods').select2('data')[0];
        const unitData = modalGoodsInput.find('#unit').select2('data')[0];
        const positionData = modalGoodsInput.find('#position').select2('data')[0];
        if (!goodsData || !goodsData.name) {
            let dataInput = modalGoodsInput.find('#goods').data('data');
            if (dataInput) {
                goodsData = dataInput;
            }
        }
        const overtimeData = getCurrentOvertimeStatus(modalGoodsInput.find('#overtime_date').val() || null);

        const bookingReferenceId = modalGoodsInput.find('#id_booking_reference').val();
        const goodsId = modalGoodsInput.find('#goods').val();
        const quantity = modalGoodsInput.find('#quantity').val();
        const unitId = modalGoodsInput.find('#unit').val();
        const weight = modalGoodsInput.find('#weight').val();
        const grossWeight = modalGoodsInput.find('#gross_weight').val();
        const length = modalGoodsInput.find('#length').val();
        const width = modalGoodsInput.find('#width').val();
        const height = modalGoodsInput.find('#height').val();
        const volume = modalGoodsInput.find('#volume').val();
        const positionId = modalGoodsInput.find('#position').val();
        const positionBlocksId = modalGoodsInput.find('#position_blocks').val();
        const isHold = modalGoodsInput.find('#is_hold').val();
        const status = modalGoodsInput.find('#status').val();
        const statusDanger = modalGoodsInput.find('#status_danger').val();
        const exNoContainer = modalGoodsInput.find('#ex_no_container').val();
        const description = modalGoodsInput.find('#description').val();
        const overtime_status = modalGoodsInput.find('#overtime_status').val() || overtimeData.status;
        const overtime_date = modalGoodsInput.find('#overtime_date').val() || overtimeData.date;
        const handling_type = modalGoodsInput.find('#handlingtype').val();
        const multiplier_goods = modalGoodsInput.find('#multiplierGoods').val();
        const wheyNumber = modalGoodsInput.find('#whey_number').val();
        const no_pallet = modalGoodsInput.find('#no_pallet').val();

        var no_booking = modalGoodsInput.find('#no_booking').val();
        if (no_booking=='') {
            var noPallet1 = goodsId+"/"+(exNoContainer === '' ? '-' : exNoContainer);   
        } else {
            var noPallet1 = goodsId+"/"+(exNoContainer === '' ? '-' : exNoContainer)+"/"+no_booking;                        
        }
        var noPallet = noPallet1;

        var no_booking_char = no_booking.substring(0,2);
        if(no_pallet != '' && no_booking_char == 'BO'){
            noPallet = no_pallet;
        }
        let totalNetto = 0;
        let totalBruto = 0;
        // MAKE SURE ANOTHER GOODS WITH SAME ID IN TABLE GET THE LATEST UNIT WEIGHT AND VOLUME ATTRIBUTES
        // FOR IN CASE, USER UPDATE MASTER GOODS DATA
        $('.table-editor-wrapper').find('tr td.sticky-col-right').each((index, row) => {
            const input = $(row).find('#id_goods');
            if ($(input).val() == goodsData.id) {
                const quantityRow = $(row).find('#quantity').val();

                $(row).find('#unit_weight').val(weight);
                $(row).find('#unit_gross_weight').val(grossWeight);
                $(row).find('#unit_length').val(length);
                $(row).find('#unit_width').val(width);
                $(row).find('#unit_height').val(height);
                $(row).find('#unit_volume').val(volume);

                $(row).find('#unit-weight-label').text(setNumeric(weight));
                $(row).find('#total-weight-label').text(setNumeric(Number(weight || 0) * quantityRow));
                $(row).find('#unit-gross-weight-label').text(setNumeric(grossWeight));
                $(row).find('#total-gross-weight-label').text(setNumeric(Number(grossWeight || 0) * quantityRow));
                $(row).find('#unit-volume-label').text(`${setNumeric(volume)} (${setNumeric(length)}x${setNumeric(width)}x${setNumeric(height)})`);
                $(row).find('#total-volume-label').text(setNumeric(Number(volume || 0) * quantityRow));
                //calculate netto and bruto on booking form
                if ($('#netto').length && $('#bruto').length) {
                    console.log('as '+totalNetto);
                    totalNetto += Number(getCurrencyValue(weight) || 0) * Number(getCurrencyValue(quantity) || 0);
                    totalBruto += Number(getCurrencyValue(grossWeight) || 0) * Number(getCurrencyValue(quantity) || 0);
                }
            }else{
                if(activeRow != null && activeRow.find('#id_goods').val() != goodsId && (activeRow.find('#id_goods').val() == $(input).val())){
                    // continue;
                }else{
                    //calculate netto and bruto on booking form
                    if ($('#netto').length && $('#bruto').length) {
                        // console.log('te '+ $(row).find('#unit_weight').val() * $(row).find('#quantity').val());
                        // console.log(totalNetto +'+'+Number(getCurrencyValue($(row).find('#unit_weight').val() * $(row).find('#quantity').val())));
                        if(!Number.isNaN($(row).find('#unit_weight').val() * $(row).find('#quantity').val())){
                            totalNetto += $(row).find('#unit_weight').val() * $(row).find('#quantity').val();
                        }
                        if(!Number.isNaN($(row).find('#unit_gross_weight').val() * $(row).find('#quantity').val())){
                            totalBruto += $(row).find('#unit_gross_weight').val() * $(row).find('#quantity').val();
                        }
                        // totalNetto += getCurrencyValue($(row).find('#unit_weight').val() * $(row).find('#quantity').val());
                        // totalBruto += getCurrencyValue($(row).find('#unit_gross_weight').val() * $(row).find('#quantity').val());
                    }
                }                
            }
        });
        if(activeRow != null && activeRow.find('#id_goods').val() != goodsId){
            //calculate netto and bruto on booking form
            if ($('#netto').length && $('#bruto').length) {
                totalNetto += Number(getCurrencyValue(weight) || 0) * Number(getCurrencyValue(quantity) || 0);
                totalBruto += Number(getCurrencyValue(grossWeight) || 0) * Number(getCurrencyValue(quantity) || 0);
            }
        }
        //calculate netto and bruto on booking form
        if ($('#netto').length && $('#bruto').length) {
            $('#netto').val(setCurrencyValue(Number(totalNetto || 0), '', ',', '.'));
            $('#bruto').val(setCurrencyValue(Number(totalBruto || 0), '', ',', '.'));
        }

        if (handling_type !== false && (handling_type === "STRIPPING" || handling_type === "UNLOAD" || handling_type === "LOAD") && positionId == 0) {
            alert("Please select position type !");
            return false;
        }

        if (!volume || volume <= 0 && handling_type === "STRIPPING") {
            //alert('Volume is required');
            //return false;
        }
        
        if ((quantity == 0 || quantity == "") && handling_type === "STRIPPING") {
            alert('quantity is required');
            return false;
        }

        if ((length == 0 || length == "" || width == 0 || width == "" || height == 0 || height == "") && (multiplier_goods == 1)) {
            alert('length, width and height dont input 0');
            return false;
        }

        let goods = {
            id_booking_reference: bookingReferenceId,
            id_goods: goodsId,
            no_goods: goodsData.no_goods,
            goods_name: goodsData.name || goodsData.goods_name,
            quantity: getCurrencyValue(quantity),
            unit: unitData.text,
            whey_number: wheyNumber,
            id_unit: unitId,
            weight: getCurrencyValue(weight),
            gross_weight: getCurrencyValue(grossWeight),
            length: getCurrencyValue(length),
            width: getCurrencyValue(width),
            height: getCurrencyValue(height),
            volume: getCurrencyValue(volume, true),
            position: positionData.position || positionData.text,
            id_position: positionId,
            id_position_blocks: positionBlocksId,
            is_hold: isHold,
            status: status,
            status_danger: statusDanger,
            no_pallet: noPallet,
            ex_no_container: exNoContainer,
            description: description,
            overtime_status: overtime_status,
        };
        if (modalGoodsInput.hasClass('create')) {
            if ($('#status_page').val() == 'EDIT_JOB') {
                if (modalContainerInput.find('#permission').val() == 'PERMISSION_WORKORDER_VALIDATED_EDIT') {
                    goods.overtime_date = overtime_date;
                }
            }
            addGoods(goods, false);
        } else {
            // update table row label
            activeRow.find('#goods-label a').attr('href', `${baseUrl}goods/view/${goods.id_goods}`).text(goods.goods_name);
            activeRow.find('#quantity-label').text(quantity);
            activeRow.find('#unit-label').text(unitData.text);
            activeRow.find('#whey-number-label').text(wheyNumber);
            activeRow.find('#unit-weight-label').text(weight);
            activeRow.find('#total-weight-label').text(setCurrencyValue(Number(getCurrencyValue(weight) || 0) * Number(getCurrencyValue(quantity) || 0), '', ',', '.'));
            activeRow.find('#unit-gross-weight-label').text(grossWeight);
            activeRow.find('#total-gross-weight-label').text(setCurrencyValue(Number(getCurrencyValue(grossWeight) || 0) * Number(getCurrencyValue(quantity) || 0), '', ',', '.'));
            activeRow.find('#unit-volume-label').text(`${volume} (${length}x${width}x${height})`);
            activeRow.find('#total-volume-label').text(setNumeric(Number(getCurrencyValue(volume) || 0) * Number(getCurrencyValue(quantity) || 0), '', ',', '.'));
            activeRow.find('#position-label').text(positionData.text);
            activeRow.find('#no-pallet-label').text(noPallet);
            activeRow.find('#is-hold-label').text(isHold === '1' ? 'YES' : 'NO');
            activeRow.find('#status-label').text(status);
            activeRow.find('#status-danger-label').text(statusDanger);
            activeRow.find('#ex-no-container-label').text(exNoContainer);
            activeRow.find('#status-overtime-label').text(overtime_status === "0" ? '-' : overtime_status);
            activeRow.find('#date-overtime-label').text(overtime_status === '0' ? '-' : (moment(overtime_date,'DD-MM-YYYY HH:mm:ss', true).isValid() == true ? overtime_date : moment(overtime_date,'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY HH:mm:ss')));
            
            // update goods hidden input
            activeRow.find('#goods-data').val(encodeURIComponent(JSON.stringify(goods)));
            activeRow.find('#id_booking_reference').val(bookingReferenceId);
            activeRow.find('#id_goods').val(goodsId);
            activeRow.find('#quantity').val(getCurrencyValue(quantity));
            activeRow.find('#id_unit').val(unitId);
            activeRow.find('#whey_number').val(wheyNumber);
            activeRow.find('#unit_weight').val(getCurrencyValue(weight));
            activeRow.find('#unit_gross_weight').val(getCurrencyValue(grossWeight));
            activeRow.find('#unit_length').val(getCurrencyValue(length));
            activeRow.find('#unit_width').val(getCurrencyValue(width));
            activeRow.find('#unit_height').val(getCurrencyValue(height));
            activeRow.find('#unit_volume').val(getCurrencyValue(volume, true));
            activeRow.find('#id_position').val(positionId);
            activeRow.find('#id_position_blocks').val(positionBlocksId);
            activeRow.find('#no_pallet').val(noPallet);
            activeRow.find('#is_hold').val(isHold);
            activeRow.find('#status').val(status);
            activeRow.find('#status_danger').val(statusDanger);
            activeRow.find('#ex_no_container').val(exNoContainer);
            activeRow.find('#description').val(description);
            activeRow.find('#overtime_status').val(overtime_status);
            activeRow.find('#overtime_date').val(overtime_date);

        }

        if ($('#status_page').val() == undefined) {
            $('.status-label').hide();
            $('.date-label').hide();
            $('.status-overtime-title').hide();
            $('.date-overtime-title').hide();
        } else {
            $('.status-label').show();
            $('.date-label').show();
            $('.status-overtime-title').show();
            $('.date-overtime-title').show();
        }
        modalGoodsInput.modal('hide');
    });


    /**
     * remove container from button in modal container editor,
     * remove table row and check if table is empty and reorder the rest if not.
     */
    $('.content-wrapper').on('click', '.btn-remove-container', function (e) {
        e.preventDefault();

        if ($(this).parent('td').length) {
            activeRow = $(this).closest('tr');
        }

        // remove detail first if exist
        if (activeRow.next('tr.row-detail').length) {
            activeRow.next().remove();
        }
        // remove current row
        activeRow.remove();

        // find out if table container is empty the add placeholder into it
        // otherwise try to reorder the index and number
        let row = tableContainer.find('tbody tr');
        if (row.length === 0) {
            const placeholder = `<tr class="row-placeholder"><td colspan="12">No container data</td></tr>`;
            tableContainer.find('tbody').html(placeholder);
        } else {
            reorderRow();
        }

        // close modal container editor
        modalContainerInput.modal('hide');
    });


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
            const placeholder = `<tr class="row-placeholder"><td colspan="17">No goods data</td></tr>`;
            tableGoods.find('tbody').html(placeholder);
        } else {
            reorderRow();
        }

        // close modal goods editor
        modalGoodsInput.modal('hide');
    });

    tallyEditor.find('.btn-clear-all-goods').on('click', function () {
        tableGoods.find('tbody').empty();

        // add placeholder if empty or if not reorder the list
        let row = tableGoods.find('tbody tr');
        if (row.length === 0) {
            const placeholder = `<tr class="row-placeholder"><td colspan="17">No goods data</td></tr>`;
            tableGoods.find('tbody').html(placeholder);
        } else {
            reorderRow();
        }
    });

    /**
     * clear the container editor, reset all fields.
     */
    function clearContainerInput() {

        modalContainerInput.find('#no_container').val('').trigger('change');
        modalContainerInput.find('#position_blocks').val('');
        modalContainerInput.find('#position').val('').trigger('change');
        modalContainerInput.find('#length_payload').val(0);
        modalContainerInput.find('#width_payload').val(0);
        modalContainerInput.find('#height_payload').val(0);
        modalContainerInput.find('#volume_payload').val(0);
        modalContainerInput.find('#seal').val('');
        modalContainerInput.find('#is_hold').val(0).trigger('change');
        modalContainerInput.find('#is_empty').val('').trigger('change');
        modalContainerInput.find('#status').val('GOOD').trigger('change');
        modalContainerInput.find('#status_danger').val('NOT DANGER').trigger('change');
        modalContainerInput.find('#description').val('');
        // modalContainerInput.find('#overtime_status').val(0).trigger('change');
    }


    /**
     * clear the goods editor, reset the fields.
     */
    function clearGoodsInput() {

        modalGoodsInput.find('#goods').val('').trigger('change');
        modalGoodsInput.find('#quantity').val('').data('default', '0');
        modalGoodsInput.find('#weight').val('').data('default', '0');
        modalGoodsInput.find('#gross_weight').val('').data('default', '0');
        modalGoodsInput.find('#length').val('').data('default', '0');
        modalGoodsInput.find('#width').val('').data('default', '0');
        modalGoodsInput.find('#height').val('').data('default', '0');
        modalGoodsInput.find('#volume').val('').data('default', '0');
        modalGoodsInput.find('#unit').val('').trigger('change');
        modalGoodsInput.find('#whey_number').val('');
        modalGoodsInput.find('#position_blocks').val('');
        modalGoodsInput.find('#position').val('').trigger('change');
        modalGoodsInput.find('#is_hold').val(0).trigger('change');
        modalGoodsInput.find('#status').val('GOOD').trigger('change');
        modalGoodsInput.find('#status_danger').val('NOT DANGER').trigger('change');
        modalGoodsInput.find('#no_pallet').val('');
        modalGoodsInput.find('#ex_no_container').val('');
        modalGoodsInput.find('#description').val('');
        // modalGoodsInput.find('#overtime_status').val(0).trigger('change');

        // FORCE TALLYMAN TO INPUT IN SOME HANDLING TYPE
        if ($('#handling_type').val() == 'STRIPPING' || $('#handling_type').val() == 'UNLOAD' || $('#handling_type').val() == 'ADD UNPACKAGE') {
            modalGoodsInput.find('#weight,#gross_weight,#volume').prop('readonly', true);
            modalGoodsInput.find('#length,#width,#height').prop('readonly', false);
        } else {
            modalGoodsInput.find('#weight,#gross_weight,#length,#width,#height,#volume').prop('readonly', true);
        }

        modalGoodsInput.find('#source-weight').text('');
        modalGoodsInput.find('#source-gross-weight').text('');
        modalGoodsInput.find('#source-length').text('');
        modalGoodsInput.find('#source-width').text('');
        modalGoodsInput.find('#source-height').text('');
        modalGoodsInput.find('#source-volume').text('');
    }


    /**
     * reorder the row, find out it a nested row of header row.
     */
    function reorderRow() {
        let totalNetto = 0;
        let totalBruto = 0;
        tableContainer.find('> tbody > tr.row-header').each(function (index) {
            // recount header number
            $(this).children('td').first().html((index + 1).toString());

            // reorder index of inputs
            $(this).find('input[name]').each(function () {
                const pattern = new RegExp("containers[([0-9]*\\)?]", "i");
                const attributeName = $(this).attr('name').replace(pattern, 'containers[' + index + ']');
                $(this).attr('name', attributeName);
            });

            // check if it has detail
            $(this).next('tr.row-detail').find('> td > table > tbody > tr.row-goods').each(function (count) {
                // reorder detail number
                $(this).children('td').first().html((count + 1).toString());

                // reorder index of inputs
                $(this).find('input[name]').each(function () {
                    const patternParent = new RegExp("containers[([0-9]*\\)?]", "i");
                    const attributeNameParent = $(this).attr('name').replace(patternParent, 'containers[' + index + ']');
                    $(this).attr('name', attributeNameParent);

                    const pattern = new RegExp("\\[goods\\][([0-9]*\\)?]", "i");
                    const attributeName = $(this).attr('name').replace(pattern, '[goods][' + count + ']');
                    $(this).attr('name', attributeName);
                });

                //calculate netto and bruto on booking form
                if ($('#netto').length && $('#bruto').length) {
                    console.log('masuk '+totalBruto);
                    totalNetto += $(this).find('input#unit_weight').val() * $(this).find('input#quantity').val();
                    totalBruto += $(this).find('input#unit_gross_weight').val() * $(this).find('input#quantity').val();
                }
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
            
            //calculate netto and bruto on booking form
            if ($('#netto').length && $('#bruto').length) {
                totalNetto += $(this).find('input#unit_weight').val() * $(this).find('input#quantity').val();
                totalBruto += $(this).find('input#unit_gross_weight').val() * $(this).find('input#quantity').val();
            }

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

        //calculate netto and bruto on booking form
        if ($('#netto').length && $('#bruto').length) {
            $('#netto').val(setCurrencyValue(Number(totalNetto || 0), '', ',', '.'));
            $('#bruto').val(setCurrencyValue(Number(totalBruto || 0), '', ',', '.'));
        }

        setTimeout(function () {
            setTableViewport();
        }, 300);
    }


    /**
     * Toggle button between calculate volume based inputs or manual edit.
     */
    modalGoodsInput.find('.btn-calculate-volume').on('click', function () {
        if (modalGoodsInput.find('#volume').prop('readonly')) {
            modalGoodsInput.find('#volume').prop('readonly', false);
            $(this).tooltip('hide').attr('data-original-title', 'Calculate from dimension fields');
            $(this).html(`<i class="fa fa-calculator"></i>`);
        } else {
            modalGoodsInput.find('#volume').prop('readonly', true);
            $(this).tooltip('hide').attr('data-original-title', 'Edit manual volume value');
            $(this).html(`<i class="ion-compose"></i>`);
            calculateVolume(); // put this after setup volume readonly not before, see the function.
        }
    });

    /**
     * Calculate volume payload when input value in length, width and height fields is typed.
     */
    modalContainerInput.find('#length_payload, #width_payload, #height_payload').on('input', calculateVolumePayload);

    /**
     * Calculate when input value in length, width and height fields is typed.
     */
    modalGoodsInput.find('#length, #width, #height').on('input', calculateVolume);

    /**
     * Calculate volume from length, width, height.
     */
    function calculateVolume() {
        // calculate when volume input has state readonly because we don't want replace user's volume
        if (modalGoodsInput.find('#volume').prop('readonly')) {
            const length = getCurrencyValue(modalGoodsInput.find('#length').val());
            const width = getCurrencyValue(modalGoodsInput.find('#width').val());
            const height = getCurrencyValue(modalGoodsInput.find('#height').val());

            // for precaution just check if all is number otherwise just put in zero
            if (!isNaN(length) && !isNaN(width) && !isNaN(height)) {
                const volume = roundVal(length * width * height);
                modalGoodsInput.find('#volume').val(setCurrencyValue(volume, '', ',', '.'));
            } else {
                modalGoodsInput.find('#volume').val(0);
            }
        }
    }

    /**
     * Toggle button between calculate the input that related by quantity or manual edit.
     * if the input switches from manual to calculate mode then call calculateByQuantity() function to reset the value.
     */
    modalGoodsInput.find('.btn-manual-edit').on('click', function () {
        const input = $(this).closest('.input-group').find('input');
        if (input.prop('readonly')) {
            input.prop('readonly', false);
            $(this).tooltip('hide').attr('data-original-title', 'Calculate from quantity fields');
            $(this).html(`<i class="fa fa-calculator"></i>`);
        } else {
            input.prop('readonly', true);
            $(this).tooltip('hide').attr('data-original-title', 'Edit manual the value');
            $(this).html(`<i class="ion-compose"></i>`);

            const qtyDefault = modalGoodsInput.find('#quantity').data('default');
            const qtyTake = getCurrencyValue(modalGoodsInput.find('#quantity').val());
            calculateByQuantity(input, qtyDefault, qtyTake);
        }
    });

    /**
     * update weight, volume, and dimension props by quantity, see calculateByQuantity()
     * update only if the inputs are readonly (because we can edit manually the value)
     */
    modalGoodsInput.find('#quantity').on('input', function () {
        const qtyDefault = modalGoodsInput.find('#quantity').data('default');
        const qtyTake = getCurrencyValue(modalGoodsInput.find('#quantity').val());

        if (modalGoodsInput.find('#weight').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#weight'), qtyDefault, qtyTake);
        }

        if (modalGoodsInput.find('#gross_weight').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#gross_weight'), qtyDefault, qtyTake);
        }

        // disable calculate volume by taken quantity because volume itself now is unit volume
        if (modalGoodsInput.find('#volume').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#volume'), qtyDefault, qtyTake);
        }

        if (modalGoodsInput.find('#length').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#length'), qtyDefault, qtyTake);
        }

        // in auto calculation, we cannot calculate the width and height linear of quantity
        if (modalGoodsInput.find('#width').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#width'), qtyDefault, qtyTake);
        }

        if (modalGoodsInput.find('#height').prop('readonly')) {
            //calculateByQuantity(modalGoodsInput.find('#height'), qtyDefault, qtyTake);
        }
    });

    /**
     * Calculate volume payload from length payload, width payload, height payload.
     */
    function calculateVolumePayload() {
        // calculate when volume input has state readonly because we don't want replace user's volume
        if (modalContainerInput.find('#volume_payload').prop('readonly')) {
            const length_payload = getCurrencyValue(modalContainerInput.find('#length_payload').val());
            const width_payload = getCurrencyValue(modalContainerInput.find('#width_payload').val());
            const height_payload = getCurrencyValue(modalContainerInput.find('#height_payload').val());

            // for precaution just check if all is number otherwise just put in zero
            if (!isNaN(length_payload) && !isNaN(width_payload) && !isNaN(height_payload)) {
                const volume_payload = roundVal(length_payload * width_payload * height_payload);
                modalContainerInput.find('#volume_payload').val(setCurrencyValue(volume_payload, '', ',', '.'));
            } else {
                modalContainerInput.find('#volume_payload').val(0);
            }
        }
    }

    /**
     * Rounded floating point into nearest group precision.
     * @param value
     * @param precision
     * @returns {number}
     */
    function roundVal(value, precision = 9) {
        return (+value).toFixed(precision).replace(/([0-9]+(\.[0-9]+[1-9])?)(\.?0+$)/,'$1');

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
     * Get data from stock container
     */
    const btnStockContainer = $('#btn-stock-container');
    const modalStockContainer = $('#modal-stock-container');
    const btnReloadStockContainer = modalStockContainer.find('#btn-reload-stock');

    /**
     * Show modal that contain stock container list data,
     * save last stock data for reduce server request,
     * give delay because we waiting parent modal to be closed.
     */
    btnStockContainer.on('click', showModalStockContainer.bind(this, 500));

    function showModalStockContainer(delay = 0) {
        const bookingId = modalStockContainer.data('booking-id');

        if (bookingId && bookingId !== lastBookingId) {
            getContainerStock(bookingId);
        } else {
            initContainerStock(lastStockData);
        }

        setTimeout(() => {
            modalStockContainer.data('source-id', tallyEditor.data('id'));
            modalStockContainer.modal({
                backdrop: 'static',
                keyboard: false
            });
        }, delay);
    }

    /**
     * Reload (bust) last stock data (replace old stock variable)
     */
    btnReloadStockContainer.on('click', function () {
        const bookingId = modalStockContainer.data('booking-id');
        if (bookingId) {
            getContainerStock(bookingId);
        }
    });

    /**
     * Fetch stock from server and setup table.
     * @param bookingId
     */
    function getContainerStock(bookingId) {
        let exceptWorkOrderId = '';
        if (statusPage.val() === 'EDIT_JOB' && workOrderId) {
            exceptWorkOrderId = workOrderId.val()
        }
        let stockUrl = `${baseUrl}work-order/ajax_get_stock_by_booking?id_booking=${bookingId}&except_work_order=${exceptWorkOrderId}`;
        if (tallyEditor.data('stock-url') !== '') {
            stockUrl = tallyEditor.data('stock-url');
        }

        modalStockContainer.find('tbody').html(`
            <tr><td colspan="11">Fetching stock containers...</td></tr>
        `);

        fetch(stockUrl)
            .then(result => result.json())
            .then(stock => {
                // save last stock data so we do not need to fetch again
                lastBookingId = bookingId;
                lastStockData = stock;
                initContainerStock(lastStockData);
            })
            .catch(console.log);
    }

    /**
     * Initialize table stock list from source data.
     * @param stock
     */
    function initContainerStock(stock) {
        const booking = stock ? stock.booking : null;
        if (booking) {
            modalStockContainer.find('#label-customer').text(booking.customer_name);
            if (stock.bookings) {
                modalStockContainer.find('#label-reference').html(stock.bookings.map(booking => booking.no_reference).join('<br>'));
            } else {
                modalStockContainer.find('#label-reference').text(booking.no_reference);
            }
        }
        const containers = stock ? stock.containers : [];
        if (containers.length) {
            modalStockContainer.find('tbody').empty();

            // find taken stock
            const takenStock = Array.from(tableContainer.find('.row-stock')).map((row) => {
                return $(row).find('#id_container').val();
            });

            // loop through the stock
            containers.forEach((container, index) => {
                if (!takenStock.includes(container.id_container)) {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td id="container-label">${container.no_container}</td>
                            <td id="type-label">${container.container_type || container.type}</td>
                            <td id="size-label">${container.container_size || container.size}</td>
                            <td id="seal-label">${container.seal || '-'}</td>
                            <td id="position-label">${container.position || '-'}</td>
                            <td id="is-empty-label">${container.is_empty === '1' ? 'EMPTY' : 'FULL'}</td>
                            <td id="is-hold-label">${container.is_hold === '1' ? 'YES' : 'NO'}</td>
                            <td id="status-label">${container.status}</td>
                            <td id="status-danger-label">${container.status_danger}</td>
                            <td>
                                <input type="hidden" name="container-data" id="container-data" value="${encodeURIComponent(JSON.stringify(container))}">
                                <button class="btn btn-sm btn-primary btn-take-container" type="button">
                                    TAKE
                                </button>
                            </td>
                        </tr>
                    `;
                    modalStockContainer.find('tbody').first().append(row);
                }
            });
        } else {
            modalStockContainer.find('tbody').html(`
                <tr><td colspan="11">No data available</td></tr>
            `);
        }
    }

    /**
     * Take container into table list from stock.
     */
    modalStockContainer.on('click', '.btn-take-container', function () {
        if (tallyEditor.data('id') == $(this).closest('#modal-stock-container').data('source-id')) {
            const container = decodeURIComponent($(this).closest('tr').find('#container-data').val());
            let btnTake = $(this).closest('tbody').find('.btn-take-container');
            btnTake.prop("disabled", true);
            $(this).closest('tr').remove();
            const containerData = JSON.parse(container);

            const overtimeData = getCurrentOvertimeStatus();
            containerData.overtime_date = overtimeData.date;
            containerData.overtime_status = overtimeData.status;

            addContainer(containerData, true, isEditJob);
            if ($('#status_page').val() == undefined) {
                $('.status-overtime-label').hide();
                $('.date-overtime-label').hide();
            } else {
                $('.status-overtime-label').show();
                $('.date-overtime-label').show();
            }
            setTimeout(function () {
                btnTake.prop("disabled", false);
            }, 3000);
        }
    });


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
    const inputWeightTake = modalTakeStockGoods.find('#weight');
    const inputGrossWeightTake = modalTakeStockGoods.find('#gross_weight');
    const inputVolumeTake = modalTakeStockGoods.find('#volume');
    const inputLengthTake = modalTakeStockGoods.find('#length');
    const inputWidthTake = modalTakeStockGoods.find('#width');
    const inputHeightTake = modalTakeStockGoods.find('#height');
    let activeRowGoodsTake = null;

    /**
     * Show modal that contain stock goods list data,
     * save last stock data for reduce server request,
     * give delay because we waiting parent modal to be closed.
     */
    btnStockGoods.on('click', showModalStockGoods.bind(this, 500));

    function showModalStockGoods(delay = 0) {
        const bookingId = modalStockGoods.data('booking-id');

        if (bookingId && bookingId !== lastBookingId) {
            getGoodsStock(bookingId);
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
     * Reload stock data if there is differences in last stock from server.
     */
    btnReloadStockGoods.on('click', function () {
        const bookingId = modalStockGoods.data('booking-id');
        if (bookingId) {
            getGoodsStock(bookingId);
        }
    });

    /**
     * Fetch stock data from server, and setup table.
     * @param bookingId
     * 
     */
    function getGoodsStock(bookingId) {
        let exceptWorkOrderId = '';
        if (statusPage.val() === 'EDIT_JOB' && workOrderId) {
            exceptWorkOrderId = workOrderId.val()
        }
        let stockUrl = `${baseUrl}work-order/ajax_get_stock_by_booking?id_booking=${bookingId}&except_work_order=${exceptWorkOrderId}`;
        if (tallyEditor.data('stock-url') !== '') {
            stockUrl = tallyEditor.data('stock-url');
        }

        modalStockGoods.find('tbody').html(`
            <tr><td colspan="18">Fetching stock goods...</td></tr>
        `);

        fetch(stockUrl)
            .then(result => result.json())
            .then(stock => {
                // save last stock data so we do not need to fetch again
                lastBookingId = bookingId;
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
        const booking = stock ? stock.booking : null;
        if (booking) {
            modalStockGoods.find('#label-customer').text(booking.customer_name);
            //modalStockGoods.find('#label-reference').text(booking.no_reference);
            if (stock.bookings) {
                modalStockGoods.find('#label-reference').html(stock.bookings.map(booking => booking.no_reference).join('<br>'));
            } else {
                modalStockGoods.find('#label-reference').text(booking.no_reference);
            }
        }
        const goods = stock ? stock.goods : [];
        if (goods.length) {
            modalStockGoods.find('tbody').empty();

            // find taken stock
            const takenStock = Array.from(tallyEditor.find('.row-stock')).map((row) => {
                return {
                    id_goods: $(row).find('#id_goods').val(),
                    id_unit: $(row).find('#id_unit').val(),
                    quantity: $(row).find('#quantity').val(),
                    unit_weight: $(row).find('#unit_weight').val(),
                    unit_gross_weight: $(row).find('#unit_gross_weight').val(),
                    unit_volume: $(row).find('#unit_volume').val(),
                    unit_length: $(row).find('#unit_length').val(),
                    unit_width: $(row).find('#unit_width').val(),
                    unit_height: $(row).find('#unit_height').val(),
                    id_reference: $(row).find('#id_reference').val(),
                };
            });

            // loop through the stock
            let order = 1;
            goods.forEach((item) => {
                let leftQuantity = item.stock_quantity || item.left_quantity || item.quantity;
                let leftWeight = item.unit_weight || item.left_weight;
                let leftGrossWeight = item.unit_gross_weight || item.left_gross_weight;
                let leftVolume = item.unit_volume || item.left_volume;
                let leftLength = item.unit_length;
                let leftWidth = item.unit_width;
                let leftHeight = item.unit_height;

                takenStock.forEach((taken, index) => {
                    if (taken.id_goods === item.id_goods && taken.id_unit === item.id_unit && taken.id_reference === (item.id || item.no_pallet || item.id_work_order_goods || item.id_goods) && !taken['_taken']) {
                        leftQuantity -= taken.quantity;
                        //leftWeight -= taken.unit_weight;
                        //leftGrossWeight -= taken.unit_gross_weight;
                        //leftVolume -= taken.unit_volume;
                        //leftLength -= taken.unit_length;
                        //leftWidth -= taken.unit_width;
                        //leftHeight -= taken.unit_height;
                        takenStock[index]['_taken'] = true;
                    }
                });

                leftQuantity = roundVal(leftQuantity);
                //leftWeight = roundVal(leftWeight);
                //leftGrossWeight = roundVal(leftGrossWeight);
                //leftVolume = roundVal(leftVolume);
                //leftLength = roundVal(leftLength);
                //leftWidth = roundVal(leftWidth);
                //leftHeight = roundVal(leftHeight);

                // this is important, we clone the object so the stock data is not changed
                let itemData = {...item};
                itemData.stock_quantity = leftQuantity;
                itemData.stock_weight = leftWeight;
                itemData.stock_gross_weight = leftGrossWeight;
                itemData.stock_volume = leftVolume;
                itemData.stock_length = leftLength;
                itemData.stock_width = leftWidth;
                itemData.stock_height = leftHeight;

                if (leftQuantity > 0) {
                    const row = `
                        <tr>
                            <td>${order++}</td>
                            <td id="goods-label">${itemData.goods_name}</td>
                            <td id="no-goods-label">${itemData.no_goods}</td>
                            <td id="quantity-label">${setCurrencyValue(Number(leftQuantity || 0), '', ',', '.')}</td>
                            <td id="unit-label">${itemData.unit}</td>
                            <td id="whey-number-label">${itemData.whey_number || '-'}</td>
                            <td id="ex-no-container-label">${itemData.ex_no_container || '-'}</td>
                            <td id="weight-label">${setCurrencyValue(Number(leftWeight || 0), '', ',', '.')}</td>
                            <td id="gross-weight-label">${setCurrencyValue(Number(leftGrossWeight || 0), '', ',', '.')}</td>
                            <td id="unit-length-label">${setCurrencyValue(Number(leftLength || 0), '', ',', '.')}</td>
                            <td id="unit-width-label">${setCurrencyValue(Number(leftWidth || 0), '', ',', '.')}</td>
                            <td id="unit-height-label">${setCurrencyValue(Number(leftHeight || 0), '', ',', '.')}</td>
                            <td id="unit-volume-label">${setNumeric(Number(leftVolume || 0), '', ',', '.')}</td>
                            <td id="position-label">${itemData.position || '-'}</td>
                            <td id="no-pallet-label">${itemData.no_pallet || '-'}</td>
                            <td id="is-hold-label">${itemData.is_hold === '1' ? 'YES' : 'NO'}</td>
                            <td id="status-label">${itemData.status}</td>
                            <td id="status-danger-label">${itemData.status_danger}</td>
                            <td class="text-center sticky-col-right">
                                <input type="hidden" name="goods-data" id="goods-data" value="${encodeURIComponent(JSON.stringify(itemData))}">
                                <button class="btn btn-sm btn-primary btn-take-goods" type="button">
                                    TAKE
                                </button>
                            </td>
                        </tr>
                    `;
                    modalStockGoods.find('tbody').first().append(row);
                }

            });

            checkToDisableTakeGoodsStock();
        } else {
            modalStockGoods.find('tbody').html(`
                <tr><td colspan="18">No data available</td></tr>
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
        inputWeightTake.val(setCurrencyValue(Number(goods.stock_weight || 0), '', ',', '.')).data('default', goods.stock_weight);
        inputGrossWeightTake.val(setCurrencyValue(Number(goods.stock_gross_weight || 0), '', ',', '.')).data('default', goods.stock_gross_weight);
        inputVolumeTake.val(setNumeric(Number(goods.stock_volume || 0), '', ',', '.')).data('default', goods.stock_volume);
        inputLengthTake.val(setCurrencyValue(Number(goods.stock_length || 0), '', ',', '.')).data('default', goods.stock_length);
        inputWidthTake.val(setCurrencyValue(Number(goods.stock_width || 0), '', ',', '.')).data('default', goods.stock_width);
        inputHeightTake.val(setCurrencyValue(Number(goods.stock_height || 0), '', ',', '.')).data('default', goods.stock_height);

        modalTakeStockGoods.modal({
            backdrop: 'static',
            keyboard: false
        });

        if ($('#status_page').val() == undefined) {
            $('.status-label').hide();
            $('.date-label').hide();
            $('.status-overtime-title').hide();
            $('.date-overtime-title').hide();
        } else {
            $('.status-label').show();
            $('.date-label').show();
            $('.status-overtime-title').show();
            $('.date-overtime-title').show();
        }
    });

    btnTakeAllStockGoods.on('click', function () {
        $('#table-stock-goods').find('tbody tr').each(function (i, row) {

            let goods = $(row).find('#goods-data').val();
            goods = JSON.parse(decodeURIComponent(goods));

            inputQuantityTake.val(setCurrencyValue(Number(goods.stock_quantity || 0), '', ',', '.')).data('default', goods.stock_quantity);
            inputWeightTake.val(setCurrencyValue(Number(goods.stock_weight || 0), '', ',', '.')).data('default', goods.stock_weight);
            inputGrossWeightTake.val(setCurrencyValue(Number(goods.stock_gross_weight || 0), '', ',', '.')).data('default', goods.stock_gross_weight);
            inputVolumeTake.val(setNumeric(Number(goods.stock_volume || 0), '', ',', '.')).data('default', goods.stock_volume);
            inputLengthTake.val(setCurrencyValue(Number(goods.stock_length || 0), '', ',', '.')).data('default', goods.stock_length);
            inputWidthTake.val(setCurrencyValue(Number(goods.stock_width || 0), '', ',', '.')).data('default', goods.stock_width);
            inputHeightTake.val(setCurrencyValue(Number(goods.stock_height || 0), '', ',', '.')).data('default', goods.stock_height);

            activeRowGoodsTake = $(row);
            modalTakeStockGoods.find('#btn-take-goods-stock').click();
        });
    });

    /**
     * Toggle button between calculate the input that related by quantity or manual edit.
     * if the input switches from manual to calculate mode then call calculateByQuantity() function to reset the value.
     */
    modalTakeStockGoods.find('.btn-manual-edit').on('click', function () {
        const input = $(this).closest('.input-group').find('input');
        if (input.prop('readonly')) {
            input.prop('readonly', false);
            $(this).tooltip('hide').attr('data-original-title', 'Calculate from quantity fields');
            $(this).html(`<i class="fa fa-calculator"></i>`);
        } else {
            input.prop('readonly', true);
            $(this).tooltip('hide').attr('data-original-title', 'Edit manual the value');
            $(this).html(`<i class="ion-compose"></i>`);

            const qtyDefault = inputQuantityTake.data('default');
            const qtyTake = getCurrencyValue(inputQuantityTake.val());
            calculateByQuantity(input, qtyDefault, qtyTake);
        }
    });

    /**
     * update weight, volume, and dimension props by quantity, see calculateByQuantity()
     * update only if the inputs are readonly (because we can edit manually the value)
     */
    modalTakeStockGoods.find('#quantity').on('input', function () {
        const qtyDefault = inputQuantityTake.data('default');
        const qtyTake = getCurrencyValue(inputQuantityTake.val());

        if (inputWeightTake.prop('readonly')) {
            //calculateByQuantity(inputWeightTake, qtyDefault, qtyTake);
        }

        if (inputGrossWeightTake.prop('readonly')) {
            //calculateByQuantity(inputGrossWeightTake, qtyDefault, qtyTake);
        }

        if (inputVolumeTake.prop('readonly')) {
            //calculateByQuantity(inputVolumeTake, qtyDefault, qtyTake);
        }

        if (inputLengthTake.prop('readonly')) {
            //calculateByQuantity(inputLengthTake, qtyDefault, qtyTake);
        }

        // in auto calculation, we cannot calculate the width and height linear of quantity
        if (inputWidthTake.prop('readonly')) {
            //calculateByQuantity(inputWidthTake, qtyDefault, qtyTake);
        }

        if (inputHeightTake.prop('readonly')) {
            //calculateByQuantity(inputHeightTake, qtyDefault, qtyTake);
        }
    });

    /**
     * Calculate other fields from quantity, Eg. stock Qty 50, Weight 80
     * find the weight value if quantities are 25 (use simple algebra) ?
     *  50  =  80
     * ----   ----  =  80x25 / 50 = 40
     *  25     x
     *
     * @param input
     * @param quantityDefault
     * @param quantityTake
     */
    function calculateByQuantity(input, quantityDefault, quantityTake) {
        const calculated = input.data('default') * quantityTake / quantityDefault;
        input.val(setCurrencyValue(roundVal(calculated), '', ',', '.'));
    }

    /**
     * Take goods from stock and put them into table goods list.
     * 1. populate inputs value
     * 2. calculate left stock by subtracting inputs value (update stock table, delete if quantity is 0 or less)
     * 3. add inputs that taken into goods table (depends on active table, see addGoods())
     */
    modalTakeStockGoods.find('#btn-take-goods-stock').on('click', function () {
        // get input goods are taken
        const quantity = getCurrencyValue(inputQuantityTake.val());
        const weight = getCurrencyValue(inputWeightTake.val());
        const grossWeight = getCurrencyValue(inputGrossWeightTake.val());
        const volume = getCurrencyValue(inputVolumeTake.val(), true);
        const length = getCurrencyValue(inputLengthTake.val());
        const width = getCurrencyValue(inputWidthTake.val());
        const height = getCurrencyValue(inputHeightTake.val());

        // calculate left value from default stock
        const leftQuantity = roundVal(inputQuantityTake.data('default') - quantity);
        const leftWeight = roundVal(inputWeightTake.data('default'));
        const leftGrossWeight = roundVal(inputGrossWeightTake.data('default'));
        const leftVolume = roundVal(inputVolumeTake.data('default'));
        const leftLength = roundVal(inputLengthTake.data('default'));
        const leftWidth = roundVal(inputWidthTake.data('default'));
        const leftHeight = roundVal(inputHeightTake.data('default'));
        //const leftWeight = roundVal(inputWeightTake.data('default') - weight);
        //const leftGrossWeight = roundVal(inputGrossWeightTake.data('default') - grossWeight);
        //const leftVolume = roundVal(inputVolumeTake.data('default') - volume);
        //const leftLength = roundVal(inputLengthTake.data('default') - length);
        //const leftWidth = roundVal(inputWidthTake.data('default') - width);
        //const leftHeight = roundVal(inputHeightTake.data('default') - height);

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
        goods.weight = weight;
        goods.gross_weight = grossWeight;
        goods.volume = volume;
        goods.length = length;
        goods.width = width;
        goods.height = height;
        goods.ex_no_container = goods.ex_no_container || goods.no_container || '';

        const overtimeData = getCurrentOvertimeStatus();
        goods.overtime_date = overtimeData.date;
        goods.overtime_status = overtimeData.status;

        addGoods(goods, true, isEditJob);

        // set current stock data
        goods.stock_quantity = leftQuantity;
        goods.stock_weight = leftWeight;
        goods.stock_gross_weight = leftGrossWeight;
        goods.stock_volume = leftVolume;
        goods.stock_length = leftLength;
        goods.stock_width = leftWidth;
        goods.stock_height = leftHeight;

        activeRowGoodsTake.find('#goods-data').val(encodeURIComponent(JSON.stringify(goods)));

        // update left quantity data in stock
        if (leftQuantity <= 0) {
            activeRowGoodsTake.remove();
            activeRowGoodsTake = null;
        } else {
            activeRowGoodsTake.find('#quantity-label').text(setCurrencyValue(leftQuantity || 0, '', ',', '.'));
            activeRowGoodsTake.find('#weight-label').text(setCurrencyValue(leftWeight || 0, '', ',', '.'));
            activeRowGoodsTake.find('#gross-weight-label').text(setCurrencyValue(leftGrossWeight || 0, '', ',', '.'));
            activeRowGoodsTake.find('#volume-label').text(setNumeric(leftVolume || 0, '', ',', '.'));
            activeRowGoodsTake.find('#length-label').text(setCurrencyValue(leftLength || 0, '', ',', '.'));
            activeRowGoodsTake.find('#width-label').text(setCurrencyValue(leftWidth || 0, '', ',', '.'));
            activeRowGoodsTake.find('#height-label').text(setCurrencyValue(leftHeight || 0, '', ',', '.'));
        }

        if ($('#status_page').val() == undefined) {
            $('.status-label').hide();
            $('.date-label').hide();
            $('.status-overtime-title').hide();
            $('.date-overtime-title').hide();
        } else {
            $('.status-label').show();
            $('.date-label').show();
            $('.status-overtime-title').show();
            $('.date-overtime-title').show();
        }

        // close take goods modal
        modalTakeStockGoods.modal('hide');
    });


    const modalSelectPosition = $('#modal-select-position');
    let inputPositionBlocks = null;

    $('.btn-edit-block').on('click', function () {
        const inputPosition = $(this).closest('.form-group').find('#position');
        fetchPositionBlock.apply(inputPosition, [null]);
    });
    $('.multi-position').on('change', fetchPositionBlock);

    function fetchPositionBlock(e, source) {
        const positionId = $(this).val();
        if (positionId && positionId != '0' && source != 'script') {
            inputPositionBlocks = $(this).closest('.form-group').find('#position_blocks');
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

        if (inputPositionBlocks) {
            if (values && values.length) {
                inputPositionBlocks.val(values.join(','));
            } else {
                inputPositionBlocks.val('');
            }
        }
        modalSelectPosition.modal('hide');
    });

    function getCurrentOvertimeStatus(inputTime = null) {
        const firstOvertimeValue = $('#first_overtime').val();
        const secondOvertimeValue = $('#second_overtime').val();
        const timerDateValue = inputTime === null ? $('#timer-date').val() : (inputTime ? moment(inputTime) : moment()).format("YYYY-MM-DD");
        const timerValue = inputTime === null ? $('#timer').val() : (inputTime ? moment(inputTime) : moment()).format("HH:mm:ss");

        const timeNow = moment(timerValue, "HH:mm:ss").valueOf();
        const firstOvertime = moment(firstOvertimeValue, "HH:mm:ss").valueOf();
        const secondOvertime = moment(secondOvertimeValue, "HH:mm:ss").valueOf();

        const overtimeData = {
            date: timerDateValue + ' ' + timerValue,
            status: '',
        }
        if (firstOvertimeValue && secondOvertime) {
            modalContainerInput.find('#overtime_status').attr('disabled', false);
            if (firstOvertime > timeNow) {
                overtimeData.status = 'NORMAL';
            } else if ((firstOvertime <= timeNow) && (secondOvertime > timeNow)) {
                overtimeData.status = 'OVERTIME 1';
            } else if ((secondOvertime <= timeNow)) {
                overtimeData.status = 'OVERTIME 2';
            }
        }
        return overtimeData;
    }

    function checkToDisableTakeGoodsStock() {
        modalStockGoods.find('#btn-take-all-stock').prop('disabled', false);
        if (tableGoods.find('tbody tr:not(.row-placeholder)').length) {
            const handlingTypeId = modalGoodsInput.find('#handlingtype').val();
            if (statusPage.val() === 'HANDLING' && handlingTypeId === '27') { // add unpackage
                modalStockGoods.find('.btn-take-goods').prop('disabled', true);
                modalStockGoods.find('#btn-take-all-stock').prop('disabled', true);
            }
        }
    }

});
})();