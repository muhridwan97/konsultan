$(function () {
    const tableAssignment = $('#table-booking-assignment.table-ajax');
    const controlTemplate = $('#control-booking-assignment-template').html();
    tableAssignment.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search assignment"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'booking-assignment/booking-assignment-data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title',
                data: 'no_booking'
            },
            {data: 'name'},
            {data: 'created_at'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 1,
            render: (data, type, full) => {
                return data + '<br><small class="text-muted">' + full.no_reference + '</small>';
            }
        }, {
            targets: ['type-date'],
            render: (data) => {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: ['type-action'],
            render: (data, type, full) => {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{assignment_label}}/g, `Assignment ${full.no_booking}`);
            }
        }]
    });

});