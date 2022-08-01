$(function () {
    var tableAuction = $('#table-auction.table-ajax');
    var controlTemplate = $('#control-auction-template').html();
    tableAuction.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search auction"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'auction/auction_data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title',
                data: 'no_auction'
            },
            {data: 'no_doc'},
            {data: 'doc_date'},
            {data: 'auction_date'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                var statuses = {'PENDING': 'default', 'APPROVED': 'success', 'REJECTED': 'danger'};
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_auction}}/g, full.no_auction);

                var control = $.parseHTML(control);
                if (full.status === 'APPROVED') {
                    $(control).find('.edit').remove();
                }
                if (full.status === 'APPROVED' || full.status === 'REJECTED') {
                    $(control).find('.btn-validate').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }]
    });



    var formAuction = $('#form-auction');
    var buttonAddBooking = formAuction.find('.btn-add-booking');
    var tableBooking = formAuction.find('#table-detail-booking tbody');
    var rowPlaceholder = tableBooking.find('.row-placeholder');
    var bookingTemplate = $('#row-booking-template').html();

    buttonAddBooking.on('click', function () {
        rowPlaceholder.hide();
        tableBooking.append(bookingTemplate);
        tableBooking.find('tr:last-child select').select2();
        reorderRow();
    });

    tableBooking.on('click', '.btn-remove-booking', function () {
        $(this).closest('tr').remove();

        if(tableBooking.find('tr').not(rowPlaceholder).length === 0) {
            rowPlaceholder.show();
        } else {
            reorderRow();
        }
    });

    function reorderRow() {
        tableBooking.find('tr').not(rowPlaceholder).each(function (index, el) {
            $(el).find('.no').text(index + 1);
        });
    }
});