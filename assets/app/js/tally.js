$(function () {

    let currentWorkOrderHandheldStatus = 'LOCKED';
    const tallyQueueList = $('#tally-queue-list');
    const attachmentPhotoTemplate = $('#attachment-photo-template').html();
    const attachmentDefaultTemplate = $('#attachment-default-template').html();
    var totalPhoto = $('#modal-confirm-tally-check').find('#photo-wrapper').children().length;//modal take
    var totalPhoto2 = $('#modal-confirm-check-tally').find('#photo-wrapper').children().length;//modal complete

    tallyQueueList.on('click', '.btn-take-tally-check', function (e) {
        e.preventDefault();

        currentWorkOrderHandheldStatus = $(this).data('handheld-status');
        if (!checkHandheldStatus()) {
            return;
        }

        let id = $(this).closest('.queue-list').data('id');
        let no = $(this).closest('.queue-list').data('no');
        let url = $(this).data('url');
        let photo = $(this).closest('.queue-list').data('photo');
        let customer = $(this).closest('.queue-list').data('customer');
        let category = $(this).closest('.queue-list').data('category');
        let handling_type = $(this).closest('.queue-list').data('handling-type');
        let id_upload = $(this).closest('.queue-list').data('id-upload');
        let id_handling_type = $(this).closest('.queue-list').data('id-handling-type');
        // console.log(category);

        let modal = $('#modal-confirm-tally-check');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('input[name="id_customer"]').val(customer.toString());
        modal.find('input[name="category"]').val(category.toString());
        modal.find('input[name="handling_type"]').val(handling_type.toString());
        modal.find('input[name="id_upload"]').val(id_upload.toString());
        modal.find('#tally-check-title').text(no.toString());
        modal.find('#transporter').val("").trigger("change");
        modal.find('#plat').val("").trigger("change");
        modal.find('#plat_external').val("").trigger("change");
        modal.find('#armada').val("").trigger("change");
        modal.find('#armada_type').val("").trigger("change");
        modal.find('#armada_description').val("");
        modal.find('#rute_pengiriman').val("");
        // console.log(photo);
        if (photo==0 || photo=='withContainer') {
            modal.find('#photo-add').prop('hidden',true);
            modal.find('.file-upload-info').prop('required',false);
            modal.find('#photo_name_0').prop('required',false);
            modal.find('#photo-wrapper').prop('hidden',true);
        }else {
            modal.find('#photo-add').prop('hidden',false);
            modal.find('.file-upload-info').prop('required',true);
            modal.find('#photo_name_0').prop('required',true);
            modal.find('#photo-wrapper').prop('hidden',false);

            modal.find('#photo-wrapper').html('Fetching Attachmant Photo...');
            modal.find('#btn-add-photo').prop('disabled',true);
            modal.find('button[type="submit"]').prop('disabled',true);
            
            const params = $.param({
                id_handling_type: id_handling_type,
                condition: 'TAKE',
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
                        photoWrapper.append(attachmentDefaultTemplate
                        );
                        modal.find('#btn-add-photo').prop('disabled',false);
                        modal.find('button[type="submit"]').prop('disabled',false);
                    }
                    totalPhoto = photoWrapper.children().length;
                    console.log(photoWrapper.children().length);
                })
                .catch(err => {
                    console.log(err);
                });
        }
        if (handling_type=='LOAD' || handling_type=='EMPTY CONTAINER') {
            modal.find('#transporter').prop('required',true);
            modal.find('#armada').prop('required',true);
            modal.find('#transporter-wrapper').prop('hidden',false);
        } else {
            modal.find('#transporter').prop('required',false);
            modal.find('#armada').prop('required',false);
            modal.find('#transporter-wrapper').prop('hidden',true);
        }

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    $('select[name=transporter]').change(function() { 
        // alert($(this).val()); 
        let modal =$(this).closest('#modal-confirm-tally-check');
        let id_customer = modal.find('input[name="id_customer"]').val();
        let category = modal.find('input[name="category"]').val();
        let handling_type = modal.find('input[name="handling_type"]').val();
        let id_upload = modal.find('input[name="id_upload"]').val();
        let internalWrapper = modal.find('#internal-expedition-wrapper');
        let externalWrapper = modal.find('#external-expedition-wrapper');
        let ruteWrapper = modal.find('#rute-pengiriman-wrapper');
        let expeditionWrapper = modal.find('#expedition-wrapper');
        
        expeditionWrapper.find('#expedition option[value=""]').prop('selected',true).change();
        ruteWrapper.hide();
        ruteWrapper.find('#rute_pengiriman').prop('required',false);
        if ($(this).val()=='internal') {
            internalWrapper.find('#plat').data("placeholder", "Fetching data...");
            internalWrapper.find('#plat_external').select2();
            $.ajax({
                url: baseUrl + 'tally/get_plat',
                type: 'POST',
                data: {
                    jenis: 'internal'
                },
                success: function (data) {
                    console.log(data);
                    internalWrapper.find('#plat').children('option').remove();
                    data.forEach(function(data){
                        console.log(data._disabled);
                        internalWrapper.find('#plat').append($("<option></option>", {
                            disabled: data._disabled,
                            "data-toggle": 'tooltip',
                            title: data._disabled ? 'Used in safe conduct ' + data._disabled_data : ''
                        })
                        .attr("value",data.no_plate+","+data.id)
                        .text(data.vehicle_name+" - "+data.vehicle_type+" ("+data.no_plate+")")); 
                      });
                    if (data == '') {
                        internalWrapper.find('#plat').prop("placeholder", "No data...");
                    }
                },
                error: function() { 
                    alert("gagal memperoleh no police");
                }  
            });
            internalWrapper.show();
            externalWrapper.hide();
            internalWrapper.find('#plat').prop('required',true);
            externalWrapper.find('#plat_external').prop('required',false);
            internalWrapper.find('#plat_label').html('Plat Internal');
            ruteWrapper.show();
            ruteWrapper.find('#rute_pengiriman').prop('required',true);
        } else if ($(this).val()=='external') {
            externalWrapper.find('#plat_external').data("placeholder", "Fetching data...");
            externalWrapper.find('#plat_external').select2();
            $.ajax({
                url: baseUrl + 'tally/get_plat',
                type: 'POST',
                data: {
                    jenis: 'external',
                    id_customer: id_customer,
                    category:category,
                    handling_type:handling_type,
                    id_upload : id_upload,
                },
                success: function (data) {
                    console.log(data);
                    externalWrapper.find('#plat_external').children('option').remove();
                    externalWrapper.find('#plat_external').data("placeholder", "Select external");
                    externalWrapper.find('#plat_external').append($('<option>', {value: ''}).text(''));
                    externalWrapper.find('#plat_external').append(
                        $('<option>', {value: '0'}).text('UNKNOWN TEP (Select Chassis)')
                    );
                    data.forEach(function(data){
                        externalWrapper.find('#plat_external').append($("<option></option>")
                        .attr("value",data.receiver_no_police+","+data.id)
                        .text(data.receiver_vehicle+" ("+data.receiver_no_police+")")); 
                      });
                    if (data == '') {
                        externalWrapper.find('#plat_external').data("placeholder", "No data...");
                    }
                    externalWrapper.find('#plat_external').select2();
                },
                error: function() { 
                    alert("gagal memperoleh no police");
                }  
            });
            externalWrapper.show();
            internalWrapper.hide();
            internalWrapper.find('#plat').prop('required',false);
            externalWrapper.find('#plat_external').prop('required',true);
            externalWrapper.find('#plat_label').html('Plat External');
        }
    });
    $('select[name=plat_external]').change(function() { 
        const expeditionWrapper = modal.find('#expedition-wrapper');
        const chassisWrapper = modal.find('#chassis-wrapper');

        if ($(this).val() != 0) {
            expeditionWrapper.show();
            expeditionWrapper.find('#expedition').prop('required',true);
        } else {
            expeditionWrapper.hide();
            expeditionWrapper.find('#expedition').prop('required',false);
            expeditionWrapper.find('#expedition option[value=""]').prop('selected',true).change();
        }

        // unknown tep should pick chassis
        if ($(this).val() === '' || $(this).val() !== '0') {
            chassisWrapper.hide();
            chassisWrapper.find('#chassis').prop('required', false);
        } else {
            chassisWrapper.show();
            chassisWrapper.find('#chassis').prop('required', true);
        }
    });
    $('select[name=armada]').change(function() { 
        // alert($(this).val()); 
        let modal =$(this).closest('#modal-confirm-tally-check');
        let armadaTypeWrapper = modal.find('#armada-type-wrapper');
        let armadaDescriptionTypeWrapper = modal.find('#armada-description-wrapper');
        armadaTypeWrapper.hide();
        armadaDescriptionTypeWrapper.hide();
        armadaTypeWrapper.find('#armada_type').prop('required',false);
        armadaDescriptionTypeWrapper.find('#armada_description').prop('required',false);

        if ($(this).val()=='8' || $(this).val()=='9') {
            armadaTypeWrapper.show();
            armadaTypeWrapper.find('#armada_type').prop('required',true);
        }
        if ($(this).val()=='13') {
            armadaDescriptionTypeWrapper.show();
            armadaDescriptionTypeWrapper.find('#armada_description').prop('required',true);
        }
    });
    const photoTemplate = $('#photo-template').html();
    const modal = $('#modal-confirm-tally-check');
    const btnAddPhoto = modal.find('#btn-add-photo');
    let photoWrapper = modal.find('#photo-wrapper');


    btnAddPhoto.on('click', function (e) {
        e.preventDefault();

        photoWrapper.append(
            photoTemplate
                .replace(/{{no}}/g, (totalPhoto + 1))
                .replace(/{{index}}/g, totalPhoto)
        );

        totalPhoto++;

        initUploadPhoto($('.upload-photo'));
        checkHandheldStatus();
    });
    photoWrapper.on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();

        var btnSubmit = $(this).closest('form');
        btnSubmit.find(':submit').attr('disabled', false);

        totalPhoto--;

        $(this).closest('.card-photo').remove();

    });

    tallyQueueList.on('click', '.btn-complete-tally', function (e) {
        e.preventDefault();

         currentWorkOrderHandheldStatus = $(this).data('handheld-status');
         if (!checkHandheldStatus()) {
             return;
         }

        let id = $(this).closest('.queue-list').data('id');
        let no = $(this).closest('.queue-list').data('no');
        let photo = $(this).closest('.queue-list').data('photo');
        let url = $(this).data('url');
        let customer = $(this).closest('.queue-list').data('customer');
        let handling_type = $(this).closest('.queue-list').data('handling-type');
        let container = $(this).closest('.queue-list').data('container');
        let category = $(this).closest('.queue-list').data('category');
        let id_handling_type = $(this).closest('.queue-list').data('id-handling-type');
        $('.btn-complete-tally').attr('disabled', true).html('Checking...');

        tallyQueueList.find('.alert-complete-validation').remove();
        $.ajax({
            url: baseUrl + 'tally/complete_job_checking',
            type: 'POST',
            data: {
                workOrderId: id,
                type: 'form'
            },
            success: function (data) {
                if(data.status !== true) {
                    // console.log(data);
                    // console.log("gagal eror");
                    // window.location.assign(url);
                    tallyQueueList.prepend("<div class='alert alert-danger'>" + data.message + "</div>");
                    $('.btn-complete-tally').attr('disabled', false).html('COMPLETE &nbsp; <i class="fa fa-check"></i>');
                }else{
                    $('.btn-complete-tally').attr('disabled', false).html('COMPLETE &nbsp; <i class="fa fa-check"></i>');
                    let modalConfirmCheckTally = $('#modal-confirm-check-tally');
                    const tallyWrapper = modalConfirmCheckTally.find('#tally-wrapper');
                    const spaceWrapper = tallyWrapper.find('#space-wrapper');
                    modalConfirmCheckTally.find('form').attr('action', url.toString());
                    modalConfirmCheckTally.find('#job-title').text(no.toString());
                    modalConfirmCheckTally.find('input[name="id"]').val(id.toString());
                    modalConfirmCheckTally.find('input[name="id_customer"]').val(customer.toString());
                    modalConfirmCheckTally.find('input[name="category"]').val(category.toString());
                    
                    if (handling_type=='STRIPPING' || (handling_type=='UNLOAD' && container == '') || handling_type=='LOAD' || handling_type=='ADD UNPACKAGE') {
                        spaceWrapper.show();
                        modalConfirmCheckTally.find('#space').prop('required',true);
                    }else{
                        spaceWrapper.hide();
                        modalConfirmCheckTally.find('#space').val('');
                        modalConfirmCheckTally.find('#space').prop('required',false);
                    }

                    // console.log(photo);
                    if (photo==0 || photo=='withContainer') {
                        modalConfirmCheckTally.find('#photo-add').prop('hidden',true);
                        modalConfirmCheckTally.find('.file-upload-info').prop('required',false);
                        modalConfirmCheckTally.find('#photo_name_0').prop('required',false);
                        modalConfirmCheckTally.find('#photo-wrapper').prop('hidden',true);
                        if (handling_type!='ADD UNPACKAGE') {
                            modalConfirmCheckTally.find('#tally-wrapper').prop('hidden',true);
                        }
                        modalConfirmCheckTally.find('#activity_type').prop('required',false);
                        modalConfirmCheckTally.find('#resources').prop('required',false);
                    }else {
                        modalConfirmCheckTally.find('#photo-add').prop('hidden',false);
                        modalConfirmCheckTally.find('.file-upload-info').prop('required',true);
                        modalConfirmCheckTally.find('#photo_name_0').prop('required',true);
                        modalConfirmCheckTally.find('#photo-wrapper').prop('hidden',false);
                        modalConfirmCheckTally.find('#tally-wrapper').prop('hidden',false);
                        modalConfirmCheckTally.find('#activity_type').prop('required',true);
                        modalConfirmCheckTally.find('#resources').prop('required',true);

                        modalConfirmCheckTally.find('#photo-wrapper').html('Fetching Attachmant Photo...');
                        modalConfirmCheckTally.find('#btn-add-photo').prop('disabled',true);
                        modalConfirmCheckTally.find('button[type="submit"]').prop('disabled',true);
                        
                        const params = $.param({
                            id_handling_type: id_handling_type,
                            condition: 'COMPLETED',
                        });

                        fetch(`${baseUrl}handling-type/ajax_get_photo_handling_types?${params}`)
                            .then(result => result.json())
                            .then(data => {
                                console.log(data);
                                modalConfirmCheckTally.find('#photo-wrapper').empty();
                                modalConfirmCheckTally.find('#btn-add-photo').prop('disabled',false);
                                modalConfirmCheckTally.find('button[type="submit"]').prop('disabled',false);
                                data.forEach(function(data,i){
                                    modalConfirmCheckTally.find('#photo-wrapper').append(
                                        attachmentPhotoTemplate
                                        .replace(/{{photo_name}}/g, (data.photo_name))
                                        .replace(/{{index}}/g, i)
                                        );
                                });
                                if (data == '') {
                                    photoWrapper2.append(attachmentDefaultTemplate
                                    );
                                    modalConfirmCheckTally.find('#btn-add-photo').prop('disabled',false);
                                    modalConfirmCheckTally.find('button[type="submit"]').prop('disabled',false);
                                }
                                totalPhoto2 = photoWrapper2.children().length;
                                console.log(photoWrapper2.children().length);
                            })
                            .catch(err => {
                                console.log(err);
                            });
                            
                        $.ajax({
                            url: baseUrl + 'tally/get_labours',
                            type: 'POST',
                            success: function (data) {
                                console.log(data);
                                selectLabours.children('option').remove();
                                
                                data.forEach(function(data){
                                    selectLabours.append($("<option></option>")
                                        .attr("value",data.id)    
                                        .text(data.nama_visitor)); 
                                });
                            },
                            error: function() { 
                                alert("gagal labours");
                            }  
                        });
                        $.ajax({
                            url: baseUrl + 'tally/get_vas_and_resources',
                            type: 'POST',
                            data: {
                                id: id
                            },
                            success: function (data) {
                                console.log(data);
                                const vasWrapper = tallyWrapper.find('#vas-wrapper');
                                const resourcesWrapper = tallyWrapper.find('#resources-wrapper');
                                tallyWrapper.find('#vas-wrapper').hide();
                                tallyWrapper.find('#resources-wrapper').hide();
                                vasWrapper.find('#activity_type').prop("required",false);
                                resourcesWrapper.find('#resources').prop("required",false);
                                selectActivityType.children('option').remove();
                                selectResources.children('option').remove();
                                checkboxResources.children('div').remove();
                                selectResources.append($("<option></option>")
                                        .attr("value","")    
                                        .text("")); 
                                data.forEach(function(data){
                                    if (data.component_category == "VALUE ADDITIONAL SERVICES") {
                                        tallyWrapper.find('#vas-wrapper').show();
                                        vasWrapper.find('#activity_type').prop("required",true);
                                        selectActivityType.append($("<option></option>")
                                        .attr("value",data.id)    
                                        .text(data.handling_component)); 
                                    }
                                    if (data.handling_component == "Forklift") {
                                        tallyWrapper.find('#resources-wrapper').show();
                                        checkboxResources.append(
                                            "<div class='col-sm-4'><div class='checkbox' style='margin-top: 0'><label><input type='checkbox' name='resources[]' id='resources_"+data.id+"' value='"+data.id+"' data-component='"+data.handling_component+"'>&nbsp; "+"Heavy equipment type"+"</label></div></div>")
                                        resourcesWrapper.find('#resources').prop("required",true);
                                    }
                                    if (data.component_category == "RESOURCES") {
                                        tallyWrapper.find('#resources-wrapper').show();
                                        checkboxResources.append(
                                            "<div class='col-sm-4'><div class='checkbox' style='margin-top: 0'><label><input type='checkbox' name='resources[]' id='resources_"+data.id+"' value='"+data.id+"' data-component='"+data.handling_component+"'>&nbsp; "+data.handling_component+"</label></div></div>")
                                        resourcesWrapper.find('#resources').prop("required",true);
                                    }
                                });
                                $('input').iCheck({
                                    checkboxClass: 'icheckbox_square-blue',
                                    radioClass: 'iradio_square-blue',
                                    increaseArea: '20%'
                                });
                                $('.checkbox').on('ifChanged', function (e) {
                                    e.preventDefault();
                                    var resources = $(this).find("input[type='checkbox']").val();
                                    var resourcesName = $(this).find("input[type='checkbox']").data('component');
                                    // console.log(resourcesName);
                                    if ($(this).find("input[type='checkbox']").is(':checked')) {
                                        if (resourcesName == "Forklift") {
                                            forkliftWrapper.show();
                                            forkliftWrapper.find("#forklift").prop("required",true);
                                            forkliftWrapper.find("#operator_name").prop("required",true);
                                            // forkliftWrapper.find("#is_owned").prop("required",true);
                                            forkliftWrapper.find("#capacity").prop("required",true);
                                            $('select[id=heavy_equipment]').trigger('change');
                                        }
                                        if (resourcesName == 'Labours') {
                                            laboursWrapper.show();
                                            laboursWrapper.find("#labours").prop("required",true);
                                        }
                                        if (resourcesName == 'Pallet'){
                                            palletWrapper.show();
                                            palletWrapper.find("#pallet").prop("required",true);
                                        }
                                    }else{
                                        if (resourcesName == 'Forklift') {
                                            forkliftWrapper.hide();
                                            forkliftWrapper.find("#forklift").prop("required",false);
                                            forkliftWrapper.find("#operator_name").prop("required",false);
                                            // forkliftWrapper.find("#is_owned").prop("required",false);
                                            forkliftWrapper.find("#capacity").prop("required",false);
                                        }
                                        if (resourcesName == 'Labours') {
                                            laboursWrapper.hide();
                                            laboursWrapper.find("#labours").prop("required",false);
                                        }
                                        if (resourcesName == 'Pallet'){
                                            palletWrapper.hide();
                                            palletWrapper.find("#pallet").prop("required",false);
                                        }
                                    }
                                });
                            },
                            error: function() { 
                                alert("gagal memperoleh vas and resources");
                            }  
                        });
                    }

                    modalConfirmCheckTally.modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                // window.location.assign(url)
                console.log("gagal");
            }  
        });
        
        
    });
    const photoTemplate2 = $('#photo-template').html();
    const modal2 = $('#modal-confirm-check-tally');
    const btnAddPhoto2 = modal2.find('#btn-add-photo');
    const selectActivityType = modal2.find('#activity_type');
    const selectResources = modal2.find('#resources');
    const descriptionVasWrapper = modal2.find('#description-vas-wrapper');
    const forkliftWrapper = modal2.find('#forklift-wrapper');
    const laboursWrapper = modal2.find('#labours-wrapper');
    const palletWrapper = modal2.find('#pallet-wrapper');
    const checkboxResources = modal2.find('#checkbox-resources');
    const selectLabours = modal2.find('#labours');
    const inputSpace = modal2.find('#space');
    const inputPallet = modal2.find('#pallet');
    let photoWrapper2 = modal2.find('#photo-wrapper');
    
    checkboxResources.find('.checkbox').on('ifChanged', function (e) {
        e.preventDefault();
        var resources = $(this).find("input[type='checkbox']").val();
        var resourcesName = $(this).find("input[type='checkbox']").data('component');
        // console.log(resourcesName);
        if ($(this).find("input[type='checkbox']").is(':checked')) {
            if (resourcesName == "Forklift") {
                forkliftWrapper.show();
                $('select[id=heavy_equipment]').trigger('change');
            }
            if (resourcesName == 'Labours') {
                laboursWrapper.show();
            }
            if (resourcesName == 'Pallet'){
                palletWrapper.show();
            }
        }else{
            if (resourcesName == 'Forklift') {
                forkliftWrapper.hide();
            }
            if (resourcesName == 'Labours') {
                laboursWrapper.hide();
            }
            if (resourcesName == 'Pallet'){
                palletWrapper.hide();
            }
        }
    });
    checkboxResources.find('.checkbox').trigger('ifChanged');
    function checkInternal(pilihan) {
        return pilihan == 'INTERNAL';
    }
    function checkExternal(pilihan) {
        return pilihan == 'EXTERNAL';
    }
    $('select[id=heavy_equipment]').change(function(e) { 
        e.preventDefault();
        var pilihan = $(this).val();
        console.log(pilihan);
        forkliftWrapper.find('#forklift-in-wrapper').hide();
        forkliftWrapper.find('#forklift-ex-wrapper').hide();
        forkliftWrapper.find("#forklift").prop("required",false);
        forkliftWrapper.find("#forklift_external").prop("required",false);
        if (pilihan.find(checkInternal)=='INTERNAL' ) {
            forkliftWrapper.find('#forklift-in-wrapper').show();
            forkliftWrapper.find("#forklift").prop("required",true);
            if ( forkliftWrapper.find("#forklift").find('option:selected').length == 0) {
                forkliftWrapper.find('#forklift').data("placeholder", "Fetching data...");
                forkliftWrapper.find('#forklift').select2();
                $.ajax({
                    url: baseUrl + 'tally/get_heavy_equipment',
                    type: 'POST',
                    data: {
                        jenis: 'internal'
                    },
                    success: function (data) {
                        console.log(data);
                        forkliftWrapper.find('#forklift').children('option').remove();
                        data.forEach(function(data){
                            forkliftWrapper.find('#forklift').append($("<option></option>")
                            .attr("value",data.id)
                            .text(data.name)); 
                          });
                        if (data == '') {
                            forkliftWrapper.find('#forklift').data("placeholder", "No data...");
                            forkliftWrapper.find('#forklift').select2();
                        }else{
                            forkliftWrapper.find('#forklift').data("placeholder", "Select Forklift");
                            forkliftWrapper.find('#forklift').select2();
                        }
                    },
                    error: function() { 
                        alert("gagal forklift");
                    }  
                });  
            }
        }else{
            forkliftWrapper.find('#forklift').children('option').remove();
        }
        if(pilihan.find(checkExternal)=='EXTERNAL') {
            forkliftWrapper.find('#forklift-ex-wrapper').show();
            forkliftWrapper.find("#forklift_external").prop("required",true);
            if ( forkliftWrapper.find("#forklift_external").find('option:selected').length == 0) {
                forkliftWrapper.find('#forklift_external').data("placeholder", "Fetching data...");
                forkliftWrapper.find('#forklift_external').select2();
                $.ajax({
                    url: baseUrl + 'tally/get_heavy_equipment',
                    type: 'POST',
                    data: {
                        jenis: 'external'
                    },
                    success: function (data) {
                        console.log(data);
                        forkliftWrapper.find('#forklift_external').children('option').remove();
                        data.forEach(function(data){
                            forkliftWrapper.find('#forklift_external').append($("<option></option>")
                            .attr("value",data.id)
                            .text(data.heep_code)); 
                          });
                        if (data == '') {
                            forkliftWrapper.find('#forklift_external').data("placeholder", "No data...");
                            forkliftWrapper.find('#forklift_external').select2();
                        }else{
                            forkliftWrapper.find('#forklift_external').data("placeholder", "Select Forklift");
                            forkliftWrapper.find('#forklift_external').select2();
                        }
                    },
                    error: function() { 
                        alert("gagal forklift");
                    }  
                });
            } 
        }else{
            forkliftWrapper.find('#forklift_external').children('option').remove();
        }
    });
    btnAddPhoto2.on('click', function (e) {
        e.preventDefault();

        photoWrapper2.append(
            photoTemplate2
                .replace(/{{no}}/g, (totalPhoto2 + 1))
                .replace(/{{index}}/g, totalPhoto2)
        );

        totalPhoto2++;

        initUploadPhoto($('.upload-photo'));
        checkHandheldStatus();
    });

    // inputSpace.on('keyup', function (e) {
    //     e.preventDefault();
        
    //     var id_customer = modal2.find('input[name="id_customer"]').val();
    //     $.ajax({
    //         url: baseUrl + 'tally/ajax_get_used_space',
    //         type: 'POST',
    //         data: {
    //             id_customer : id_customer,
    //         },
    //         success: function (data) {
    //             inputSpace.prop('max',data);
    //         },
    //         error: function() { 
    //             alert("fail get used space");
    //         }  
    //     });
    // });

    inputPallet.on('keyup', function (e) {
        e.preventDefault();
        let category = modal2.find('input[name="category"]').val();
        console.log(category);
        $.ajax({
            url: baseUrl + 'tally/ajax_get_used_pallet',
            type: 'POST',
            data: {
            },
            success: function (data) {
                if (category=="OUTBOUND") {
                    inputPallet.removeProp("max");
                }else{
                    inputPallet.prop('max',data);
                }
            },
            error: function() { 
                alert("fail get used pallet");
            }  
        });
    });

    selectActivityType.on('change', function (e) {
        e.preventDefault();
        descriptionVasWrapper.hide();
        descriptionVasWrapper.find("#description_vas").prop("required",false);
        // var activity_type_name = $(this).find('option:selected').data('component');
        var multiple = $(this).val();
        if (multiple.includes("13")) {
            descriptionVasWrapper.show();
            descriptionVasWrapper.find("#description_vas").prop("required",true);
        }
    });
    selectActivityType.trigger('change');
    
    selectResources.on('change', function (e) {
        e.preventDefault();
        var resources = $(this).find('option:selected').val();
        forkliftWrapper.hide();
        laboursWrapper.hide();
        forkliftWrapper.find("#forklift").prop("required",false);
        forkliftWrapper.find("#operator_name").prop("required",false);
        // forkliftWrapper.find("#is_owned").prop("required",false);
        forkliftWrapper.find("#capacity").prop("required",false);
        laboursWrapper.find("#labours").prop("required",false);
        if (resources==0) {
            forkliftWrapper.show();
            laboursWrapper.show();
            forkliftWrapper.find("#forklift").prop("required",true);
            forkliftWrapper.find("#operator_name").prop("required",true);
            // forkliftWrapper.find("#is_owned").prop("required",true);
            forkliftWrapper.find("#capacity").prop("required",true);
            laboursWrapper.find("#labours").prop("required",true);
        }
        if (resources==1) {
            forkliftWrapper.show();
            forkliftWrapper.find("#forklift").prop("required",true);
            forkliftWrapper.find("#operator_name").prop("required",true);
            // forkliftWrapper.find("#is_owned").prop("required",true);
            forkliftWrapper.find("#capacity").prop("required",true);
        }
        if (resources==2) {
            laboursWrapper.show();
            laboursWrapper.find("#labours").prop("required",true);
        }
    });
    

    photoWrapper2.on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();

        var btnSubmit = $(this).closest('form');
        btnSubmit.find(':submit').attr('disabled', false);

        totalPhoto2--;

        $(this).closest('.card-photo').remove();

    });
    
     
    // $('#btn-complete-tally').on('click', function (e) {
    //     e.preventDefault();

    //     let modalConfirmCheckTally = $('#modal-confirm-check-tally');
    //     modalConfirmCheckTally.find('#job-title').text($(this).data('no').toString());
    //     modalConfirmCheckTally.find('.btn-save-check').on('click', function () {
    //         modalConfirmCheckTally.find('button').prop('disabled', true);
    //         $('#form-tally-check').submit();
    //     });

    //     modalConfirmCheckTally.modal({
    //         backdrop: 'static',
    //         keyboard: false
    //     });
    // });
    // uploadDocument();

    let fileFromCapture = null;
    initUploadPhoto($('.upload-photo'));

    function initUploadPhoto(input) {
        var btnSubmit = $(input).closest('form').find(':submit');
        input.fileupload({
            url: baseUrl + 'upload_document_file/upload_s3',
            dataType: 'json',
            add: function (e, data) {
                btnSubmit.attr('disabled', true);
                console.log(input.closest('form'));
                if (fileFromCapture) {
                    data.files = [fileFromCapture];
                }
                data.submit();
            },
            done: function (e, data) {
                console.log('jalan1')
                var inputFileParent = $(this).closest('.form-group');
                inputFileParent.find('.text-danger').remove();
                inputFileParent.find('.btn-simple-upload').attr('disabled', true);
                $.each(data.result, function (index, file) {
                    if (file != null && file.status) {
                        // console.log(file.data[0].file_name);
                        inputFileParent.find('.uploaded-file')
                            .append($('<p/>', {class: 'text-muted text-ellipsis'})
                                .html('<a href="#" data-file="' + file.data.file_name + '" class="btn btn-danger btn-sm btn-delete-file">DELETE</a> &nbsp; ' + file.data.client_name));
                        inputFileParent.find('.upload-input-wrapper')
                            .append($('<input/>', {
                                type: 'hidden',
                                name: index + '_name[]',
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
                if (progress==100) {
                    btnSubmit.attr('disabled', false);
                }
            },
            fail: function (e, data) {
                alert(data.textStatus);
            }
        });
    }

    function checkButtonUpload(wrapper) {
        if (wrapper.find('.upload-input-wrapper').children().length) {
            console.log('check');
            wrapper.find('.button-file').attr('disabled', true);
        } else {
            console.log('check2');
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
        var inputFileParent = $(this).closest('.form-group');
        inputFileParent.find('.btn-simple-upload').attr('disabled', false);
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

    $(document).on('click', '.btn-photo-picker', function (e) {
        e.preventDefault();
        const inputFile = $(this).closest('.form-group').find('[type=file]');
        fileFromCapture = null;
        currentUploadFile = null;
        openModalTakePhoto(function(blob, base64, modal) {
            const file = new File([blob], "image.jpg", {lastModified: new Date().getTime()});
            fileFromCapture = file;
            // force initialize without open the dialog file picker
            initUploadPhoto(inputFile);
            //inputFile.trigger('change');
            inputFile.fileupload('add', {files: [file]}); // manually passing File API rather from input
            //inputFile.trigger('fileuploadadd');
            $(modal).modal('hide');
        }, function(modal) {
            inputFile.click();
            $(modal).modal('hide');
        });
    });

    $('#btn-release-tally-check').on('click', function (e) {
        e.preventDefault();

        let id = $(this).data('id');
        let no = $(this).data('no');
        let url = $(this).attr('href');

        let modal = $('#modal-release-tally-check');
        modal.find('form').attr('action', url.toString());
        modal.find('input[id]').val(id.toString());
        modal.find('#tally-check-title').text(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    let modalGeneratePallet = $('#modal-confirm-generate-pallet');
    $('#btn-generate-pallet').on('click', function (e) {
        e.preventDefault();

        modalGeneratePallet.find('#booking-title').text($(this).data('booking').toString());
        modalGeneratePallet.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    modalGeneratePallet.find('.btn-confirm-generate-pallet').on('click', function () {
        let buttonGenerate = $(this);
        buttonGenerate.attr('disabled', true);

        let fields = $('#table-detail-item').find('[name="goods[no_pallets][]"]');
        if (!fields.length) {
            fields = $('.field-pallet');
        }
        $.post(baseUrl + 'work-order/ajax_generate_pallet', {total: fields.length})
            .done(function (data) {
                if (data === false) {
                    alert('Generate pallet failed');
                } else {
                    fields.each(function (index, field) {
                        $(field).val(data[index]);
                    });
                }
            })
            .fail(function () {
                alert("Something went wrong");
            })
            .always(function () {
                buttonGenerate.prop('disabled', false);
                modalGeneratePallet.modal('hide');
            });
    });

    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    const isOperaVariant = navigator.userAgent.match(/OPR/i) || navigator.userAgent.match(/OPT/i);
    const isProperDevice1 = (window.screen.width === 534 && window.screen.height === 854) || (window.screen.height === 534 && window.screen.width === 854);
    const isProperDevice2 = (window.screen.width === 601 && window.screen.height === 962) || (window.screen.height === 601 && window.screen.width === 962);

    function isUsingHandheld() {
        return isMobile.any() && isChrome && !isOperaVariant;
    }

    function blockInvalidHandheldPage() {
        $('body').html(`
            <div class="mobile-only-info" style="background: rgba(255, 255, 255, .9); padding: 10px; position: fixed; z-index: 9999; width: 100%; height: 100%; top: 0; left: 0; text-align: center; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold">
                <div>
                    THIS PAGE MUST BE ACCESSED BY <b>THE COMPANY MOBILE DEVICE AND WITH CHROME BROWSER</b><br>
                    <small class="text-muted">Except you have "tally validated edit" permission or "unlocked handheld" job</small><br>
                    <button class="btn btn-primary mt10" onClick="window.location.reload();">Refresh Page</button>
                </div>
            </div>
        `);
    }

    // check handheld status before proceed
    function checkHandheldStatus() {
        $('input[type="file"]').prop('capture', 'camera');
        const statusHandheld = currentWorkOrderHandheldStatus || 'LOCKED';
        if (statusHandheld === 'LOCKED') {
            $('#btn-browse-photo').hide();
        } else {
            $('#btn-browse-photo').show();
        }
        if (statusHandheld === 'LOCKED' && !isUsingHandheld()) {
            blockInvalidHandheldPage();
            return false;
        } else {
            // allow browse image from directory
            if (statusHandheld === 'UNLOCKED') {
                $('input[type="file"]').removeAttr('capture');
            }
        }
        return true;
    }

    const formTallyCheck = $('#form-tally-check');
    if (formTallyCheck.length) {
        currentWorkOrderHandheldStatus = formTallyCheck.data('handheld-status');
        checkHandheldStatus();
    }

    //if(!isMobile.any() || !isChrome || isOperaVariant /*||(window.screen.width )!='801'||(window.screen.height )!='1281'*/) {
    //    if(!$('#tally-validated-edit[value=1]').length) {
    //        $('body').html('<div class="mobile-only-info" style="background: rgba(255, 255, 255, .9); padding: 10px; position: fixed; z-index: 9999; width: 100%; height: 100%; top: 0; left: 0; text-align: center; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold"><div>THIS PAGE MUST BE ACCESSED BY THE MOBILE DEVICE (CHROME BROWSER)<br><small class="text-muted">Except you have "tally validated edit" permission</small></div></div>');
    //    }
    //}

    modal.on('keyup','.file-upload-info', function(){
        $(this).val("");
    });

    tallyQueueList.on('click', '.btn-approved', function (e) {
        e.preventDefault();

        let id = $(this).closest('.queue-list').data('id');
        let no = $(this).closest('.queue-list').data('no');
        let url = $(this).data('url');

        let modal = $('#modal-approved');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('input[name="no"]').val(no.toString());
        modal.find('.approved-label').html(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tallyQueueList.on('click', '.btn-req-handover', function (e) {
        e.preventDefault();

        let id = $(this).closest('.queue-list').data('id');
        let no = $(this).closest('.queue-list').data('no');
        let url = $(this).data('url');

        let modal = $('#modal-request-handover');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('input[name="no"]').val(no.toString());
        modal.find('.approved-label').html(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    tallyQueueList.on('click', '.btn-handover-approved', function (e) {
        e.preventDefault();

        let id = $(this).closest('.queue-list').data('id');
        let user = $(this).closest('.btn-handover-approved').data('user');
        let url = $(this).data('url');

        let modal = $('#modal-handover-approved');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('.handover-label').html(user.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    tallyQueueList.on('click', '.btn-take-handover', function (e) {
        e.preventDefault();

        let id = $(this).closest('.queue-list').data('id');
        let no = $(this).closest('.queue-list').data('no');
        let url = $(this).data('url');

        let modal = $('#modal-handover-take');
        modal.find('form').attr('action', url.toString());
        modal.find('input[name="id"]').val(id.toString());
        modal.find('.handover-label').html(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    
    const photoTemplateChecker = $('#photo-template').html();
    const modalChecker = $('#modal-checked');
    const btnAddPhotoChecker = modalChecker.find('#btn-add-photo');
    let photoWrapperChecker = modalChecker.find('#photo-wrapper');
    let totalPhotoChecker = photoWrapperChecker.children().length;


    btnAddPhotoChecker.on('click', function (e) {
        e.preventDefault();

        photoWrapperChecker.append(
            photoTemplateChecker
                .replace(/{{no}}/g, (totalPhotoChecker + 1))
                .replace(/{{index}}/g, totalPhotoChecker)
        );

        totalPhotoChecker++;

        initUploadPhoto($('.upload-photo'));
        currentWorkOrderHandheldStatus = $('#form-stock-remain').find('input[name="handheld_status"]').val();
        checkHandheldStatus();
    });
    photoWrapperChecker.on('click', '.btn-remove-photo', function (e) {
        e.preventDefault();

        var btnSubmit = $(this).closest('form');
        btnSubmit.find(':submit').attr('disabled', false);

        totalPhotoChecker--;

        $(this).closest('.card-photo').remove();

    });
});

