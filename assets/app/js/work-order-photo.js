$(function () {
    let tableJobs = $('#table-jobs');
    let tableJob = $('#table-jobs.table-ajax');
    let controlTemplate = $('#control-work-order-template').html();
    let tableReq = $('#table-request');
    var queryString = window.location.search.slice(1);

    // reset filter value
    $(document).on('click', '.btn-reset-filter-tally-history', function () {
        $('.form-filter .select2').val('').trigger("change");
        $('.form-filter input').val('');
    });

    // reset filter lock value
    $(document).on('click', '.btn-reset-filter-lock-tally-history', function () {
        $('.form-filter input').val('');
    });

    
    // modal comfirm locked
    $(document).on('click', '.btn-submit-lock-tally-history', function (e) {
        e.preventDefault();
        let date_from = document.getElementById("date_from_locked").value;
        let date_to = document.getElementById("date_to_locked").value;
        let customer = $("select#customer_locked").val();
        let handling_type_locked = $("select#handling_type_locked").val();
        let no_work_order_locked = $("select#no_work_order_locked").val();
                
        var mydatefrom = new Date(date_from+" 00:00:00");
        var mydateto = new Date(date_to+" 23:59:59");
        
        // if (date_from=='') {
        //     date_from = '08 May 2017';
        //     mydatefrom = new Date("May 8, 2017 00:00:00");
        // }
        // console.log(mydatefrom);
        let modalLocked = $('#modal-confirm-locked-tally');
        modalLocked.find('#print-subtitle').prop('hidden',true);
        if (date_from==''||date_to=='') {
            if (date_from=='' && date_to=='') {
                if (customer!=''||handling_type_locked!=''||no_work_order_locked!='') {
                    modalLocked.find('#print-subtitle').prop('hidden',true);
                    modalLocked.find('#warning').text('');  
                    $('#submit_locked').prop('disabled', false);
                }else{
                    modalLocked.find('#warning').text('Lengkapi field');
                    // document.getElementById("#submit_locked").disabled = true;
                    $('#submit_locked').prop('disabled', true);
                }                
            } else {
                modalLocked.find('#print-subtitle').prop('hidden',false);
                modalLocked.find('#warning').text('Lengkapi semua field date from dan date to');
                // document.getElementById("#submit_locked").disabled = true;
                $('#submit_locked').prop('disabled', true);
            }
        }else if (mydateto<mydatefrom){
            modalLocked.find('#print-subtitle').prop('hidden',false);
            modalLocked.find('#warning').text('Tanggal date from harus lebih kecil dari date to');
            $('#submit_locked').prop('disabled', true);
        }else{
            modalLocked.find('#print-subtitle').prop('hidden',false); 
            modalLocked.find('#warning').text('');
            $('#submit_locked').prop('disabled', false);
        }
        // modalLocked.find("#form-confirm-locked-tally").attr("action", url);
        modalLocked.find('#print-lock').text('Lock');
        modalLocked.find('#submit_locked').text('Lock Now');
        modalLocked.find('#submit_locked').addClass("submit_locked");
        modalLocked.find('#submit_locked').removeClass("submit_unlocked");
        modalLocked.find('#date-from').text(date_from);
        modalLocked.find('#date-to').text(date_to);

        modalLocked.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    //onclick modal lock
    $(document).on('click', '.submit_locked', function () {
        let url = window.location.href.split("?")[0];
        let param = window.location.href.split("?")[1];
        url=url+'/locked_tally';
        // alert (param);
        $('#form-lock-tally-history').find('input[name=url_param]').val(param);
        $('#form-lock-tally-history').attr("action", url);
        $('#form-lock-tally-history').submit();
    });
    //onclick modal unlock
    $(document).on('click', '.submit_unlocked', function () {
        let url = window.location.href.split("?")[0];
        let param = window.location.href.split("?")[1];
        url=url+'/unlocked_tally';
        $('#form-lock-tally-history').find('input[name=url_param]').val(param);
        $('#form-lock-tally-history').attr("action", url);
        $('#form-lock-tally-history').submit();
    });
    // modal comfirm unlocked
    $(document).on('click', '.btn-submit-unlock-tally-history', function (e) {
        e.preventDefault();
        let date_from = document.getElementById("date_from_locked").value;
        let date_to = document.getElementById("date_to_locked").value;
        let customer = $("select#customer_locked").val();
        let handling_type_locked = $("select#handling_type_locked").val();
        let no_work_order_locked = $("select#no_work_order_locked").val();
        let modalLocked = $('#modal-confirm-locked-tally');
        var mydatefrom = new Date(date_from+" 00:00:00");
        var mydateto = new Date(date_to+" 23:59:59");
        // if (date_from=='') {
        //     date_from = '08 May 2017';
        //     mydatefrom = new Date("May 8, 2017 00:00:00");
        // }
        // console.log(mydatefrom);
        modalLocked.find('#print-subtitle').prop('hidden',true);
        if (date_from==''||date_to=='') {
            if (date_from=='' && date_to=='') {
                if (customer!=''||handling_type_locked!=''||no_work_order_locked!='') {
                    modalLocked.find('#print-subtitle').prop('hidden',true);
                    modalLocked.find('#warning').text('Its Okay');  
                    $('#submit_locked').prop('disabled', false);
                }else{
                    modalLocked.find('#warning').text('Lengkapi field');
                    // document.getElementById("#submit_locked").disabled = true;
                    $('#submit_locked').prop('disabled', true);
                }                
            } else {
                modalLocked.find('#print-subtitle').prop('hidden',false);
                modalLocked.find('#warning').text('Lengkapi semua field date from dan date to');
                // document.getElementById("#submit_locked").disabled = true;
                $('#submit_locked').prop('disabled', true);
            }
        }else if (mydateto<mydatefrom){
            modalLocked.find('#print-subtitle').prop('hidden',false);
            modalLocked.find('#warning').text('Tanggal date from harus lebih kecil dari date to');
            $('#submit_locked').prop('disabled', true);
        }else{
            modalLocked.find('#print-subtitle').prop('hidden',false);
            modalLocked.find('#warning').text('');
            $('#submit_locked').prop('disabled', false);
        }
        // modalLocked.find("#form-confirm-locked-tally").attr("action", url);
        modalLocked.find('#print-lock').text('Unlock');
        modalLocked.find('#submit_locked').text('Unlock Now');
        modalLocked.find('#submit_locked').addClass("submit_unlocked");
        modalLocked.find('#submit_locked').removeClass("submit_locked");
        modalLocked.find('#date-from').text(date_from);
        modalLocked.find('#date-to').text(date_to);

        modalLocked.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    // modal comfirm checked
    $(document).on('click', '.btn-checked', function (e) {
        e.preventDefault();

        let modal = $('#modal-checked');
        let id = $(this).data('id');
        let no = $(this).data('no');
        let url = $(this).data('url');
        let id_handling_type = $(this).data('id-handling-type');
        const attachmentPhotoTemplate = $('#attachment-photo-template').html();
        const attachmentDefaultTemplate = $('#attachment-default-template').html();
        let photoWrapperChecker = modal.find('#photo-wrapper');

        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('input[name="no"]').val(no.toString());
        modal.find('.checked-label').html(no.toString());

        modal.find('#photo-wrapper').html('Fetching Attachmant Photo...');
        modal.find('#btn-add-photo').prop('disabled',true);
        modal.find('button[type="submit"]').prop('disabled',true);
        
        
        const params = $.param({
            id_handling_type: id_handling_type,
            condition: 'CHECK',
        });

        fetch(`${baseUrl}handling-type/ajax_get_photo_handling_types?${params}`)
            .then(result => result.json())
            .then(data => {
                console.log(data);
                modal.find('#photo-wrapper').empty();
                modal.find('#btn-add-photo').prop('disabled',false);
                modal.find('button[type="submit"]').prop('disabled',false);
                data.forEach(function(data,i){
                    modal.find('#photo-wrapper').append(
                        attachmentPhotoTemplate
                        .replace(/{{photo_name}}/g, (data.photo_name))
                        .replace(/{{index}}/g, i)
                        );
                });
                if (data == '') {
                    photoWrapperChecker.append(attachmentDefaultTemplate
                    );
                    modal.find('#btn-add-photo').prop('disabled',false);
                    modal.find('button[type="submit"]').prop('disabled',false);
                }
            })
            .catch(err => {
                console.log(err);
            });
            
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    // modal comfirm checked modal
    $(document).on('click', '.btn-submit-checked', function (e) {
        e.preventDefault();
        let modal = $('#modal-checked');
        let formModal = modal.find('form');
        let message = modal.find('textarea[name="message"]').val();
        // let photo_name = modal.find('input[name="photo_name[]"]').map(function () {
        //     return this.value; // $(this).val()
        // }).get();

        let form = $('#form-stock-remain');
        form.find('input[name="message"]').val(message.toString());
        // form.find('input[name="photo_name[]"]').val(photo_name);
        $('span#loader').css('display', 'block');
        if ($(window).width() > 768 ) {
            $('p#loader').text('Loading, please wait...');
        }
        $(this).attr('disabled', true).html("submitted...");
        $("html, body").animate({ scrollTop: 0 }, "slow");
        modal.modal('toggle');
        $.ajax({
            type: "POST",
            url: baseUrl + "tally/checked-job/",
            data: form.serializeArray().concat(formModal.serializeArray()), // serializes the form's elements.
            success: function (data) {
                if(data.status !== true) {
                    $('#tally-check-body').prepend("<div class='alert alert-danger'>" + data.message + "</div>");
                    $('span#loader').css('display', 'none');
                    console.log(data);
                    setTimeout(function(){ /* show the alert for 3sec and then reload the page. */
                        window.location.assign(data.redirect)
                    },3000);
                }else{
                    window.location.assign(data.redirect)
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                // window.location.assign(url)
                console.log("error : "+errorThrown);
            }  
          });
        // $('#form-stock-remain').submit();
    });
    // modal comfirm checked modal
    $(document).on('click', '.btn-submit-approve', function (e) {
        e.preventDefault();
        let modal = $('#modal-approved');
        let message = modal.find('textarea[name="message"]').val();
        console.log('jasd');
        let form = $('#form-stock-remain');
        let url = modal.find('input[name="no"]').val();
        form.attr('action', url);
        form.find('input[name="message"]').val(message.toString());
        $('#form-stock-remain').submit();
    });

    $(document).on('click', '.reload', function (e) {
        e.preventDefault();
        forReload.ajax.reload( null, false ); // user paging is not reset on reload
    });
    var forReload = tableJob.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search job"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'work-order-photo/data?' + queryString,
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'customer_name'},
            {data: 'no_work_order'},
            {
                class: "no-wrap",
                data: 'handling_type'
            },
            {data: 'gate_in_date'},
            {data: 'completed_at'},
            {data: 'description'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['field-job'],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}work-order/view/${full.id}">${data}</a><br>
                    <a href="${baseUrl}booking/view/${full.id_booking}" class="text-muted small">${full.no_reference}</a>
                `;
            }
        }, {
            targets: ['field-handling'],
            render: function (data, type, full) {
                return `
                    ${data}<br>
                    <a href="${baseUrl}handling/view/${full.id_handling}" class="text-muted small">${full.no_handling}</a>
                `;
            }
        }, {
            targets: ['field-gate-date'],
            render: function (data, type, full) {
                return `
                    ${$.trim(data) === '' ? 'Gate In: -' : moment(data).format('D MMMM YYYY HH:mm')}<br>
                    <small class="text-muted">
                        Gate Out: ${$.trim(full.gate_out_date) === '' ? '-' : moment(data).format('D/M/YYYY HH:mm')}
                    </small>                    
                `;
            }
        }, {
            targets: ['field-job-date'],
            render: function (data) {
                return `${$.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY HH:mm')}`;
            }
        }, {
            targets: ['field-status'],
            render: function (data, type, full) {
                let statusLabel = 'default';
                let statusLabelValidated = 'default';
                let status = full.status;
                let validatedLabel = full.status_validation;

                if (full.gate_in_date === '' || full.gate_in_date == null) {
                    status = 'NEED GATE IN';
                }

                if (full.status === 'QUEUED') {
                    statusLabel = 'danger';
                } else if (full.status === 'TAKEN') {
                    statusLabel = 'warning';
                } else if (full.status === 'COMPLETED') {
                    statusLabel = 'success';
                }

                if (full.status_validation === 'VALIDATED') {
                    statusLabelValidated = 'primary';
                } else if (full.status_validation === 'ON REVIEW') {
                    statusLabelValidated = 'warning';
                } else if (full.status_validation === 'CHECKED') {
                    statusLabelValidated = 'warning';
                } else if (full.status_validation === 'APPROVED') {
                    statusLabelValidated = 'info';
                }

                let field = `<span class="label label-${statusLabel}">${status}</span>`;
                if (status === 'COMPLETED') {
                    field += `<br><span class="label label-${statusLabelValidated}">${validatedLabel}</span>`;
                }
                if (status === 'COMPLETED' && full.gate_out_date == null) {
                    field += `<br><a href="${baseUrl}gate/check?code=${full.no_handling}"><span class="label label-danger">Check Out</span></a>`;
                }

                return field
            }
        }, {
            targets: ['field-action'],
            data: 'id',
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_work_order}}/g, full.no_work_order)
                    .replace(/{{customer_name}}/g, full.customer_name)
                    .replace(/{{attachment}}/g, full.attachment)
                    .replace(/{{status}}/g, full.status)
                    .replace(/{{print_total}}/g, full.print_total)
                    .replace(/{{is_locked_disable}}/g, (full.is_locked==1)? 'disabled' : '')
                    .replace(/{{hide_lock}}/g, (full.is_locked==1)? 'hidden' : '')
                    .replace(/{{hide_unlock}}/g, (full.is_locked==1)? '' : 'hidden')
                    .replace(/{{is_locked_tooltip}}/g, full.is_locked == 1 ? 'tooltip' : '')
                    .replace(/{{is_locked_title}}/g, full.is_locked == 1 ? 'Edit is lock' : '')
                    .replace(/{{btn_request}}/g, full.is_locked != 1 ? 'hidden' : '')
                    .replace(/{{unlink}}/g, full.is_locked == 1 ? 'return false;' : '')
                    .replace(/{{print_max}}/g, full.print_max)
                    .replace(/{{is_print}}/g, (full.status=="COMPLETED")? '' : 'disabled')
                    .replace(/{{hidden_print}}/g, (full.status=="COMPLETED")? 'true' : 'false');

                // the trim() matter to prevent parsing empty text node before and after the actual element
                control = $.parseHTML(control.trim());
                const controlEdit = $(control).find('.action-edit');

                //completed date
                var completedDate = moment(full.completed_at,'YYYY-MM-DD');
                var statusCompletedDate = completedDate.isValid();
                if(full.status === 'COMPLETED' && statusCompletedDate == true){
                    var unix_today = moment(moment().format('YYYY-MM-DD')).format('X'); //detik
                    var unix_completed = moment(moment(completedDate).format('YYYY-MM-DD')).format('X'); //detik
                    var unix_tomorrow_completed = moment(moment(completedDate).add(1, 'days').format('YYYY-MM-DD')).format('X'); //detik
                    
                    if(full.multiplier_goods == 1){
                        if(unix_today > unix_tomorrow_completed){
                            if ($(control).find('.action-view-pallet-marking').data('validated-edit') == '') {
                                if(full.print_pallet_total == 0){
                                    if(full.pallet_status == "FREE"){
                                        $(control).find('.action-confirm-pallet-marking').remove();
                                        $(control).find('.action-print-pallet-marking').remove();
                                        $(control).find('.action-view-pallet-marking').remove();
                                    }else if(full.pallet_status == "UNLOCKED"){
                                        $(control).find('.action-open-pallet-marking').remove();
                                        $(control).find('.action-confirm-pallet-marking').remove();
                                    }else{
                                        $(control).find('.action-print-pallet-marking').remove();
                                        $(control).find('.action-open-pallet-marking').remove();
                                        $(control).find('.action-confirm-pallet-marking').remove();
                                    }
                                }else{
                                    $(control).find('.action-open-pallet-marking').remove();
                                    $(control).find('.action-confirm-pallet-marking').remove();
                                }
                            }else{
                                $(control).find('.action-open-pallet-marking').remove();
                                if(full.pallet_status != "REQUESTED"){
                                    $(control).find('.action-confirm-pallet-marking').remove();
                                }
                            }
                        }else{
                            $(control).find('.action-open-pallet-marking').remove();
                            $(control).find('.action-confirm-pallet-marking').remove();
                        }
                    }else{
                        $(control).find('.action-print-pallet-marking').remove();
                        $(control).find('.action-view-pallet-marking').remove();
                        $(control).find('.action-open-pallet-marking').remove();
                        $(control).find('.action-confirm-pallet-marking').remove();
                    }
                    
                }else{
                    if(full.multiplier_goods != 1){
                        $(control).find('.action-print-pallet-marking').remove();
                        $(control).find('.action-view-pallet-marking').remove();
                        $(control).find('.action-open-pallet-marking').remove();
                        $(control).find('.action-confirm-pallet-marking').remove();
                    }else{
                        $(control).find('.action-open-pallet-marking').remove();
                        $(control).find('.action-confirm-pallet-marking').remove();
                    }
                }
                
                if (full.gate_in_date>='2019-12-26 00:00:00' && (full.multiplier_goods=='-1'||full.multiplier_goods=='1')) {
                    if (['PENDING', 'VALIDATED', 'APPROVED', 'CHECKED'].indexOf(full.status_validation) !== -1) {
                        $(control).find('.action-validate').remove();
                    }
                    if (['ON REVIEW', 'VALIDATED', 'PENDING', 'CHECKED'].indexOf(full.status_validation) !== -1) {
                        $(control).find('.action-review').remove();
                    }
                }else{
                    if (['PENDING', 'VALIDATED'].indexOf(full.status_validation) !== -1) {
                        $(control).find('.action-validate').remove();
                    }
                    if (['ON REVIEW', 'VALIDATED'].indexOf(full.status_validation) !== -1) {
                        $(control).find('.action-review').remove();
                    }
                }
                

                if (full.status_validation === 'VALIDATED' || full.status_validation === 'APPROVED') {
                    $(control).find('.divider-validate').remove();
                    if (controlEdit.data('validated-edit') === '') {
                        $(control).find('.action-edit').remove();
                    }
                }
                // if (full.is_locked === 1|| full.is_locked === '1' ) {
                //     $(control).find('.action-edit').css('pointer-events', 'none');
                //     $(control).find('.action-edit').css('cursor', 'default');
                // }

                if (full.status !== 'COMPLETED') {
                    $(control).find('.action-review').remove();
                    $(control).find('.action-validate').remove();
                    $(control).find('.action-edit').remove();
                }

                // $('<div/>').append($(control).clone()).html();
                return control[0].outerHTML;
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' ? '-' : data;
            }
        }]
    });
    
    tableJob.on('click', '.btn-view-pallet-history', function (e) {
        e.preventDefault();

        let idJob = $(this).closest('.row-workorder').data('id');
        let labelJob = $(this).closest('.row-workorder').data('no');
        let urlUpload = $(this).attr('href');
        let modalPalletHistory = $('#modal-pallet-history');

        $.ajax({
            type: 'GET',
            url: baseUrl + 'work_order/pallet_histories',
            data: {
                id_pallet: idJob,
                type: 'form'
            },
            cache : true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
               modalPalletHistory.find('.modal-body').html(data);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });

        
        modalPalletHistory.find('form').attr('action', urlUpload);
        modalPalletHistory.find('input[name=id]').val(idJob);
        modalPalletHistory.find('#job-title').text(labelJob);

        modalPalletHistory.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJob.on('click', '.btn-request-edit', function (e) {
        e.preventDefault();

        let idJob = $(this).closest('.row-workorder').data('id');
        let labelJob = $(this).closest('.row-workorder').data('no');
        let urlUpload = $(this).attr('href');

        let modalRequestEdit = $('#modal-request-edit');
        modalRequestEdit.find('form').attr('action', urlUpload);
        modalRequestEdit.find('input[name=id]').val(idJob);
        modalRequestEdit.find('#job-title').text(labelJob);

        modalRequestEdit.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJob.on('click', '.btn-upload-attachment', function (e) {
        e.preventDefault();

        let idJob = $(this).closest('.row-workorder').data('id');
        let labelJob = $(this).closest('.row-workorder').data('no');
        let labelAttachment = $(this).closest('.row-workorder').data('attachment');
        let urlUpload = $(this).attr('href');

        let modalUploadAttachment = $('#modal-upload-attachment');
        modalUploadAttachment.find('form').attr('action', urlUpload);
        modalUploadAttachment.find('input[name=id]').val(idJob);
        modalUploadAttachment.find('#job-title').text(labelJob);
        modalUploadAttachment.find('#job-attachment').text(labelAttachment);

        modalUploadAttachment.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJobs.on('click', '.btn-print-job-sheet', function (e) {
        e.preventDefault();

        let idWorkOrder = $(this).closest('.row-workorder').data('id');
        let noWorkOrder = $(this).closest('.row-workorder').data('no');
        let printTotal = $(this).closest('.row-workorder').data('print-total');
        let printMax = $(this).closest('.row-workorder').data('print-max');
        let urlPrintJob = $(this).attr('href');

        let modalPrint = $('#modal-confirm-print-job-sheet');
        modalPrint.find('#print-title').text(noWorkOrder.toString());
        modalPrint.find('#print-total').text((printTotal + 1) + 'x');
        modalPrint.find('#print-max').text(printMax + 'x');
        modalPrint.find('input[name=id]').val(idWorkOrder.toString());
        modalPrint.find('form').attr('action', urlPrintJob.toString());

        let buttonSubmitPrint = modalPrint.find('button[type=submit]');
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

    tableReq.on('click', '.approve-request-tally', function (e) {
        e.preventDefault();
        // console.log("as");
        let idLock = $(this).closest('.row-request').data('id');
        let idWorkOrder = $(this).closest('.row-request').data('id-work-order');
        let date_from = $(this).closest('.row-request').data('date-from');
        let date_to = $(this).closest('.row-request').data('date-to');
        let description = $(this).closest('.row-request').data('description');
        let name = $(this).closest('.row-request').data('name');

        let modalApprove = $('#modal-approve-unlock-tally');
        modalApprove.find('input[name=id]').val(idLock.toString());
        modalApprove.find('input[name=id_work_order]').val(idWorkOrder.toString());
        modalApprove.find('input[name=date_from_approve]').val(date_from.toString());
        modalApprove.find('input[name=date_to_approve]').val(date_to.toString());
        modalApprove.find('#name-request').text(name);
        modalApprove.find('#reason-unlock').text(description);

        modalApprove.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableReq.on('click', '.reject-request-tally', function (e) {
        e.preventDefault();
        // console.log("as");
        let idLock = $(this).closest('.row-request').data('id');
        let idWorkOrder = $(this).closest('.row-request').data('id-work-order');
        let noWorkOrder = $(this).closest('.row-request').data('no-work-order');
        let date_from = $(this).closest('.row-request').data('date-from');
        let date_to = $(this).closest('.row-request').data('date-to');

        let modalReject = $('#modal-reject-unlock-tally');
        modalReject.find('input[name=id]').val(idLock.toString());
        modalReject.find('input[name=id_work_order]').val(idWorkOrder.toString());
        modalReject.find('input[name=date_from_reject]').val(date_from.toString());
        modalReject.find('input[name=date_to_reject]').val(date_to.toString());
        modalReject.find('#no-work-order-title').text(noWorkOrder);

        modalReject.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJobs.on('click', '.btn-request-open-pallet', function (e) {
        e.preventDefault();

        let idWorkOrder = $(this).closest('.row-workorder').data('id');

        let modalPrint = $('#modal-request-open-pallet');
        modalPrint.find('input[name=id]').val(idWorkOrder.toString());

        modalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

     tableJobs.on('click', '.btn-unlock-pallet', function (e) {
        e.preventDefault();

        let idWorkOrder = $(this).closest('.row-workorder').data('id');

        let modalPrint = $('#modal-confirm-open-pallet');
        modalPrint.find('input[name=id]').val(idWorkOrder.toString());

        modalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJobs.on('click', '.btn-request-open-tally', function (e) {
        e.preventDefault();

        let idWorkOrder = $(this).closest('.row-workorder').data('id');

        let modalPrint = $('#modal-request-open-tally');
        modalPrint.find('input[name=id]').val(idWorkOrder.toString());

        modalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tableJob.on('click', '.btn-update-max-print', function (e) {
        e.preventDefault();

        let idWorkOrder = $(this).closest('.row-workorder').data('id');
        let labelWorkOrder = $(this).closest('.row-workorder').data('no');
        let labelTotalPrint = $(this).closest('.row-workorder').data('print-total');
        let labelTotalPrintMax = $(this).closest('.row-workorder').data('print-max');
        let urlUpdate = $(this).attr('href');

        let modalUpdateTotalPrint = $('#modal-update-max-print');
        modalUpdateTotalPrint.find('form').attr('action', urlUpdate.toString());
        modalUpdateTotalPrint.find('input[id=id]').val(idWorkOrder.toString());
        modalUpdateTotalPrint.find('#work-order-title').text(labelWorkOrder.toString());
        modalUpdateTotalPrint.find('#work-order-print').text(labelTotalPrint + ' x print');
        modalUpdateTotalPrint.find('#print_max')
            .attr('min', labelTotalPrint.toString())
            .val(labelTotalPrintMax.toString());

        modalUpdateTotalPrint.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    $(document).on('click', '.btn-approved', function (e) {
        e.preventDefault();

        let id = $(this).data('id');
        let no = $(this).data('no');
        let url = $(this).data('url');

        let modal = $('#modal-approved');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('input[name="no"]').val(url.toString());
        modal.find('.approved-label').html(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

});