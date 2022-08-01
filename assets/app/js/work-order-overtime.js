$(function () {
    var tableWorkOrderOvertime = $('#table-work-order-overtime.table-ajax');
    var controlTemplate = $('#control-work-order-overtime-template').html();
    tableWorkOrderOvertime.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search work order overtime"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'work-order-overtime/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'no_work_order'},
            {data: 'customer_name'},
            {data: 'completed_at'},
            {data: 'service_time_end'},
            {data: 'total_overtime_minute'},
            {data: 'overtime_charged_to'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-hour'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '-' : moment(full.effective_date + ' ' + data).format('HH:mm');
            }
        }, {
            targets: ['type-overtime'],
            render: function (data) {
                return data + ' minute(s)';
            }
        }, {
            targets: ['type-date-time'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('DD MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-status'],
            render: function (data, type, full) {
                if (full.total_overtime_hour > 0) {
                    return $.trim(data) == '' ? '<span class="label label-default">PENDING</span>' : data;
                } else {
                    return '<span class="label label-success">NON OVERTIME</span>'
                }
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                if (full.total_overtime_hour > 0) {
                    var control = controlTemplate
                        .replace(/{{id}}/g, full.id_work_order)
                        .replace(/{{no_work_order}}/g, full.no_work_order);

                    control = $.parseHTML(control);

                    return $('<div />').append($(control).clone()).html();
                } else {
                    return '-';
                }
            }
        }]
    });

    $('#overtime_charged_to').on('change', function () {
        if ($(this).val() == 'CUSTOMER') {
            $('#overtime_attachment').prop('required', true);
        } else {
            $('#overtime_attachment').prop('required', false);
        }
    });

});