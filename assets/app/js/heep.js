$(function () {
    const table = $('#table-heep.table-ajax');
    const controlTemplate = $('#control-heep-template').html();
    table.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search permit"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'heavy-equipment-entry-permit/ajax-get-data',
        order: [[0, "desc"]],
        pageLength: 25,
        columns: [
            {data: 'no', class: 'responsive-hide'},
            // {data: 'customer_name', class: 'responsive-title'},
            {data: 'no_heep'},
            {data: 'heep_code'},
            {data: 'checked_in_at'},
            {data: 'checker_name'},
            {data: 'checked_out_at'},
            {data: 'checker_out_name'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data) === '' || data == null ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        },  {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{code}}/g, full.heep_code);

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

    const formHEEP = $('#form-heep');
    const orderWrapper = formHEEP.find('.order-wrapper');
    const selectOrder = orderWrapper.find('#purchase_order');
    const heepWrapper = formHEEP.find('.heep-wrapper');
    const selectHeep = heepWrapper.find('#heep_reference');
    formHEEP.find("#relate").on('change', function(){
        var relate = $(this).val();
        
        if (relate=='PURCHASE') {
            orderWrapper.show();
            heepWrapper.hide(); 
            selectHeep.empty();
            selectHeep.prop('required',false);
            selectOrder.prop('required',true);
        } else {
            selectOrder.empty();
            heepWrapper.show(); 
            orderWrapper.hide();   
            selectHeep.prop('required',true);
            selectOrder.prop('required',false);
        }
    });
    formHEEP.find("#relate").trigger('change');
    
});