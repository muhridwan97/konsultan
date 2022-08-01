$(function () {
    const table = $('#table-tep.table-ajax');
    const controlTemplate = $('#control-tep-template').html();
    table.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search permit"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit/ajax-get-data',
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        pageLength: 25,
        columns: [
            {data: 'no', class: 'responsive-hide'},
            {data: 'customer_name_in', class: 'responsive-title'},
            {data: 'no_booking'},
            {data: 'tep_category'},
            {data: 'tep_code'},
            {data: 'checked_in_at'},
            {data: 'checked_out_at'},
            {data: 'receiver_name'},
            {data: 'no_safe_conduct'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-checkin-by'],
            render: function (data, type, full) {
                const checkIn = $.trim(data) === '' || data == null ? '-' : moment(data).format('D MMMM YYYY H:mm')
                return checkIn + (data ? `<br><small class="text-muted">${full.checker_name || ''}</small>` : '');
            }
        }, {
            targets: ['type-checkout-by'],
            render: function (data, type, full) {
                const checkOut = $.trim(data) === '' || data == null ? '-' : moment(data).format('D MMMM YYYY H:mm')
                return checkOut + (data ? `<br><small class="text-muted">${full.checker_out_name || ''}</small>` : '');
            }
        }, {
            targets: ['type-carrier'],
            render: function (data, type, full) {
                return (full.receiver_name || '-') + '<br>' + (full.receiver_no_police || '') + '<br><small class="text-muted">' + (full.receiver_vehicle || '') + '</small>';
            }
        }, {
            targets: ['type-code'],
            render: function (data, type, full) {
                return full.tep_code + (full.expired_at ? `<br><small class="text-muted">Expired At : ${full.expired_at}</small>` : '');
            }
        }, {
            targets: ['type-customer'],
            render: function (data, type, full) {
                if (data){
                    return data.replace(/,/g, '<br>')
                } else if($.trim(full.tep_category) === 'OUTBOUND' && $.trim(data) === ''){
                    if ($.trim(full.people_name) === '') {
                        return $.trim(full.customer_name_out);
                    } else {
                        return $.trim(full.people_name);
                    }
                }else if($.trim(full.tep_category) === 'INBOUND'){
                    if($.trim(full.tep_category) === 'INBOUND' && $.trim(data) === ''){
                        if ($.trim(full.customer_name_in) === '') {
                            return $.trim(full.customer_name_out);
                        } else {
                            return full.customer_name_in;
                        }
                        return  (full.customer_name_in);
                        // `${data}<br><small class="text-muted">${full.category}</small>`
                    }else{
                        return data;
                    }
                }else{//untuk empty container
                    if ($.trim(full.customer_name_out_ec) === '') {
                        return $.trim(full.customer_name_out_ecs);
                    } else {
                        return $.trim(full.customer_name_out_ec);
                    }
                }
            }
        }, {
            targets: ['type-booking'],
            className: "no-wrap",
            render: function (data, type, full) {
                if (data){
                    return data.replace(/,/g, '<br>', '\g')
                } else if($.trim(full.tep_category) === 'OUTBOUND' && $.trim(data) === ''){
                     var split = (full.no_booking_safe_conduct || '').split(",");
                    return split.join('</br>');
                }else if($.trim(full.tep_category) === 'INBOUND'){
                    var split = (full.no_booking_tep_in || '').split(",");
                    if($.trim(full.tep_category) === 'INBOUND' && $.trim(data) === ''){
                        return split.join('</br>');
                    }else{
                        return data;
                    }
                }else{
                    return (full.no_booking_safe_conduct_ec || '').replace(',','</b><br>');
                }
            }
        }, {
            targets: ['type-safe-conduct'],
            className: "no-wrap",
            render: function (data, type, full) {
                return data ? data.replace(/,/g, '<br>', '\g') : '-';
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let idUser = $('#id_user').val();
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{code}}/g, full.tep_code)
                    .replace(/{{canEdit}}/g, (full.can_edit==1 && idUser==full.checked_in_by && full.checked_out_by==null)?'' : 'hidden')
                    .replace(/{{cancel}}/g, (moment(full.expired_at).format('D MMMM YYYY')>= moment().format('D MMMM YYYY'))?'' : 'hidden');

                control = $.parseHTML(control);

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' || data == null ? '-' : data;
            }
        }]
    });

    const formTEP = $('#form-tep');
    formTEP.find('#email_type').on('change', function () {
        if($(this).val() === 'INPUT') {
            formTEP.find('#email-input-field').show();
            formTEP.find('#input_email').prop('required', true);
            formTEP.find('#email-type-field')
                .addClass('col-md-4')
                .removeClass('col-md-8');
        } else {
            formTEP.find('#email-input-field').hide();
            formTEP.find('#input_email').prop('required', false);
            formTEP.find('#email-type-field')
                .addClass('col-md-8')
                .removeClass('col-md-4');
        }
    });

    $('.btn-reveal-items').on('click', function () {
        if ($(this).hasClass('collapsed')) {
            $(this).text('HIDE');
        } else {
            $(this).text('SHOW');
        }
    });

    formTEP.find("#tep_category").on('change', function(){
        var category = $(this).val();
        if(category == "INBOUND"){
            formTEP.find(".booking-wrapper").show();
            formTEP.find("#booking").attr("required", true);
            formTEP.find(".customer-wrapper").hide();
            formTEP.find("#customer").attr("required", false);
            formTEP.find("#tep-before-wrapper").hide();
            formTEP.find("#tep_before").val('no').trigger("change");
        }else{
            formTEP.find(".customer-wrapper").show();
            formTEP.find("#customer").attr("required", true);
            formTEP.find(".booking-wrapper").hide();
            formTEP.find("#booking").attr("required", false);
            formTEP.find("#tep-before-wrapper").show();
        }
    });    

    formTEP.find("#tep_before").on('change', function(){
        var tep_before = $(this).val();
        if(tep_before == "yes"){
            formTEP.find("#tep-reference-field").show();
            formTEP.find("#tep_reference").attr("required", true);
            formTEP.find('#tep-before-field')
                .addClass('col-md-6')
                .removeClass('col-md-12');
        }else{
            formTEP.find("#tep-reference-field").hide();
            formTEP.find("#tep_reference").attr("required", false);
            formTEP.find('#tep-before-field')
                .addClass('col-md-12')
                .removeClass('col-md-6');
        }
    }); 

    formTEP.find("#customer").on('change', function(){
        var id_customer = $(this).val();
        formTEP.find('#tep_reference').data("placeholder", "Fetching data...");
        formTEP.find('#tep_reference').select2();
        $.ajax({
            url: baseUrl + 'transporter_entry_permit/get_tep_reference_customer',
            type: 'POST',
            data: {
                id_customer: id_customer
            },
            success: function (data) {
                console.log(data);
                formTEP.find('#tep_reference').children('option').remove();
                data.forEach(function(data){
                    formTEP.find('#tep_reference').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.tep_code+" ("+data.receiver_no_police+"-"+data.receiver_vehicle+")")); 
                  });
                if (data == '') {
                    formTEP.find('#tep_reference').data("placeholder", "No data...");
                    formTEP.find('#tep_reference').select2();
                }
            },
            error: function() { 
                alert("gagal memperoleh tep reference");
            }  
        });
    });

    // formTEP.find("#booking").on('change', function(){
    //     var id_booking = $(this).val();
    //     formTEP.find('#tep_reference').data("placeholder", "Fetching data...");
    //     formTEP.find('#tep_reference').select2();
    //     $.ajax({
    //         url: baseUrl + 'transporter_entry_permit/get_tep_reference_booking',
    //         type: 'POST',
    //         data: {
    //             id_booking: id_booking
    //         },
    //         success: function (data) {
    //             console.log(data);
    //             formTEP.find('#tep_reference').children('option').remove();
    //             data.forEach(function(data){
    //                 formTEP.find('#tep_reference').append($("<option></option>")
    //                 .attr("value",data.id)
    //                 .text(data.tep_code+" ("+data.receiver_no_police+"-"+data.receiver_vehicle+")")); 
    //               });
    //             if (data == '') {
    //                 formTEP.find('#tep_reference').data("placeholder", "No data...");
    //                 formTEP.find('#tep_reference').select2();
    //             }
    //         },
    //         error: function() { 
    //             alert("gagal memperoleh tep reference");
    //         }  
    //     });
    // });
    // populate data for external expedition.

    const booking_id = $("#id_booking_security").val();

    if ( (booking_id != undefined) &&  (booking_id != null) && (booking_id != '') ){
        
        $('#modal-stock-goods').data('booking-id', booking_id).find('.modal-title').text('Booking Goods');
        $('#modal-stock-container').data('booking-id', booking_id).find('.modal-title').text('Booking Container');

        const query = {
            id_booking: booking_id,
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

    const formTEPReqInbound = $('#form-tep-request-inbound');
    formTEPReqInbound.find("#customer").on('change', function(){
        var id_customer = $(this).val();
        
        selectBooking = formTEPReqInbound.find('#booking');
        selectBooking.empty();
        formTEPReqInbound.find('#aju').data("placeholder", "Fetching data...");
        const params = $.param({
            id_customer: id_customer,
            category: 'INBOUND'
        });

        fetch(`${baseUrl}transporter-entry-permit/ajax_get_available_sppb?${params}`)
            .then(result => result.json())
            .then(data => {
                formTEPReqInbound.find('#aju').children('option').remove();
                formTEPReqInbound.find('#aju').append($("<option></option>")
                    .attr("value",'')
                    .text('')); 
                    console.log(data);
                data.forEach(function(data){
                    formTEPReqInbound.find('#aju').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.description)); 
                  });
                if (data == '') {
                    formTEPReqInbound.find('#aju').data("placeholder", "No data...");
                    formTEPReqInbound.find('#aju').select2();
                }
                selectBooking.val(data.length>0?'have':'');
            })
            .catch(err => {
                console.log(err);
                selectBooking.prop("disabled", false);
            });
    });

    const formTEPReq = $('#form-tep-request');
    formTEPReq.find("#customer").on('change', function(){
        var id_customer = $(this).val();
        
        selectBooking = formTEPReq.find('#booking');
        selectBooking.empty();
        formTEPReq.find('#aju').data("placeholder", "Fetching data...");
        const params = $.param({
            id_customer: id_customer,
            category: 'OUTBOUND'
        });

        fetch(`${baseUrl}transporter-entry-permit/ajax_get_available_sppb?${params}`)
            .then(result => result.json())
            .then(data => {
                formTEPReq.find('#aju').children('option').remove();
                formTEPReq.find('#aju').append($("<option></option>")
                    .attr("value",'')
                    .text('')); 
                    console.log(data);
                data.forEach(function(data){
                    formTEPReq.find('#aju').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.description)); 
                  });
                if (data == '') {
                    formTEPReq.find('#aju').data("placeholder", "No data...");
                    formTEPReq.find('#aju').select2();
                }
                selectBooking.val(data.length>0?'have':'');
            })
            .catch(err => {
                console.log(err);
                selectBooking.prop("disabled", false);
            });
    });

    formTEPReq.find("#aju").on('change', function(){
        var id_booking = $(this).val();
        
        selectGoods = formTEPReq.find('#goods');
        selectContainer = formTEPReq.find('#container');
        selectGoods.empty();
        selectContainer.empty();
        
        const params = $.param({
            id_booking: id_booking
        });
        formTEPReq.find('#goods').data("placeholder", "Fetching data...");
        formTEPReq.find('#goods').select2();
        formTEPReq.find('#container').data("placeholder", "Fetching data...");
        formTEPReq.find('#goods').select2();

        fetch(`${baseUrl}transporter-entry-permit/ajax_get_goods_container?${params}`)
            .then(result => result.json())
            .then(data => {
                console.log(data);                
                formTEPReq.find('#goods').children('option').remove();
                formTEPReq.find('#goods').append($("<option></option>")
                    .attr("value",'')
                    .text('')); 
                formTEPReq.find('#container').children('option').remove();
                formTEPReq.find('#container').append($("<option></option>")
                    .attr("value",'')
                    .text('')); 
                formTEPReq.find('#container').append($("<option></option>")
                    .attr("value",0)
                    .text('SELECT ALL')); 
                data.record_goods.forEach(function(data){
                    formTEPReq.find('#goods').append($("<option></option>")
                    .attr("value",data.id_goods)
                    .text(data.goods_name)); 
                  });
                data.record_container.forEach(function(data){
                    formTEPReq.find('#container').append($("<option></option>")
                    .attr("value",data.id_container)
                    .text(data.no_container)); 
                });
                if (data.record_goods == '') {
                    formTEPReq.find('#goods').data("placeholder", "No data...");
                    formTEPReq.find('#goods').select2();
                }else{
                    formTEPReq.find('#goods').data("placeholder", "Select Goods");
                    formTEPReq.find('#goods').select2();
                }
                if (data.record_container == '') {
                    formTEPReq.find('#container').data("placeholder", "No data...");
                    formTEPReq.find('#container').select2();
                    formTEPReq.find('#container').children('option').remove();
                    formTEPReq.find('#container').append($("<option></option>")
                        .attr("value",'')
                        .text('')); 
                }else{
                    formTEPReq.find('#container').data("placeholder", "Select Container");
                    formTEPReq.find('#container').select2();
                }
            })
            .catch(err => {
                console.log(err);
                selectBooking.prop("disabled", false);
            });
    });

    const TEPQueue = $('#tep-queue-list');
    let modalSetTep = $('#modal-set-tep');
    const queueTimeSelector = modalSetTep.find('#queue_time');
    const expressWrapper = $('#express-wrapper');
    const formSetTEP = modalSetTep.find('#form-set-tep');
    TEPQueue.find(".btn-set-tep").on('click', function(){
        var id_request = $(this).closest('.queue-list').data('id');
        var customer = $(this).closest('.queue-list').data('customer');
        var customerid = $(this).closest('.queue-list').data('customerid');
        var uploadid = $(this).closest('.queue-list').data('uploadid');
        var date = $(this).closest('.queue-list').data('date');
        var category = $(this).closest('.queue-list').data('category');
        var url = $(this).data('url');

        modalSetTep.find('.modal-title').text('Set Request');
        modalSetTep.find('.set-tep-message').text('Are you sure want to create tep');
        modalSetTep.find('#btn-submit-create-tep').text('Create TEP');

        modalSetTep.find('#customer-name').text(customer.toString());
        modalSetTep.find('#date').text(date.toString());
        modalSetTep.find('input[name="id"]').val(id_request.toString());
        modalSetTep.find('input[name="customer"]').val(customer.toString());
        modalSetTep.find('input[name="id_customer"]').val(customerid.toString());
        modalSetTep.find('input[name="id_upload"]').val(uploadid.toString());
        modalSetTep.find('input[name="tep_date"]').val(date.toString());
        modalSetTep.find('input[name="category"]').val(category.toString());
        modalSetTep.find('form').attr('action', url.toString());
        modalSetTep.find('#btn-today').attr('disabled', false).html('Change today');
        expressWrapper.hide();
        expressWrapper.removeClass('required-document');
        expressWrapper.find('#express_service_type').prop('required',false);
        expressWrapper.find('#express_service_type').val('').trigger("change");

        var buttonDelete = expressWrapper.find('.btn-delete-file');
        var file = buttonDelete.data('file');
        var inputFileParent = buttonDelete.closest('.form-group');
        inputFileParent.find('input[value="' + file + '"]').remove();
        buttonDelete.parent().remove();
        checkButtonUpload(inputFileParent);

        queueTimeSelector.prop('min', queueTimeSelector.data('min-next'));
        console.log(queueTimeSelector.prop('min'));
        modalSetTep.editSetTep = false;
        modalSetTep.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    TEPQueue.find(".btn-edit-set-tep").on('click', function(){
        var id_request = $(this).closest('.queue-list').data('id');
        var customer = $(this).closest('.queue-list').data('customer');
        var customerid = $(this).closest('.queue-list').data('customerid');
        var uploadid = $(this).closest('.queue-list').data('uploadid');
        var date = $(this).closest('.queue-list').data('date');
        var category = $(this).closest('.queue-list').data('category');
        var url = $(this).data('url');

        modalSetTep.find('.modal-title').text('Edit Set Request');
        modalSetTep.find('.set-tep-message').text('Are you sure want to update lastly set tep');
        modalSetTep.find('#btn-submit-create-tep').text('Update TEP');

        modalSetTep.find('#customer-name').text(customer.toString());
        modalSetTep.find('#date').text(date.toString());
        modalSetTep.find('input[name="id"]').val(id_request.toString());
        modalSetTep.find('input[name="customer"]').val(customer.toString());
        modalSetTep.find('input[name="id_customer"]').val(customerid.toString());
        modalSetTep.find('input[name="id_upload"]').val(uploadid.toString());
        modalSetTep.find('input[name="tep_date"]').val(date.toString());
        modalSetTep.find('input[name="category"]').val(category.toString());
        modalSetTep.find('form').attr('action', url.toString());
        modalSetTep.find('#btn-today').attr('disabled', false).html('Change today');
        expressWrapper.hide();
        expressWrapper.removeClass('required-document');
        expressWrapper.find('#express_service_type').prop('required',false);
        expressWrapper.find('#express_service_type').val('').trigger("change");

        var buttonDelete = expressWrapper.find('.btn-delete-file');
        var file = buttonDelete.data('file');
        var inputFileParent = buttonDelete.closest('.form-group');
        inputFileParent.find('input[value="' + file + '"]').remove();
        buttonDelete.parent().remove();
        checkButtonUpload(inputFileParent);

        if ($(this).data('is-today')) {
            queueTimeSelector.prop('min', queueTimeSelector.data('min-today'));
        } else {
            queueTimeSelector.prop('min', queueTimeSelector.data('min-next'));
        }
        console.log(queueTimeSelector.prop('min'));

        modalSetTep.editSetTep = true;
        modalSetTep.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#modal-set-tep').find('#btn-today').on('click', function(){
        let tgl = moment().format("DD MMMM YYYY");
        modalSetTep.find('#date').text(tgl.toString());
        modalSetTep.find('input[name="tep_date"]').val(tgl.toString());
        $(this).attr('disabled', true).html('Done');

        queueTimeSelector.prop('min', queueTimeSelector.data('min-today'));
        console.log(queueTimeSelector.prop('min'));
        expressWrapper.show();
        expressWrapper.addClass('required-document');
        expressWrapper.find('#express_service_type').prop('required',true);
    });
    
    $('.btn-add-slot').on('click', function(){
        var url = $(this).data('url');

        let modal = $('#modal-add-slot');
        modal.find('form').attr('action', url.toString());
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    // reset filter value
    $(document).on('click', '.btn-reset-filter-queue-tep', function () {
        $('.form-filter .select2').val('').trigger("change");
        $('.form-filter input').val('');
    });
    // reset filter value
    $(document).on('click', '.btn-reset-filter-queue', function () {
        $('.form-filter .select2').val('').trigger("change");
        $('.form-filter input').val('');
    });

    const listQueue = $('#list-queue-request');
	const queueCheckbox = listQueue.find('.queue-check');
    const queueCheckAll = listQueue.find('#check-all');
    
    queueCheckbox.on('ifChanged', function (e) {
        e.preventDefault();
        if ($(this).is(':checked')) {
            $(this).closest('div.row').find('.btn-set-tep').addClass('disabled');
            $(this).closest('div.row').find('.btn-skip').addClass('disabled');
        }else{
            $(this).closest('div.row').find('.btn-set-tep').removeClass('disabled');
            $(this).closest('div.row').find('.btn-skip').removeClass('disabled');
        }
        const totalChecked = listQueue.find('.queue-check:checked').length;
        if (totalChecked > 0) {
            $('.batch-action').show();
        } else {
            $('.batch-action').hide();
        }
    });

    queueCheckAll.on('ifChanged', function () {
        if ($(this).is(':checked')) {
            $('.batch-action').show();
            queueCheckbox.prop('checked', true);
        } else {
            $('.batch-action').hide();
            queueCheckbox.prop('checked', false);
        }

        const totalChecked = listQueue.find('.queue-check:checked').length;
        if (totalChecked > 0) {
            $('.batch-action').show();
        } else {
            $('.batch-action').hide();
        }
    });

   $('#btn-deleted-point').on('click', function () {
    const selectedRows = listQueue.find('.queue-check:checked');

    let ids = [];
    selectedRows.each(function (index, item) {
        ids.push($(item).val());
    });

    const modalDelete = $('#modal-delete');
    modalDelete.find('form').attr('action', $(this).data('url') || $(this).attr('href'));
    modalDelete.find('input[name=id]').val(ids.toString());
    modalDelete.find('button[type=submit]').text('DELETED');

    modalDelete.find('.delete-title').text('All');
    modalDelete.find('.delete-label').text('Deleted ' + selectedRows.length + ' row point(s)');

    modalDelete.modal({
        backdrop: 'static',
        keyboard: false
        });
    });

    $('#table-tep').on('click','.btn-cancel', function (e) {
        e.preventDefault();
        console.log('asd');
        const modalCancel = $('#modal-cancel');
        modalCancel.find('form').attr('action', $(this).data('url'));
    
        modalCancel.find('#label-tep-code').text($(this).data('label'));
    
        modalCancel.modal({
            backdrop: 'static',
            keyboard: false
            });
    });

    $(".batch-action").on('click', function(){
    const selectedRows = listQueue.find('.queue-check:checked');
    let ids = [];
    let idCus = [];
    let idUp = [];
    let slot = $(selectedRows[0]).data('slot');
    let date = $(selectedRows[0]).data('date');
    let category = $(selectedRows[0]).data('category');
    var url = $(this).data('url');
    var benar = true;
    console.log(selectedRows.length);
    if (selectedRows.length < 2) {
        alert("merger must be more than 1");
        benar = false;
    }
    selectedRows.each(function (index, item) {
        ids.push($(item).val());
        idCus.push($(item).data('customerid'));
        if (slot != $(item).data('slot')) {
            alert("slot must be the same");
            benar = false;
        }
        if (date != $(item).data('date')) {
            alert("date must be the same");
            benar = false;
        }
        if (category != $(item).data('category')) {
            alert("category must be the same");
            benar = false;
        }
        idUp.push($(item).data('uploadid'));
    });
    if (benar) {
        let modal = $('#modal-set-tep-merge');
        // modal.find('#customer-name').text(customer.toString());
        modal.find('#date').text(date.toString());
        modal.find('input[name="id"]').val(ids.toString());
        // modal.find('input[name="customer"]').val(customer.toString());
        modal.find('input[name="id_customer"]').val(idCus.toString());
        modal.find('input[name="id_upload"]').val(idUp.toString());
        modal.find('input[name="tep_date"]').val(date.toString());
        modal.find('input[name="category"]').val(category.toString());
        modal.find('form').attr('action', url.toString());
    
        modal.modal({
            backdrop: 'static',
            keyboard: false
            });
        }
    });
    $('.btn-skip').on('click', function(){
        var url = $(this).data('url');

        let modal = $('#modal-skip');
        var id_request = $(this).closest('.queue-list').data('id');
        modal.find('input[name="id"]').val(id_request.toString());
        modal.find('form').attr('action', url.toString());
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    TEPQueue.find(".btn-add-merge").on('click', function(){
        var id_request = $(this).closest('.queue-list').data('id');
        var customer = $(this).closest('.queue-list').data('customer');
        var customerid = $(this).closest('.queue-list').data('customerid');
        var uploadid = $(this).closest('.queue-list').data('uploadid');
        var date = $(this).closest('.queue-list').data('date');
        var category = $(this).closest('.queue-list').data('category');
        var reference = $(this).closest('.queue-list').data('reference');
        var slot = $(this).closest('.queue-list').data('slot');
        var armada = $(this).closest('.queue-list').data('armada');

        var url = $(this).data('url');
        let modal = $('#modal-add-merge');

        //mengambil slot yang sudah terpakai
        let request = `${baseUrl}transporter-entry-permit/ajax_get_request_by_id?id=${id_request}`;
        
        fetch(request)
            .then(result => result.json())
            .then(request => {
                // save last stock data so we do not need to fetch again   
                let req = request.request;             
                modal.find('#label-slot').text(slot-req.slot_created);
                modal.find('input[name="slot_created"]').val(req.slot_created);
            })
            .catch(console.log);

        modal.find('#label-customer').text(customer.toString());
        modal.find('#label-reference').text(reference.toString());
        modal.find('input[name="id"]').val(id_request.toString());
        modal.find('input[name="id_customer"]').val(customerid.toString());
        modal.find('input[name="id_upload"]').val(uploadid.toString());
        modal.find('input[name="tep_date"]').val(tep_date.toString());
        modal.find('input[name="category"]').val(category.toString());
        modal.find('input[name="slot"]').val(slot);
        modal.find('input[name="armada"]').val(armada);
        // modal.find('form').attr('action', url.toString());
        getTepQueue(slot,armada,category,id_request);
        
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    
    const modalAddMerge = $('#modal-add-merge');
    const modalMergeValidate = $('#modal-merge-validate');
    const btnReloadTepQueue = modalAddMerge.find('#btn-reload-tep');
    /**
     * Reload (bust) last tep queue data (replace old stock variable)
     */
    btnReloadTepQueue.on('click', function () {
        const slot = modalAddMerge.find('#slot_created').val();
        const armada = modalAddMerge.find('#armada').val();
        const category = modalAddMerge.find('#category').val();
        const id_request = modalAddMerge.find('#id').val();

        if (slot) {
            getTepQueue(slot, armada,category,id_request);
        }
    });

    /**
     * Fetch tep queue from server and setup table.
     * 
     */
    function getTepQueue(slot_created,armada,category,id_request) {
        let stockUrl = `${baseUrl}transporter-entry-permit/ajax_get_tep_queue?armada=${armada}&category=${category}&id_request=${id_request}`;

        modalAddMerge.find('tbody').html(`
            <tr><td colspan="11">Fetching TEP queue...</td></tr>
        `);

        fetch(stockUrl)
            .then(result => result.json())
            .then(stock => {
                // save last stock data so we do not need to fetch again
                lastStockData = stock;
                console.log(stock);
                initTepQueue(lastStockData);
            })
            .catch(console.log);
        if(slot_created<1){
            var buttonMerge = modalAddMerge.find('.btn-tep-merge');
            buttonMerge.attr('disabled', true);
        }
    }

    /**
     * Initialize table stock list from source data.
     * @param stock
     */
    function initTepQueue(stock) {
        // const booking = stock ? stock.booking : null;
        // if (booking) {
        //     modalAddMerge.find('#label-customer').text(booking.customer_name);
        //     modalAddMerge.find('#label-reference').text(booking.no_reference);
        // }
        const teps = stock ? stock.teps : [];
        if (teps.length) {
            modalAddMerge.find('tbody').empty();

            // // find taken stock
            // const takenStock = Array.from(tableContainer.find('.row-stock')).map((row) => {
            //     return $(row).find('#id_container').val();
            // });

            // loop through the stock
            teps.forEach((tep, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td id="customer-label">${tep.customer_name_out}</td>
                        <td id="tep-code-label">${tep.tep_code}</td>
                        <td id="reference-label">${tep.no_aju != null ? tep.no_aju : tep.no_aju_multi}</td>
                        <td id="description-label">${tep.description_req || '-'}</td>
                        <td id="queue-time-label">${tep.queue_time || '-'}</td>
                        <td>
                            <input type="hidden" name="tep-data" id="tep-data" value="${encodeURIComponent(JSON.stringify(tep))}">
                            <button class="btn btn-sm btn-primary btn-tep-merge" data-tep-code="${tep.tep_code}"
                            data-id-tep = "${tep.id}" type="button">
                                MERGE
                            </button>
                        </td>
                    </tr>
                `;
                modalAddMerge.find('tbody').first().append(row);
            });
        } else {
            modalAddMerge.find('tbody').html(`
                <tr><td colspan="11">No data available</td></tr>
            `);
        }
    }

    /**
     * merge with tep selected
     */
    modalAddMerge.on('click', '.btn-tep-merge', function () {
        let tep_code = $(this).data('tep-code');
        let id_tep = $(this).data('id-tep');
        let id_request = modalAddMerge.find('#id').val();
        let customerid = modalAddMerge.find('#id_customer').val();
        let uploadid = modalAddMerge.find('#id_upload').val();
        let slot_created = modalAddMerge.find('#slot_created').val();
        let slot = modalAddMerge.find('#slot').val();
        let armada = modalAddMerge.find('#armada').val();
        
        let modal = $('#modal-merge-validate');
        modal.find('#label-tep-code').text(tep_code.toString());
        modal.find('input[name="id"]').val(id_request.toString());
        modal.find('input[name="id_tep"]').val(id_tep.toString());
        modal.find('input[name="id_customer"]').val(customerid.toString());
        modal.find('input[name="id_upload"]').val(uploadid.toString());
        modal.find('input[name="slot_created"]').val(slot_created);
        modal.find('input[name="slot"]').val(slot);
        modal.find('input[name="armada"]').val(armada.toString());
        modal.find('form').attr('action', baseUrl.toString());
        
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    /**
     * merge validate with tep selected
     */
    modalMergeValidate.on('click', '.btn-merge-validate', function () {
        let id_request = modalMergeValidate.find('#id').val();
        let customerid = modalMergeValidate.find('#id_customer').val();
        let uploadid = modalMergeValidate.find('#id_upload').val();
        let id_tep = modalMergeValidate.find('#id_tep').val();
        let slot_created = modalMergeValidate.find('#slot_created').val();
        let slot = modalMergeValidate.find('#slot').val();
        let armada = modalMergeValidate.find('#armada').val();
        slot_created = parseInt(slot_created);
        slot = parseInt(slot);
        var buttonSubmit = $(this);
        if (buttonSubmit.length) {
            var message = buttonSubmit.data('touch-message');
            if (message == undefined) {
                message = 'Merging...';
            }
            buttonSubmit.attr('disabled', true).html(message);
        }
        $.ajax({
            url: baseUrl + 'transporter_entry_permit/ajax_set_merge_request',
            type: 'POST',
            data: {
                id_request: id_request,
                id_customer: customerid,
                id_upload: uploadid,
                id_tep: id_tep,
            },
            success: function (data) {
                if(data.status){
                    if(armada==='TCI'){
                        btnReloadTepQueue.trigger("click");
                        modalMergeValidate.modal('hide');
                    }else if((slot_created+1)==slot){
                        window.location.href=baseUrl+"/transporter_entry_permit/queue";
                    }else{
                        buttonSubmit.attr('disabled', false).html('Merge Request');
                        TEPQueue.find(".btn-add-merge").trigger('click');
                        modalMergeValidate.modal('hide');
                    }
                }else{
                    buttonSubmit.attr('disabled', false).html('Merge Request');
                    alert("gagal merge");
                }
            },
            error: function() { 
                alert("gagal memperoleh tep reference");
            }  
        });
    });
    const formTEPManual = $('#form-tep-manual,#form-linked-tep');
    formTEPManual.find('#email_type').on('change', function () {
        if($(this).val() === 'INPUT') {
            formTEPManual.find('#email-input-field').show();
            formTEPManual.find('#input_email').prop('required', true);
            formTEPManual.find('#email-type-field')
                .addClass('col-md-4')
                .removeClass('col-md-8');
        } else {
            formTEPManual.find('#email-input-field').hide();
            formTEPManual.find('#input_email').prop('required', false);
            formTEPManual.find('#email-type-field')
                .addClass('col-md-8')
                .removeClass('col-md-4');
        }
    });   

    formTEPManual.find("#tep_before").on('change', function(){
        var tep_before = $(this).val();
        if(tep_before == "yes"){
            formTEPManual.find("#tep-reference-field").show();
            formTEPManual.find("#tep_reference").attr("required", true);
            formTEPManual.find('#tep-before-field')
                .addClass('col-md-6')
                .removeClass('col-md-12');
        }else{
            formTEPManual.find("#tep-reference-field").hide();
            formTEPManual.find("#tep_reference").attr("required", false);
            formTEPManual.find('#tep-before-field')
                .addClass('col-md-12')
                .removeClass('col-md-6');
        }
    }); 

    formTEPManual.find("#customer").on('change', function(){
        var id_customer = $(this).val();
        
        selectBooking = formTEPManual.find('#booking');
        selectBooking.empty();
        formTEPManual.find('#aju').data("placeholder", "Fetching data...");
        const params = $.param({
            id_customer: id_customer
        });

        fetch(`${baseUrl}transporter-entry-permit/ajax_get_available_sppb?${params}`)
            .then(result => result.json())
            .then(data => {
                formTEPManual.find('#aju').children('option').remove();
                formTEPManual.find('#aju').append($("<option></option>")
                    .attr("value",'')
                    .text('')); 
                    console.log(data);
                data.forEach(function(data){
                    formTEPManual.find('#aju').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.description)); 
                  });
                if (data == '') {
                    formTEPManual.find('#aju').data("placeholder", "No data...");
                    formTEPManual.find('#aju').select2();
                }
                selectBooking.val(data.length>0?'have':'');
            })
            .catch(err => {
                console.log(err);
                selectBooking.prop("disabled", false);
            });

        // we don't need tep reference in linked tep form
        if ($('#form-linked-tep').length) {
            return;
        }

        //for has tep before
        var id_customer = $(this).val();
        formTEPManual.find('#tep_reference').data("placeholder", "Fetching data...");
        formTEPManual.find('#tep_reference').select2();
        $.ajax({
            url: baseUrl + 'transporter_entry_permit/get_tep_reference_customer',
            type: 'POST',
            data: {
                id_customer: id_customer
            },
            success: function (data) {
                console.log(data);
                formTEPManual.find('#tep_reference').children('option').remove();
                data.forEach(function(data){
                    formTEPManual.find('#tep_reference').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.tep_code+" ("+data.receiver_no_police+"-"+data.receiver_vehicle+")")); 
                  });
                if (data == '') {
                    formTEPManual.find('#tep_reference').data("placeholder", "No data...");
                    formTEPManual.find('#tep_reference').select2();
                }
            },
            error: function() { 
                alert("gagal memperoleh tep reference");
            }  
        });
    });

    if (formTEPManual.find("#customer").val()) {
        // formTEPManual.find("#customer").trigger('change');
    }

    //upload do file
    // let doWrapper = $('#do-wrapper');
    // let btnTEPReq = formTEPReq.find('button[type="submit"]');
    // $('.upload-do').fileupload({
    //     url: baseUrl + 'transporter_entry_permit/upload_s3',
    //     dataType: 'json',
    //     done: function (e, data) {
    //         var inputFileParent = $(this).closest('.form-group');
    //         inputFileParent.find('.text-danger').remove();
    //         $.each(data.result, function (index, file) {
    //             if (file != null && file.status) {
    //                 inputFileParent.find('.uploaded-file')
    //                     .append($('<p/>', {class: 'text-muted text-ellipsis'})
    //                         .html('<a href="#" data-file="' + file.data.file_name + '" class="btn btn-danger btn-sm btn-delete-file">DELETE</a> &nbsp; ' + file.data.client_name));
    //                 inputFileParent.find('.upload-input-wrapper')
    //                     .append($('<input/>', {
    //                         type: 'hidden',
    //                         name: 'memo[]',
    //                         value: file.data.file_name
    //                     }));
    //             } else {
    //                 inputFileParent.find('.progress-bar')
    //                     .addClass('progress-bar-danger')
    //                     .text('Upload failed').css(
    //                     'width', '100%'
    //                 );
    //                 inputFileParent.find('.uploaded-file')
    //                     .append($(file.errors).addClass('text-danger'));
    //             }
    //         });
    //         checkButtonUpload(inputFileParent);
    //         btnTEPReq.attr('disabled',false);
    //     },
    //     progressall: function (e, data) {
    //         var progress = parseInt(data.loaded / data.total * 100, 10);
    //         $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
    //             'width',
    //             progress + '%'
    //         ).text(progress + '%');
    //         btnTEPReq.attr('disabled',true);
    //     },
    //     fail: function (e, data) {
    //         alert(data.textStatus);
    //         btnTEPReq.attr('disabled',false);
    //     }
    // });
    // doWrapper.on('click', '.btn-delete-file', function (e) {
    //     e.preventDefault();
    //     var buttonDelete = $(this);
    //     var file = buttonDelete.data('file');
    //     $.ajax({
    //         url: baseUrl + 'upload_document_file/delete_temp_s3',
    //         type: 'POST',
    //         data: {
    //             file: file
    //         },
    //         accepts: {
    //             text: "application/json"
    //         },
    //         success: function (data) {
    //             if (data.status || data.status == 'true') {
    //                 var inputFileParent = buttonDelete.closest('.form-group');
    //                 inputFileParent.find('input[value="' + file + '"]').remove();
    //                 buttonDelete.parent().remove();
    //                 checkButtonUpload(inputFileParent);
    //                 alert('File ' + file + ' is deleted');
    //             } else {
    //                 alert('Failed delete uploaded file');
    //             }
    //         }
    //     })
    // });

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

    $(document).on('click', '.btn-view-file', function (e) {
        e.preventDefault();
        var idTepReq = $(this).data('id');

        var ajaxUrl = "transporter_entry_permit/ajax_get_tep_req_files";
        var modalViewFile = $('#modal-view-file');
        
        modalViewFile.find("#id_tep_req").val(idTepReq);

        modalViewFile.modal({
            backdrop: 'static',
            keyboard: false
        });

        modalViewFile.find('#file-viewer').html('Fetching File Files...');
        $.ajax({
            type: "GET",
            url: baseUrl + ajaxUrl,
            data: {id_tep_req: idTepReq},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(data);
                modalViewFile.find('#file-viewer').empty();
                if (data.length === 0) {
                    modalViewFile.find('#file-viewer').html('This TEP doesn\'t have DO/Memo');
                }
                $.each(data, function (index, value) {
                    var ext = value.src.split('.').pop().toLowerCase();
                    var source = value.url;
                    if (ext == 'jpg' || ext == 'png' || ext == 'jpeg') {
                        modalViewFile.find('#file-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            }).css('margin', '20px auto')
                        );
                    } else {
                        modalViewFile.find('#file-viewer').append(
                            $('<object>', {
                                width: '100%',
                                height: '500px',
                                type: 'application/pdf'
                            })
                                .attr('data', source)
                                .css('margin', '20px auto')
                                .append($('<a>', {
                                href: source,
                                target: '_blank'
                            }).html('Source file ' + value.file))
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

    formTEPReq.on('submit', function () {

        //check required goods
        const requiredGoods = formTEPReq.find('#table-goods');
        
        let hasGoods = true;
        if (requiredGoods.find('#goods-data').length === 0) {
            hasGoods = false
        };

        if (!hasGoods) {
            alert('Please add goods');
            return false;
        }
        $(this).find('button[type="submit"]').attr('disabled', true).html('saving...');
        return true;
    });

    formSetTEP.on('submit', function () {

        //check upload document
        const requiredDocuments = formSetTEP.find('.required-document');

        let hasDocument = true;
        if (requiredDocuments.length) {
            requiredDocuments.each(function (index, element) {
                if ($(element).find('.upload-input-wrapper').children().length === 0) {
                    hasDocument = false;
                }
            });
        }

        if (!hasDocument) {
            alert('Upload Express Service');
            return false;
        }
        return true;
    });

    $('.upload-express').fileupload({
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
                            name: 'service[]',
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
            $('#btn-submit-create-tep').attr('disabled',false);
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(this).closest('.form-group').find('.progress-bar').removeClass('progress-bar-danger').css(
                'width',
                progress + '%'
            ).text(progress + '%');
            $('#btn-submit-create-tep').attr('disabled',true);
        },
        fail: function (e, data) {
            alert(data.textStatus);
            $('#btn-submit-create-tep').attr('disabled',false);
        }
    });
    expressWrapper.on('click', '.btn-delete-file', function (e) {
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

    $(document).on('click', '.btn-view-express-file', function (e) {
        e.preventDefault();
        var idTepReq = $(this).data('id');

        var ajaxUrl = "transporter_entry_permit/ajax_get_tep_req_express_files";
        var modalViewExpressFile = $('#modal-view-express-file');
        
        modalViewExpressFile.find("#id_tep_req").val(idTepReq);

        modalViewExpressFile.modal({
            backdrop: 'static',
            keyboard: false
        });

        modalViewExpressFile.find('#file-viewer').html('Fetching File Files...');
        $.ajax({
            type: "GET",
            url: baseUrl + ajaxUrl,
            data: {id_tep_req: idTepReq},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(data);
                modalViewExpressFile.find('#file-viewer').empty();
                if (data.length === 0) {
                    modalViewExpressFile.find('#file-viewer').html('This TEP doesn\'t have Express Service File');
                }
                $.each(data, function (index, value) {
                    var ext = value.src.split('.').pop().toLowerCase();
                    var source = value.url;
                    if (ext == 'jpg' || ext == 'png' || ext == 'jpeg') {
                        modalViewExpressFile.find('#file-viewer').append(
                            $('<img>', {
                                class: 'img-responsive',
                                src: source
                            }).css('margin', '20px auto')
                        );
                    } else {
                        modalViewExpressFile.find('#file-viewer').append(
                            $('<object>', {
                                width: '100%',
                                height: '500px',
                                type: 'application/pdf'
                            })
                                .attr('data', source)
                                .css('margin', '20px auto')
                                .append($('<a>', {
                                href: source,
                                target: '_blank'
                            }).html('Source file ' + value.file))
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });
});