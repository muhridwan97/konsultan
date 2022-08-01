$(function () {
    var tableReaddress = $('#table-readdress.table-ajax');
    var controlTemplate = $('#control-readdress-template').html();

    tableReaddress.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search readdress data"
        },
        serverSide: true,
        processing: true,
        order: [[0, "desc"]],
        ajax: baseUrl + 'readdress/readdress_data',
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'customer_from'},
            {data: 'customer_to'},
            {data: 'created_at'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 5,
            render: function (data, type, full, meta) {
                return moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: -2,
            render: function (data, type, full, meta) {
                var statusLabel = 'primary';
                if (data === 'PENDING') {
                    statusLabel = 'default';
                } else if (data === 'APPROVED') {
                    statusLabel = 'success';
                } else if (data === 'REJECTED') {
                    statusLabel = 'danger';
                }
                return "<span class='label label-" + statusLabel + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{no_booking}}/g, full.no_booking + ' (' + full.no_reference + ')');

                control = $.parseHTML(control);
                if (full.status !== 'PENDING') {
                    $(control).find('.validate').remove();
                }
                return $('<div />').append($(control)).html();
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    tableReaddress.on('click', '.btn-validate-readdress', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlValidate = $(this).attr('href');

        var modalValidate = $('#modal-validate-readdress');
        modalValidate.find('form').attr('action', urlValidate);
        modalValidate.find('input[name=id]').val(id);
        modalValidate.find('#readdress-title').text(label);

        modalValidate.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

});