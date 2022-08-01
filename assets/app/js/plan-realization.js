$(function () {
    var tableServiceHour = $('#table-plan-realization.table-ajax');
    var controlTemplate = $('#control-plan-realization-template').html();
    tableServiceHour.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search service hour"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'plan-realization/ajax-get-data?' + window.location.search.slice(1),
            type: "GET",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        columns: [
            {data: 'no'},
            {data: 'date'},
            {data: 'total_inbound'},
            {data: 'total_outbound'},
            {data: 'analysis'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('DD MMMM YYYY');
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let labelStatus = 'success';
                if (data == 'CLOSED') {
                    labelStatus = 'danger';
                }
                return `<span class="label label-${labelStatus}">${data}</span>`;
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{date}}/g, full.date)
                    .replace(/{{send_label}}/g, full.status == 'CLOSED' ? 'Realization' : 'plan');

                control = $.parseHTML(control);

                if (full.status == 'CLOSED') {
                    $(control).find('.close-realization').remove();
                    $(control).find('.edit-plan').remove();
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    const formPlanRealization = $('#form-plan-realization');

    formPlanRealization.on('ifChanged', '.check-all', function () {
        const targetRows = $($(this).data('target')).find('.check-booking').not(':disabled');
        if ($(this).is(':checked')) {
            targetRows.iCheck('check');
        } else {
            targetRows.iCheck('uncheck');
        }
    });

    formPlanRealization.find('.check-booking').not(':checked').each(function () {
        const groupRow = $(this).closest('tr').data('group');
        const inputQuery = 'input[type=text], input[type=number], input[type=hidden], textarea';
        $(this).closest('table').find('[data-group="' + groupRow + '"]').find(inputQuery).prop('disabled', true);
        if ($(this).closest('tr').data('has-plan') === 0) {
            $(this).prop('disabled', true);
        }
    });

    formPlanRealization.on('ifChanged', '.check-booking', function () {
        const inputQuery = 'input[type=text], input[type=number], input[type=hidden], textarea';
        const groupRow = $(this).closest('tr').data('group');
        if ($(this).is(':checked')) {
            $(this).closest('table').find('[data-group="' + groupRow + '"]').find(inputQuery).prop('disabled', false);
        } else {
            $(this).closest('table').find('[data-group="' + groupRow + '"]').find(inputQuery).prop('disabled', true);
        }
    });

    // edit plan realization
    formPlanRealization.on('click', '.btn-delete-booking', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
    });

});

function searchTableRowGroup(input, tableId, column) {
    // Declare variables
    var filter, table, tr, td, i;
    filter = input.value.toUpperCase();
    table = document.getElementById(tableId);
    tr = $(table).find('tbody tr').not('#placeholder').not('.skip-filtering');

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[column];
        if (td) {
            const group = $(tr[i]).data('group');
            console.log(group)
            if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
                $(table).find(`[data-group="${group}"]`).show();
            } else {
                tr[i].style.display = "none";
                $(table).find(`[data-group="${group}"]`).hide();
            }
        }
    }
}