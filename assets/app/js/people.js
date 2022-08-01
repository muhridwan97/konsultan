$(function () {
    var tablePeople = $('#table-people');
    var controlTemplate = $('#control-people-template').html();

    tablePeople.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search people"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'people/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'type'},
            {data: 'no_person'},
            {data: 'name'},
            {data: 'contact'},
            {data: 'email'},
            {data: 'whatsapp_group'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: -1,
            data: 'id',
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{person}}/g, full.name);

                control = $.parseHTML(control.trim());

                const controlEdit = $(control).find('.action-edit');
                const controlDelete = $(control).find('.action-delete');
                switch(full.type) {
                    case 'CUSTOMER':
                        if (controlEdit.data('edit-customer') === '') {
                            $(control).find('.action-edit').remove();
                        }
                        if (controlDelete.data('delete-customer') === '') {
                            $(control).find('.action-delete').remove();
                            $(control).find('.action-delete-divider').remove();
                        }
                        break;
                    case 'SUPPLIER':
                        if (controlEdit.data('edit-supplier') === '') {
                            $(control).find('.action-edit').remove();
                        }
                        if (controlDelete.data('delete-supplier') === '') {
                            $(control).find('.action-delete').remove();
                            $(control).find('.action-delete-divider').remove();
                        }
                        break;
                    default:
                        if (controlEdit.data('edit-person') === '') {
                            $(control).find('.action-edit').remove();
                        }
                        if (controlDelete.data('delete-person') === '') {
                            $(control).find('.action-delete').remove();
                            $(control).find('.action-delete-divider').remove();
                        }
                        break;
                }

                return control[0].outerHTML;
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    var formPeople = $('#form-people');
    var peopleType = formPeople.find('#type');
    var peopleUserType = formPeople.find('#type_user');
    var fieldUserType = formPeople.find('#field-user-type');
    var fieldBranch = formPeople.find("#field-branch");
    var peopleBranchAccess = formPeople.find('[name="branches[]"]');
    var peopleUserOption = formPeople.find('#user');
    var fieldwhatsappGroup = formPeople.find('#field-whatsapp-group');
    const fieldRelatedParent = formPeople.find('#field-related-parent');

    if(peopleType.val() == 'CUSTOMER'){
        fieldwhatsappGroup.show();
        if(peopleUserType.val() == "USER"){
            fieldBranch.hide();
            fieldUserType.show();

            peopleUserOption.attr("required", true);
            peopleUserOption.attr("disabled", false);
        }else{
            fieldBranch.show();
            fieldUserType.show();

            peopleUserOption.attr("required", false);
            peopleUserOption.attr("disabled", true);
        }
    }else{
        fieldwhatsappGroup.hide();
        fieldBranch.hide();
        fieldUserType.hide();

        peopleUserOption.attr("required", false);
        peopleUserOption.attr("disabled", false);
    }

    peopleType.on('change', function () {
        if($(this).val() === 'CUSTOMER') {
            $('#field-type').addClass('col-md-3').removeClass('col-md-6');
            $('#field-outbound-type').show();
            //peopleBranchAccess.iCheck('disable');
            peopleUserOption.attr("required", true);
            peopleUserType.attr("required", true);
            peopleUserOption.attr("disabled", false);
            fieldwhatsappGroup.show();

            fieldBranch.hide();
            fieldUserType.show();
            peopleUserTypeChange();
            peopleUserType.val('');
            formPeople.find('#type_user').select2();
            peopleUserOption.val('').trigger('change');
            formPeople.find('#user').select2();

            fieldRelatedParent.show();
        }else{
            fieldwhatsappGroup.hide();
            $('#field-type').addClass('col-md-6').removeClass('col-md-3');
            $('#field-outbound-type').hide();
            //peopleBranchAccess.iCheck('enable');
            peopleUserOption.attr("required", false);
            peopleUserType.attr("required", false);
            peopleUserOption.attr("disabled", false);
            
            fieldBranch.hide();
            fieldUserType.hide();
            peopleUserType.val('');
            formPeople.find('#type_user').select2();
            peopleUserOption.val('').trigger('change');
            formPeople.find('#user').select2();

            fieldRelatedParent.hide();
            fieldRelatedParent.find('#parent').val('').trigger('change');
        }
    });

    /**
     * change user type 
     */
    function peopleUserTypeChange() {
        peopleUserType.on('change', function () {
            if($(this).val() === 'USER') {
                peopleUserOption.attr("required", true);
                peopleUserOption.attr("disabled", false);
                fieldBranch.hide()
            } else {
                peopleUserOption.attr("required", false);
                peopleUserOption.attr("disabled", true);
                fieldBranch.show();
            }
        });
    }

    peopleUserType.on('change', function () {
        if($(this).val() === 'USER') {
            peopleUserOption.attr("required", true);
            peopleUserOption.attr("disabled", false);
            fieldBranch.hide()
        } else {
            peopleUserOption.attr("required", false);
            peopleUserOption.attr("disabled", true);
            fieldBranch.show();
        }
    });

    $('#field-outbound-type').on('change', function () {
        if ($(this).find('#outbound_type').val() === 'ACCOUNT RECEIVABLE') {
            $('#field-customer-storage').show();
        } else {
            $('#field-customer-storage').hide();
        }
    });

    var formEditNotification = $('#form-edit-notification');
    var inputWhatsappGroup = formEditNotification.find('#whatsapp_group');
    var selectCompliance = formEditNotification.find('#compliance');
    var selectOperational = formEditNotification.find('#operational');
    var selectExternal = formEditNotification.find('#external');
    inputWhatsappGroup.on('keyup', function () {
        selectCompliance.empty().append($('<option>'));
        selectOperational.empty().append($('<option>'));
        selectExternal.empty().append($('<option>'));

        selectCompliance.data("placeholder", "Fetching data...");
        selectCompliance.select2();
        selectOperational.data("placeholder", "Fetching data...");
        selectOperational.select2();
        selectExternal.data("placeholder", "Fetching data...");
        selectExternal.select2();
        $.ajax({
            url: baseUrl + 'whatsapp_dialog/ajax_get_participant',
            type: 'GET',
            data: {whatsapp_group: $(this).val()},
            success: function (data) {
                if (data) {
                    selectCompliance.empty().append($('<option>'));
                    selectOperational.empty().append($('<option>'));
                    selectExternal.empty().append($('<option>'));
                    data.forEach(row => {
                        selectCompliance.append(
                            $('<option>', {
                                value: row.id,
                            })
                                .text(`${row.number} (${row.name})`)
                        );
                        selectOperational.append(
                            $('<option>', {
                                value: row.id,
                            })
                                .text(`${row.number} (${row.name})`)
                        );
                        selectExternal.append(
                            $('<option>', {
                                value: row.id,
                            })
                                .text(`${row.number} (${row.name})`)
                        );
                    });
                    selectCompliance.data("placeholder", "Select Whatsapp");
                    selectOperational.data("placeholder", "Select Whatsapp");
                    selectExternal.data("placeholder", "Select Whatsapp");
                }
                if(data == ''){
                    selectCompliance.data("placeholder", "No data");
                    selectOperational.data("placeholder", "No data");
                    selectExternal.data("placeholder", "No data");
                }                
                selectCompliance.select2();
                selectOperational.select2();
                selectExternal.select2();
            }
        });
    });
});