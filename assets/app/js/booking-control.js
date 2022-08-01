$(function () {
    const queryString = window.location.search.slice(1);
    const tableBookingControl = $('#table-booking-control.table-ajax');
    const controlTemplate = $('#control-booking-template').html();
    tableBookingControl.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search booking"
        },
        pageLength: 25,
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'booking-control/booking-control-data?' + queryString,
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title',
                data: 'customer_name'
            },
            {data: 'booking_type'},
            {data: 'no_booking'},
            {data: 'no_booking_in'},
            {data: 'booking_date'},
            {data: 'status'},
            {data: 'status_control'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) === '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full) {
                return `${full.no_booking}<br><small class="text-muted">${full.no_reference}</small>`;
            }
        }, {
            targets: ['type-reference'],
            render: function (data, type, full) {
                let ref = full.no_reference_in || '';
                if (full.no_reference_out) {
                    const refOut = full.no_reference_out.replace(/,/g, '<br>');
                    console.log(refOut);
                    ref += (ref ? '<br>' : '') + refOut;
                }
                let noBookingRef = (full.no_booking_in || full.no_booking_out);
                return (noBookingRef ? noBookingRef.replace(/,/g, '<br>') + '<br>' : '') + `<small class="text-muted">${ref}</small>`;
            }
        }, {
            targets: ['type-category'],
            render: function (data, type, full) {
                return data + ': ' + full.category;
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                let statusLabel = 'default';
                switch (data) {
                    case 'BOOKED':
                        statusLabel = 'danger';
                        break;
                    case 'REJECTED':
                        statusLabel = 'warning';
                        break;
                    case 'APPROVED':
                        statusLabel = 'success';
                        break;
                }

                return `<span class='label label-${statusLabel}'>${data.toUpperCase()}</span>`;
            }
        }, {
            targets: ['type-status-control'],
            render: function (data) {
                let statusLabel = 'default';
                switch (data) {
                    case 'CANCELED':
                        statusLabel = 'danger';
                        break;
                    case 'DRAFT':
                        statusLabel = 'warning';
                        break;
                    case 'PENDING':
                        statusLabel = 'default';
                        break;
                    case 'DONE':
                        statusLabel = 'primary';
                        break;
                    case 'CLEAR':
                        statusLabel = 'success';
                        break;
                }

                return `<span class='label label-${statusLabel}'>${data.toUpperCase()}</span>`;
            }
        }, {
            targets: ['type-action'],
            data: 'id',
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{status_control}}/g, full.status_control)
                    .replace(/{{category}}/g, full.category)
                    .replace(/{{no_booking}}/g, full.no_booking);

                control = $.parseHTML(control);

                if ((full.status_control === 'DONE' || full.status_control === 'CLEAR') && $(control).find('[data-authorize-revert=""]').length) {
                    $(control).find('.action-validate').remove();
                }
                if (full.status_control === 'CLEAR' && full.category === 'OUTBOUND') {
                    $(control).find('.action-validate').remove();
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) ? data : '-';
            }
        }]
    });


    const modalChangeStatusBooking = $('#modal-change-status-booking');
    tableBookingControl.on('click', '.btn-change-status', function (e) {
        e.preventDefault();

        const url = $(this).attr('href');
        const label = $(this).data('label');
        const category = $(this).data('category');
        const status = $(this).data('status-control');

        modalChangeStatusBooking.find('form').attr('action', url);
        modalChangeStatusBooking.find('#booking-title').text(label);

        modalChangeStatusBooking.find('#status_control option[value="CLEAR"]').remove();
        if (category === 'INBOUND') {
            modalChangeStatusBooking.find('#status_control').append('<option value="CLEAR">CLEAR</option>');
        }
        modalChangeStatusBooking.find('#status_control').val(status).trigger('change');

        modalChangeStatusBooking.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});