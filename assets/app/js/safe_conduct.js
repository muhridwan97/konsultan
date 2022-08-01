$(function () {
    const tableSafeConduct = $('#table-safe-conduct.table-ajax');
    const controlTemplate = $('#control-safe-conduct-template').html();
    const tableSF = tableSafeConduct.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search safe conduct"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'safe-conduct/data?type=' + (getAllUrlParams().type == undefined ? 'ALL' : getAllUrlParams().type),
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title no-wrap',
                data: 'no_safe_conduct'
            },
            {data: 'no_booking'},
            {data: 'type'},
            {data: 'expedition_type'},
            {data: 'no_police'},
            {data: 'driver'},
            {data: 'containers_load'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 'type-no-safe-conduct',
            render: function (data, type, full) {
                return `<strong>${full.no_safe_conduct}</strong><br><small class="text-muted">${full.no_safe_conduct_group || ''}</small>`;
            }
        },{
            targets: 4,
            render: function (data, type, full, meta) {
                return ($.trim(data) == '' ? '-' : data + ($.trim(full.tep_code) == '' ? '' : ' (<a href="' + baseUrl + "transporter-entry-permit/view/" + $.trim(full.id_transporter_entry_permit) + '">' + full.tep_code + '</a>)'));
            }
        },{
            targets: 7,
            render: function (data, type, full, meta) {
                return ($.trim(data) == '' ? '-' : '<strong>' + data + '</strong>') + ($.trim(full.goods_load) == '' ? '' : ' (' + full.goods_load + ')');
            }
        }, {
            targets: 8,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: 9,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{id_work_order}}/g, full.id_work_order)
                    .replace(/{{print_total}}/g, full.print_total)
                    .replace(/{{print_max}}/g, full.print_max)
                    .replace(/{{no_safe_conduct}}/g, full.no_safe_conduct);

                var control = $.parseHTML(control);
                if ($.trim(full.security_in_date) != '') {
                    $(control).find('.edit').remove();
                }
                if ( (($.trim(full.security_in_date) == '' && $.trim(full.security_out_date) == '') || (($.trim(full.containers_load) != '' || $.trim(full.goods_load) != '') && $.trim(full.security_in_date) != '' && $.trim(full.security_out_date) == '')) || (($.trim(full.containers_load) != '' || $.trim(full.goods_load) != '') && ($.trim(full.security_in_date) != '' && $.trim(full.security_out_date) != '')) ) {
                    $(control).find('.update-data').remove();
                }
                if ($.trim(full.id_work_order) == null || $.trim(full.id_work_order) == '') {
                    $(control).find('.print_eir').remove();
                }
                if (full.type === 'OUTBOUND') {
                    $(control).find('.edit-tps-data').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }]
    });

    var branch_type = $("#form-safe-conduct").find("#branch_type").val();
    if(branch_type != null && branch_type == "TPP"){
        $("#form-safe-conduct").find('#field-eseal-security').hide();
        $("#form-safe-conduct").find('#eseal-security').prop('required', false);
    }else{
        $("#form-safe-conduct").find('#field-eseal-security').show();
        $("#form-safe-conduct").find('#eseal-security').prop('required', true);
    }

    tableSafeConduct.on('click', '.btn-delete-safe-conduct', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalSafeConduct = $('#modal-delete-safe-conduct');
        modalSafeConduct.find('form').attr('action', urlDelete);
        modalSafeConduct.find('input[name=id]').val(id);
        modalSafeConduct.find('#safe-conduct-title').text(label);

        modalSafeConduct.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableSafeConduct.on('click', '.btn-print-safe-conduct', function (e) {
        e.preventDefault();

        var id = $(this).closest('.row-safe-conduct').data('id');
        var no = $(this).closest('.row-safe-conduct').data('no');
        var printTotal = $(this).closest('.row-safe-conduct').data('print-total');
        var printMax = $(this).closest('.row-safe-conduct').data('print-max');
        var urlPrint = $(this).attr('href');

        var modalPrint = $('#modal-confirm-print-safe-conduct');
        modalPrint.find('#print-title').text(no);
        modalPrint.find('#print-total').text((printTotal + 1) + 'x');
        modalPrint.find('#print-max').text(printMax + 'x');
        modalPrint.find('input[name=id]').val(id);
        modalPrint.find('form').attr('action', urlPrint);

        var buttonSubmitPrint = modalPrint.find('button[type=submit]');
        if (printTotal >= printMax) {
            modalPrint.find('#print-subtitle').hide();
            buttonSubmitPrint
                .text('Reaching Maximum of ' + printMax + 'X Print')
                .prop('disabled', true);
        } else {
            modalPrint.find('#print-subtitle').show();
            buttonSubmitPrint
                .text('Print Now')
                .prop('disabled', false);
        }

        modalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableSafeConduct.on('click', '.btn-update-max-print', function (e) {
        e.preventDefault();

        var idSafeConduct = $(this).closest('.row-safe-conduct').data('id');
        var labelSafeConduct = $(this).closest('.row-safe-conduct').data('no');
        var labelTotalPrint = $(this).closest('.row-safe-conduct').data('print-total');
        var labelTotalPrintMax = $(this).closest('.row-safe-conduct').data('print-max');
        var urlUpdate = $(this).attr('href');

        var modalUpdateTotalPrint = $('#modal-update-max-print');
        modalUpdateTotalPrint.find('form').attr('action', urlUpdate);
        modalUpdateTotalPrint.find('input[id=id]').val(idSafeConduct);
        modalUpdateTotalPrint.find('#safe-conduct-title').text(labelSafeConduct);
        modalUpdateTotalPrint.find('#safe-conduct-print').text(labelTotalPrint + ' x print');
        modalUpdateTotalPrint.find('#print_max')
            .attr('min', labelTotalPrint)
            .val(labelTotalPrintMax);

        modalUpdateTotalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    const formSafeConductFilter = $('#form-safe-conduct-filter');
    formSafeConductFilter.find('#type').on('change', function () {
        formSafeConductFilter.submit();
    });


    const formSafeConduct = $('#form-safe-conduct');
    const externalExpeditionWrapper = formSafeConduct.find('#external-expedition-wrapper');
    const internalExpeditionWrapper = formSafeConduct.find('#internal-expedition-wrapper');
    const existingSafeConductWrapper = formSafeConduct.find('#existing-safe-conduct-wrapper');

    const selectCategory = formSafeConduct.find('#category');
    const selectExpeditionType = formSafeConduct.find('#expedition_type');
    const selectInternalVehicle = internalExpeditionWrapper.find('#vehicle');
    const selectBooking = formSafeConduct.find('#booking');
    const selectTEP = formSafeConduct.find('#tep');
    const selectJob = formSafeConduct.find('#work_order');

    const jobDataWrapper = formSafeConduct.find('#job-data-wrapper');
    const tepDataWrapper = formSafeConduct.find('#tep-data-wrapper');
    const expeditionTypeWrapper = formSafeConduct.find('#expedition-type-wrapper');
    const expeditionTypeTextWrapper = formSafeConduct.find('#expedition-type-text-wrapper');
    const tepWrapper = formSafeConduct.find('#tep-wrapper');
    const tepTextWrapper = formSafeConduct.find('#tep-text-wrapper');

    // when edit safe conduct, return amount of value container or goods
    let exceptSafeConduct = '';
    if (formSafeConduct.hasClass('edit')) {
        exceptSafeConduct = formSafeConduct.find('input[name=id]').val();
    }

    /**
     * Inbound category take data from booking,
     * Outbound take data from job
     */
    selectCategory.on('change', function () {
        const type = $(this).val();

        //selectJob.val("").trigger("change");
        formSafeConduct.find('.cy-wrapper').hide();
        formSafeConduct.find('#cy_date').prop('required', false);
        //selectExpeditionType.val("").trigger('change');
        selectInternalVehicle.prop('disabled',false);
        formSafeConduct.find('#field-tep').hide();
        formSafeConduct.find('#tep').prop('required', false);
        expeditionTypeTextWrapper.hide();
        expeditionTypeWrapper.show();
        tepTextWrapper.hide();
        tepWrapper.show();

        if (type === "INBOUND") {
            formSafeConduct.find('.cy-wrapper').show();
            formSafeConduct.find('#cy_date').prop('required', true);
            formSafeConduct.find('#field-booking').show();
            formSafeConduct.find('.field-work-order').hide();

            // show booking data
            selectBooking.empty().append($('<option>')).prop("disabled", true);
            const params = $.param({
                category: type,
                id_except_safe_conduct: exceptSafeConduct,
                status: 'APPROVED'
            });

            fetch(`${baseUrl}safe-conduct/ajax_get_available_booking?${params}`)
                .then(result => result.json())
                .then(data => {
                    selectBooking.prop("disabled", false);
                    data.forEach(row => {
                        selectBooking.append(
                            $('<option>', {value: row.id})
                                .text(`${row.no_booking} (${row.no_reference}) - ${row.customer_name}`)
                        );
                    });
                })
                .catch(err => {
                    console.log(err);
                    selectBooking.prop("disabled", false);
                });

            formSafeConduct.find('.field-work-order').find('input').prop('disabled', true);
            formSafeConduct.find('.field-work-order').find('select').prop('disabled', true);
            formSafeConduct.find('#field-booking').find('input').prop('disabled', false);
            formSafeConduct.find('#field-booking').find('select').prop('disabled', false);

            if (selectExpeditionType.val() === 'INTERNAL') {
                formSafeConduct.find('#field-eseal').hide();
                formSafeConduct.find('#eseal').prop('required', false);
            }
        } else {
            // show job data
            expeditionTypeTextWrapper.show();
            expeditionTypeWrapper.hide();
            tepTextWrapper.show();
            tepWrapper.hide();
            selectInternalVehicle.prop('disabled',false);
            formSafeConduct.find('#field-booking').hide();
            formSafeConduct.find('.field-work-order').show();
            formSafeConduct.find('#field-eseal').hide();
            formSafeConduct.find('#eseal').prop('required', false);

            formSafeConduct.find('#field-booking').find('input').prop('disabled', true);
            formSafeConduct.find('#field-booking').find('select').prop('disabled', true);
            formSafeConduct.find('.field-work-order').find('input').prop('disabled', false);
            formSafeConduct.find('.field-work-order').find('select').prop('disabled', false);
            formSafeConduct.find('.field-work-order').find('select').prop('required', true);

            if (selectExpeditionType.val() === 'EXTERNAL') {
                formSafeConduct.find('#field-tep').show();
                formSafeConduct.find('#tep').prop('required', true);
            }
        }
    });

    //for form create safe conduct
    const type = formSafeConduct.find('#category').val();
    formSafeConduct.find('#field-tep').hide();
    formSafeConduct.find('.cy-wrapper').hide();
    formSafeConduct.find('#tep').prop('required', false);
    formSafeConduct.find('#cy_date').prop('required', false);

    if (type === "INBOUND") {
        formSafeConduct.find('.cy-wrapper').show();
        formSafeConduct.find('#field-booking').show();
        formSafeConduct.find('.field-work-order').hide();
        formSafeConduct.find('#cy_date').prop('required', false);

        // show booking data
        formSafeConduct.find('.field-work-order').find('input').prop('disabled', true);
        formSafeConduct.find('.field-work-order').find('select').prop('disabled', true);
        formSafeConduct.find('#field-booking').find('input').prop('disabled', false);
        formSafeConduct.find('#field-booking').find('select').prop('disabled', false);

        if (selectExpeditionType.val() === 'INTERNAL') {
            formSafeConduct.find('#field-eseal').hide();
            formSafeConduct.find('#eseal').prop('required', false);
        }
    } else {
        // show job data
        formSafeConduct.find('#field-booking').hide();
        formSafeConduct.find('.field-work-order').show();
        formSafeConduct.find('#field-eseal').hide();
        formSafeConduct.find('#eseal').prop('required', false);

        formSafeConduct.find('#field-booking').find('input').prop('disabled', true);
        formSafeConduct.find('#field-booking').find('select').prop('disabled', true);
        formSafeConduct.find('.field-work-order').find('input').prop('disabled', false);
        formSafeConduct.find('.field-work-order').find('select').prop('disabled', false);
        formSafeConduct.find('.field-work-order').find('select').prop('required', true);

        if (selectExpeditionType.val() === 'EXTERNAL') {
            formSafeConduct.find('#field-tep').show();
            formSafeConduct.find('#tep').prop('required', true);
        }
    }

    //fot get data TEP
    function getDataTEP() {
        // update tep
        /*selectTEP.empty().append($('<option>')).prop("disabled", true);

        if (selectJob.val()) {
            console.log('getDataTEP')
            $.ajax({
                url: baseUrl + 'work-order/ajax_get_work_orders_by_id',
                type: 'GET',
                data: {
                    id_work_order: selectJob.val(),
                },
                success: function (data) {
                    if(data.workOrder != null){
                        var IdBooking = data.workOrder.id_booking;
                        var IdCustomer = data.workOrder.id_customer;
                        var workOrder = data.workOrder;
                        const params = $.param({
                            //category: selectCategory.val(),
                            id_customer: IdCustomer,
                            id_except_safe_conduct: exceptSafeConduct,
                            id_work_order: data.workOrder.id,
                            status: 'UNATTACHED',
                        });

                        fetch(`${baseUrl}transporter-entry-permit/ajax_get_outstanding_tep?${params}`)
                            .then(result => result.json())
                            .then(data => {
                                selectTEP.prop("disabled", false);
                                data.forEach(row => {
                                    // unknown tep put only checked in more than completed at
                                    let includedTep = true;
                                    if(!workOrder.id_transporter_entry_permit && !workOrder.id_vehicle) {
                                        if(row.checked_in_at < workOrder.taken_at) {
                                            includedTep = false;
                                        }
                                    }

                                    if(includedTep) {
                                        selectTEP.append(
                                            $('<option>', {value: row.id})
                                                .text(`${row.tep_code} - ${row.people_name || row.customer_name_out} - ${row.receiver_no_police || 'Empty no police'}`)
                                        );
                                    }
                                });
                                selectTEP.val(idTEP).trigger('change');
                            })
                            .catch(err => {
                                console.log(err);
                                selectTEP.prop("disabled", false);
                            });

                    }
                }
            });
        }*/

        if (selectCategory.val() && selectBooking.val()){
            selectTEP.empty().append($('<option>')).prop("disabled", true);
            const paramsInbound = $.param({
                category: selectCategory.val(),
                id_booking: selectBooking.val(),
                id_except_safe_conduct: exceptSafeConduct,
                status: 'UNATTACHED'
            });
            fetch(`${baseUrl}transporter-entry-permit/ajax_get_outstanding_tep?${paramsInbound}`)
                .then(result => result.json())
                .then(data => {
                    selectTEP.prop("disabled", false);
                    data.forEach(row => {
                        selectTEP.append(
                            $('<option>', {value: row.id})
                                .text(`${row.tep_code} - ${row.customer_name || row.customer_name_in || row.customer_name_out} - (${row.no_reference || row.no_reference_tep_in || row.no_reference_in_req}) - ${row.receiver_no_police || 'Empty no police'}`)
                        );
                    });
                })
                .catch(err => {
                    console.log(err);
                    selectTEP.prop("disabled", false);
                });
        }
    }

    selectTEP.on('change', function () {
        if ($(this).val()) {
            var tep_text = $(this).children("option:selected").html();
            tepTextWrapper.find("#tep_text").val(tep_text.trim());
            console.log($(this).val());
            const options = {
                headers: {
                    Accept: "text/html; charset=utf-8",
                    "Content-Type": "text/plain; charset=utf-8"
                }
            };
            tepDataWrapper.html('Fetching data...');
            fetch(`${baseUrl}transporter-entry-permit/ajax_get_tep_by_id?id_tep=${$(this).val()}`, options)
                .then(result => result.text())
                .then(data => {
                    if (data.trim() === '') {
                        tepDataWrapper.html('<p class="lead text-success">No tep data available</p>');
                    } else {
                        tepDataWrapper.html(data);
                    }
                })
                .catch(console.log);
        }
    });

    /**
     * Internal expedition takes from master,
     * External manually input in form
     */
    selectExpeditionType.on('change', function () {
        var expedition_type_text = $(this).children("option:selected").html();
        formSafeConduct.find('#field-tep').hide();
        formSafeConduct.find('#tep').prop('required', false);
        formSafeConduct.find('#field-eseal').hide();
        formSafeConduct.find('#eseal').prop('required', false);
        expeditionTypeTextWrapper.find("#expedition_type_text").val(expedition_type_text.trim());
        selectBooking.val("").trigger("change");
        selectTEP.val("").trigger("change");

        if ($(this).val() === 'INTERNAL') {
            internalExpeditionWrapper.show();
            internalExpeditionWrapper.find('input').prop('disabled', false);
            internalExpeditionWrapper.find('select').prop('disabled', false);

            externalExpeditionWrapper.hide();
            externalExpeditionWrapper.find('input').prop('disabled', true);
            externalExpeditionWrapper.find('select').prop('disabled', true);

            formSafeConduct.find('#field-tep').hide();
            formSafeConduct.find('#tep').prop('required', false);

            formSafeConduct.find('#field-eseal').hide();
            formSafeConduct.find('#eseal').prop('required', false);
        } else {
            internalExpeditionWrapper.hide();
            internalExpeditionWrapper.find('input').prop('disabled', true);
            internalExpeditionWrapper.find('select').prop('disabled', true);

            externalExpeditionWrapper.show();
            externalExpeditionWrapper.find('input').prop('disabled', false);
            externalExpeditionWrapper.find('select').prop('disabled', false);

            formSafeConduct.find('#field-tep').show();
            formSafeConduct.find('#tep').prop('required', true);

            if(selectCategory.val() == "OUTBOUND"){
                formSafeConduct.find('#field-eseal').hide();
                formSafeConduct.find('#eseal').prop('required', false);
            }
        }
    });

    // populate no police in list of vehicle for internal expedition.

    const booking_id = $("#id_booking_security").val();

    if ( (booking_id != undefined) &&  (booking_id != null) && (booking_id != '') ){
        
        $('#modal-stock-goods').data('booking-id', booking_id).find('.modal-title').text('Booking Goods');
        $('#modal-stock-container').data('booking-id', booking_id).find('.modal-title').text('Booking Container');

        if (!$('.tally-editor').data('stock-url')) {
            const query = {
                id_booking: booking_id,
                id_except_safe_conduct: exceptSafeConduct
            };
            $('.tally-editor').data('stock-url', `${baseUrl}safe-conduct/ajax_get_booking_data?${$.param(query)}`);

            $('#table-container').find('tbody').html(`
                <tr class="row-placeholder">
                    <td colspan="9">No container data</td>
                </tr>
            `);

            $('#table-goods').find('tbody').html(`
                <tr class="row-placeholder">
                    <td colspan="14">No goods data</td>
                </tr>
            `);
        }
    }

    selectInternalVehicle.on('change', function () {
        const noPolicePlate = $(this).find('option:selected').data('no-police');
        $(this).parent().find('#no_police').val(noPolicePlate.toString());
    });

    selectBooking.on('change', function () {
        const bookingId = $(this).val();

        $('#modal-stock-goods').data('booking-id', bookingId).find('.modal-title').text('Booking Goods');
        $('#modal-stock-container').data('booking-id', bookingId).find('.modal-title').text('Booking Container');

        const query = {
            id_booking: bookingId,
            id_except_safe_conduct: exceptSafeConduct
        };
        $('.tally-editor').data('stock-url', `${baseUrl}safe-conduct/ajax_get_booking_data?${$.param(query)}`);

        $('#table-container').find('tbody').html(`
            <tr class="row-placeholder">
                <td colspan="9">No container data</td>
            </tr>
        `);

        $('#table-goods').find('tbody').html(`
            <tr class="row-placeholder">
                <td colspan="14">No goods data</td>
            </tr>
        `);

        if (bookingId) {
            existingSafeConductWrapper.html('Fetching booking data...');
            const options = {
                headers: {
                    Accept: "text/html; charset=utf-8",
                    "Content-Type": "text/plain; charset=utf-8"
                }
            };
            fetch(`${baseUrl}safe-conduct/ajax_get_safe_conducts_by_booking?id_booking=${bookingId}`, options)
                .then(result => result.text())
                .then(data => {
                    if (data.trim() === '') {
                        existingSafeConductWrapper.html('<p class="lead text-success">No existing safe conduct</p>');
                    } else {
                        existingSafeConductWrapper.html(data);
                    }
                })
                .catch(console.log);
        }

        if (selectExpeditionType.val() === "EXTERNAL") {
            getDataTEP();
        }
    });

    var idTEP = null;
    selectJob.on('change', function () {
        if (selectJob.val()) {
            if (selectCategory.val() === "OUTBOUND"){
                console.log('outbound')
                $.ajax({
                    url: baseUrl + 'work-order/ajax_get_work_orders_by_id',
                    type: 'GET',
                    data: {id_work_order: selectJob.val()},
                    success: function (data) {
                        if(data.workOrder != null){
                            idTEP = data.workOrder.id_transporter_entry_permit;
                            var idVehicle = data.workOrder.id_vehicle;
                            // console.log(data.workOrder);
                            if (idTEP!=null) {
                                selectExpeditionType.val("EXTERNAL").change();
                                // selectTEP.val(idTEP).change();
                                // $('#tep option[value="'+idTEP+'"]').prop('selected', true).change();

                                // if tep not set then make them choose manually
                                tepTextWrapper.show();
                                tepWrapper.hide();
                            }
                            if (idVehicle!=null) {
                                selectExpeditionType.val("INTERNAL").change();
                                $('#vehicle option[data-id="'+idVehicle+'"]').prop('selected', true).change();
                            }
                            if (idTEP==null && idVehicle==null) {
                                selectExpeditionType.val("").change();

                                // if tep not set then make them choose manually
                                if(!idTEP) {
                                    selectExpeditionType.val("EXTERNAL").change();
                                    tepTextWrapper.hide();
                                    tepWrapper.show();
                                } else {
                                    tepTextWrapper.show();
                                    tepWrapper.hide();
                                }
                            }

                            // moved from getDataTEP()
                            selectTEP.empty().append($('<option>')).prop("disabled", true);
                            var IdCustomer = data.workOrder.id_customer;
                            var workOrder = data.workOrder;
                            const params = $.param({
                                //category: selectCategory.val(),
                                id_customer: IdCustomer,
                                id_except_safe_conduct: exceptSafeConduct,
                                id_work_order: data.workOrder.id,
                                status: 'UNATTACHED',
                            });

                            fetch(`${baseUrl}transporter-entry-permit/ajax_get_outstanding_tep?${params}`)
                                .then(result => result.json())
                                .then(data => {
                                    selectTEP.prop("disabled", false);
                                    data.forEach(row => {
                                        // unknown tep put only checked in more than completed at
                                        //let includedTep = true;
                                        //if(!workOrder.id_transporter_entry_permit && !workOrder.id_vehicle) {
                                        //    if(row.checked_in_at < workOrder.taken_at) {
                                        //        includedTep = false;
                                        //    }
                                        //}

                                        //if(includedTep) {
                                            selectTEP.append(
                                                $('<option>', {value: row.id})
                                                    .text(`${row.tep_code} - ${row.customer_name || row.people_name || row.customer_name_out} - ${row.receiver_no_police || 'Empty no police'}`)
                                            );
                                        //}
                                    });
                                    selectTEP.val(idTEP).trigger('change');
                                })
                                .catch(err => {
                                    console.log(err);
                                    selectTEP.prop("disabled", false);
                                });

                        }else{
                            alert("data work order tidak ditemukan");
                        }
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        alert(err.Message);
                    }
                });
            }

            // get information of the job (items or containers)
            const options = {
                headers: {
                    Accept: "text/html; charset=utf-8",
                    "Content-Type": "text/plain; charset=utf-8"
                }
            };
            jobDataWrapper.html('Fetching data...');
            fetch(`${baseUrl}work-order/ajax_get_work_orders_by_id?id_work_order=${$(this).val()}`, options)
                .then(result => result.text())
                .then(data => {
                    if (data.trim() === '') {
                        jobDataWrapper.html('<p class="lead text-success">No job data available</p>');
                    } else {
                        jobDataWrapper.html(data);
                    }
                })
                .catch(console.log);
        }
    });


    var modalAttachment = $('#modal-attachment');
    if (modalAttachment.length) {
        tableSafeConduct.on('click', '.btn-attachment-safe-conduct', function (e) {
            e.preventDefault();
    
            var currentRow = $(this).closest('.row-safe-conduct');
            var id = currentRow.data('id');
            var noSafeConduct = currentRow.data('no');
            var url = $(this).attr('href');
    
            modalAttachment.find('.uploaded-file .old-file').remove();
    
            const data = tableSF.row(currentRow.closest('tr')).data();
            if (data['safe_conduct_attachments'].length > 0) {
                data['safe_conduct_attachments'].forEach((attachment, index) => {
                    modalAttachment.find('.uploaded-file').append(
                        `<p class="old-file">${index + 1} &nbsp; &nbsp; ${truncate(attachment.src.replace(/^.*[\\\/]/, ''), 35)}</p>`
                    );
                });
                modalAttachment.find('.uploaded-file').append('<p class="old-file" style="color: maroon">You will replace uploaded file in this submission!</p>');
            };
    
            modalAttachment.find('input[name=id]').val(id);
            modalAttachment.find('#safe-conduct-title').text(noSafeConduct);
            modalAttachment.find('form').attr('action', url);
    
            modalAttachment.modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    
        $(document).on('click', '.upload-photo', function () {
            var btnSubmit = $(this).closest('form');
            btnSubmit.find('[type="submit"]').attr('disabled', true);
            $('.upload-photo').fileupload({
                url: baseUrl + 'upload-document-file/upload-s3',
                dataType: 'json',
                done: function (e, data) {
                    var inputFileParent = $(this).closest('.form-group');
                    inputFileParent.find('.text-danger').remove();
                    inputFileParent.find('.placeholder').remove();
                    btnSubmit.find('[type="submit"]').attr('disabled', false);
                    $.each(data.result, function (index, file) {
                        if (file != null && file.status) {
                            inputFileParent.find('.uploaded-file')
                                .append($('<p/>', {class: 'text-ellipsis'})
                                    .html('<a href="#" data-file="' + file.data.file_name + '" class="btn btn-danger btn-sm btn-delete-file">DELETE</a> &nbsp; &nbsp; ' + file.data.client_name));
                            inputFileParent.find('.upload-input-wrapper')
                                .append($('<input/>', {
                                    type: 'hidden',
                                    name: 'attachments[]',
                                    value: file.data.file_name
                                }));
                            inputFileParent.find('.file-upload-info').val(file.data.client_name);
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
                },
                progressall: function (e, data) {
                    // console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                        'width',
                        progress + '%'
                    ).text(progress + '%');
                    if (progress == 100) {
                        btnSubmit.find(':submit').attr('disabled', false);
                    }
                },
                fail: function (e, data) {
                    btnSubmit.find('[type="submit"]').attr('disabled', false);
                    alert(data.textStatus);
                }
            });
        });

        function checkButtonUpload(wrapper) {
            if (wrapper.find('.upload-input-wrapper').children().length) {
                wrapper.find('.file-upload-browse').text('Add More file');
                wrapper.find('.button-file').attr('disabled', true);
            } else {
                wrapper.find('.file-upload-browse').text('Select file');
                wrapper.find('.button-file').attr('disabled', false);
                wrapper.find('.progress-bar')
                    .removeClass('progress-bar-danger')
                    .addClass('progress-bar-success')
                    .css(
                        'width', '0%'
                    );
            }
        }
    
        $(document).on('click', '.btn-delete-file', function (e) {
            e.preventDefault();
            console.log('delete');
            var buttonDelete = $(this);
            var file = buttonDelete.data('file');
            $.ajax({
                url: baseUrl + 'upload-document-file/delete-temp-s3',
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
                        inputFileParent.find('.file-upload-info').val("");
                        inputFileParent.find('.file-upload-info').attr("placeholder", "Upload attachment");
                        buttonDelete.parent().remove();
                        checkButtonUpload(inputFileParent);
                        alert('File ' + file + ' is deleted');
                    } else {
                        alert('Failed delete uploaded file');
                    }
                }
            })
        });
    }
});