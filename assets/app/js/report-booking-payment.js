$(function () {
    var tablePayment = $('#table-report-payment');
    var controlTemplate = $('#control-payment-template').html();
    const dataTablePayment = tablePayment.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search payment"},
        serverSide: true,
        pageLength: 25,
        processing: true,
        ajax: baseUrl + 'report/data_booking_payment?' + window.location.search.slice(1),
        // order: [[32, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title no-wrap',
                data: 'no_payment'
            },
            {data: 'no_booking'},
            {data: 'customer_name'},
            {data: 'payment_category'},
            {data: 'payment_type'},
            {data: 'ask_payment'},
            {data: 'payment_method'},
            {data: 'bank_name'},
            {data: 'holder_name'},
            {data: 'bank_account_number'},
            {data: 'withdrawal_date'},
            {data: 'payment_date'},
            {data: 'settlement_date'},
            {data: 'charge_position'},
            {data: 'amount'},
            {data: 'amount_request'},
            {data: 'elapsed_day_until_realized'},
            {data: 'applicant_name'},
            {data: 'validator_name'},
            {data: 'description'},
            {data: 'invoice_description'},
            {data: 'attachment'},
            {data: 'attachment_realization'},
            {data: 'status'},
            {data: 'is_realized'},
            {data: 'status_check'},
            {data: 'created_at'},
            {data: 'updated_at'},
            {data: 'approved_at'},
            {data: 'date_submitted'},
            {data: 'realized_at'},
            {data: 'elapsed_hour_until_realized'}
        ],
        columnDefs: [ {
            targets: ['type-payment'],
            render: function (data, type, full, meta) {
                return '<a href="' + assetUrl + 'p/' + full.id_branch + '/payment/view/' + full.id + '" target = "_blank">' + data + '</a><br><small class="text-danger">' + full.branch + '</small>';
            }
        },{
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                if (full.id_booking != 0) {
                    return '<a href="' + assetUrl + 'p/' + full.id_branch + '/booking/view/' + full.id_booking + '" target = "_blank">' + data + '<br><span class="text-muted">' + full.no_reference + '</span>';    
                }else{
                    return '<a href="' + assetUrl + 'p/' + full.id_branch + '/upload/view/' + full.id_upload + '" target = "_blank">' +full.no_upload+ '<br><span class="text-muted">' + full.upload_description + '</span>';
                }
                
            }
        },{
            targets: ['type-withdrawal-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        },{
            targets: ['type-payment-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        },{
            targets: ['type-settlement-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        },{
            targets: ['type-amount-realization'],
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat(data, 0, ',', '.');
            }
        },{
            targets: ['type-amount-request'],
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat(data, 0, ',', '.');
            }
        },{
            targets: ['type-day-realized'],
            render: function (data, type, full, meta) {
                return data + ' Days';
            }
        },{
            targets: ['type-attachment'],
            render: function (data, type, full, meta) {
                return '<a href="' + assetUrl + 'uploads/payments/' + data + '" target = "_blank">Download Attachment</a>';
            }
        },{
            targets: ['type-attachment-realization'],
            render: function (data, type, full, meta) {
                return '<a href="' + assetUrl + 'uploads/payments/' + data + '" target = "_blank">Download Attachment</a>';
            }
        }, {
            targets: ['type-status-payment'],
            render: function (data, type, full, meta) {
                var statusLabel = 'default';
                switch (data) {
                    case 'APPROVED':
                        statusLabel = 'success';
                        break;
                    case 'REJECTED':
                        statusLabel = 'danger';
                        break;
                }
                return '<span class="label label-' + statusLabel + '">' + data + '</span>';
            }
        },{
            targets: ['type-status-realization'],
            render: function (data, type, full, meta) {
                if(full.is_submitted === '1' && data === '0'){
                    var statusLabel = full.is_submitted === '1' && $.trim(full.submitted_at) !== '' ? 'warning' : 'default';
                    var value = full.is_submitted === '1' ? 'SUBMITTED' : 'REJECTED';
                    return '<span class="label label-' + statusLabel + '">' + value + '</span>';
                }else{
                    var statusLabel = data === '1' ? 'primary' : 'default';
                    var value = data === '1' ? 'REALIZED' : 'REQUESTED';
                    return '<span class="label label-' + statusLabel + '">' + value + '</span>';
                }
            }
        }, {
            targets: ['type-status-check'],
            render: function (data, type, full, meta) {
                var statusLabel = 'default';
                switch (data) {
                    case 'APPROVED':
                        statusLabel = 'success';
                        break;
                    case 'REJECTED':
                        statusLabel = 'danger';
                        break;
                    case 'PENDING':
                        statusLabel = 'default';
                        break;
                    case 'ASK APPROVAL':
                        statusLabel = 'warning';
                        break;
                    case 'SUBMITTED':
                        statusLabel = 'warning';
                        break;
                    case 'SUBMISSION REJECTED':
                        statusLabel = 'danger';
                        break;
                }
                return '<span class="label label-' + statusLabel + '">' + data + '</span>';
            }
        },{
            targets: ['type-created-at'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm:ss');
            }
        },{
            targets: ['type-updated-at'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm:ss');
            }
        },{
            targets: ['type-approved-at'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm:ss');
            }
        },{
            targets: ['type-submitted-at'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm:ss');
            }
        },{
            targets: ['type-realized-at'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm:ss');
            }
        },{
            targets: ['type-hour-realized'],
            render: function (data, type, full, meta) {
                return data + ' Hours';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }],
        drawCallback: function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
            $('[data-toggle="tooltip"]').tooltip({container: 'body'});
        }
    });
});
