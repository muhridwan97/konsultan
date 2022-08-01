$(function () {
    var tableDeliveryTracking = $('#table-delivery-tracking.table-ajax');
    var controlTemplate = $('#control-delivery-tracking-template').html();
    tableDeliveryTracking.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search delivery tracking"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'delivery-tracking/data?' + window.location.search.slice(1),
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
            {data: 'no_delivery_tracking'},
            {data: 'customer_name'},
            {data: 'employee_name'},
            {data: 'total_delivery_state', class: 'text-center'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-assignment'],
            render: function (data, type, full) {
                return (full.employee_name || '') + '<br><small class="text-muted">' + (full.contact_mobile || '') + '</small>';
            }
        }, {
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                var statuses = {'ACTIVE': 'default', 'DELIVERED': 'success'};
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_delivery_tracking}}/g, full.no_delivery_tracking);

                control = $.parseHTML(control);
                if (full.status === 'DELIVERED') {
                    $(control).find('.edit').remove();
                    $(control).find('.btn-validate').remove();
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


    const formDeliveryTracking = $('#form-delivery-tracking');
    const selectReminderType = formDeliveryTracking.find('#reminder_type');
    const selectContactGroup = formDeliveryTracking.find('#contact_group');
    const selectSafeConduct = formDeliveryTracking.find('#safe_conduct');
    const btnAddSafeConduct = formDeliveryTracking.find('#btn-add-safe-conduct');
    const tableSafeConductList = formDeliveryTracking.find('#table-safe-conduct-list');

    selectReminderType.on('change', function () {
        if ($(this).val() === 'DEPARTMENT') {
            selectContactGroup.prop('disabled', false);
        } else {
            selectContactGroup.val('').trigger('change').prop('disabled', true);
        }
    });

    btnAddSafeConduct.on('click', function () {
        const safeConduct = selectSafeConduct.select2('data')[0];
        const safeConductId = selectSafeConduct.val();
        const lastRow = tableSafeConductList.find('tbody tr').not('.row-placeholder').length;

        if(!safeConductId) {
            return;
        }

        if ($(`#safe_conduct_${safeConductId}`).length) {
            alert('Safe conduct already added!');
        } else {
            tableSafeConductList.find('tbody').append(`
                <tr>
                    <td class="text-center column-no">${lastRow + 1}</td>
                    <td>${safeConduct.no_safe_conduct} - ${safeConduct.no_reference}</td>
                    <td class="text-center">
                        <input type="hidden" name="safe_conducts[]" id="safe_conduct_${safeConductId}" value="${safeConductId}">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-safe-conduct">
                            <i class="ion-trash-b"></i>
                        </button>
                    </td>
                </tr>
            `);
            tableSafeConductList.find('.row-placeholder').hide();
            selectSafeConduct.val('').trigger('change');
        }
    });

    tableSafeConductList.on('click', '.btn-remove-safe-conduct', function () {
        $(this).closest('tr').remove();

        if (tableSafeConductList.find('tbody tr').not('.row-placeholder').length === 0) {
            tableSafeConductList.find('.row-placeholder').show();
        } else {
            reorderRow();
        }
    });

    function reorderRow() {
        tableSafeConductList.find('tbody tr').not('.row-placeholder').each(function (index, el) {
            $(el).find('.column-no').text(index + 1);
        });
    }

});