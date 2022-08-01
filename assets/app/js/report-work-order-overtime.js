$(function () {
    var tableWorkOrderOvertime = $('#table-work-order-overtime.table-ajax');
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
        scrollX: true,
        columns: [
            {data: 'no'},
            {data: 'branch'},
            {data: 'no_reference_in'},
            {data: 'no_reference'},
            {data: 'no_work_order'},
            {data: 'customer_name'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'service_day'},
            {data: 'service_time_end'},
            {data: 'total_overtime_minute'},
            {data: 'overtime_charged_to'},
            {data: 'overtime_attachment'},
            {data: 'reason'},
            {data: 'created_at'},
            {data: 'validator_name'}
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
            targets: ['type-has-attachment'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '-' : `<a href="${full.overtime_attachment_url}">Download</a>`;
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
            targets: '_all',
            render: function (data) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

});