$(function () {
    var tableCustomer = $('#table-customer');
    var controlTemplate = $('#control-customer-template').html();

    tableCustomer.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search customer"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'customer/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'identity_number'},
            {data: 'name'},
            {data: 'contact'},
            {data: 'email'},
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

    var formCustomer = $('#form-customer');
    var customerType = formCustomer.find('#type');
    var customerUserType = formCustomer.find('#type_user');
    var fieldUserType = formCustomer.find('#field-user-type');
    var fieldBranch = formCustomer.find("#field-branch");
    var customerBranchAccess = formCustomer.find('[name="branches[]"]');
    var customerUserOption = formCustomer.find('#user');
    var fieldwhatsappGroup = formCustomer.find('#field-whatsapp-group');
    const fieldRelatedParent = formCustomer.find('#field-related-parent');

    if(customerType.val() == 'CUSTOMER'){
        fieldwhatsappGroup.show();
        if(customerUserType.val() == "USER"){
            fieldBranch.hide();
            fieldUserType.show();

            customerUserOption.attr("required", true);
            customerUserOption.attr("disabled", false);
        }else{
            fieldBranch.show();
            fieldUserType.show();

            customerUserOption.attr("required", false);
            customerUserOption.attr("disabled", true);
        }
    }else{
        fieldwhatsappGroup.hide();
        fieldBranch.hide();
        fieldUserType.hide();

        customerUserOption.attr("required", false);
        customerUserOption.attr("disabled", false);
    }

    customerType.on('change', function () {
        if($(this).val() === 'CUSTOMER') {
            $('#field-type').addClass('col-md-3').removeClass('col-md-6');
            $('#field-outbound-type').show();
            //customerBranchAccess.iCheck('disable');
            customerUserOption.attr("required", true);
            customerUserType.attr("required", true);
            customerUserOption.attr("disabled", false);
            fieldwhatsappGroup.show();

            fieldBranch.hide();
            fieldUserType.show();
            customerUserTypeChange();
            customerUserType.val('');
            formCustomer.find('#type_user').select2();
            customerUserOption.val('').trigger('change');
            formCustomer.find('#user').select2();

            fieldRelatedParent.show();
        }else{
            fieldwhatsappGroup.hide();
            $('#field-type').addClass('col-md-6').removeClass('col-md-3');
            $('#field-outbound-type').hide();
            //customerBranchAccess.iCheck('enable');
            customerUserOption.attr("required", false);
            customerUserType.attr("required", false);
            customerUserOption.attr("disabled", false);
            
            fieldBranch.hide();
            fieldUserType.hide();
            customerUserType.val('');
            formCustomer.find('#type_user').select2();
            customerUserOption.val('').trigger('change');
            formCustomer.find('#user').select2();

            fieldRelatedParent.hide();
            fieldRelatedParent.find('#parent').val('').trigger('change');
        }
    });

    /**
     * change user type 
     */
    function customerUserTypeChange() {
        customerUserType.on('change', function () {
            if($(this).val() === 'USER') {
                customerUserOption.attr("required", true);
                customerUserOption.attr("disabled", false);
                fieldBranch.hide()
            } else {
                customerUserOption.attr("required", false);
                customerUserOption.attr("disabled", true);
                fieldBranch.show();
            }
        });
    }

    customerUserType.on('change', function () {
        if($(this).val() === 'USER') {
            customerUserOption.attr("required", true);
            customerUserOption.attr("disabled", false);
            fieldBranch.hide()
        } else {
            customerUserOption.attr("required", false);
            customerUserOption.attr("disabled", true);
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