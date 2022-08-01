$(function () {
    var tableDangerReplacement = $('#table-danger-replacement.table-ajax');
    var controlTemplate = $('#control-danger-replacement-template').html();

    tableDangerReplacement.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search danger status data"
        },
        serverSide: true,
        processing: true,
        order: [[0, "desc"]],
        ajax: baseUrl + 'danger_replacement/danger_replacement_data',
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'status_danger'},
            {data: 'created_at'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 4,
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

    tableDangerReplacement.on('click', '.btn-validate-danger-replacement', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlValidate = $(this).attr('href');

        var modalValidate = $('#modal-validate-danger-replacement');
        modalValidate.find('form').attr('action', urlValidate);
        modalValidate.find('input[name=id]').val(id);
        modalValidate.find('#danger-replacement-title').text(label);

        modalValidate.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    var formDangerReplacement = $('#form-danger-replacement');
    var selectBooking = formDangerReplacement.find('#booking');
    var stockDataWrapper = formDangerReplacement.find('#stock-data-wrapper');

    selectBooking.change(function () {
        $.ajax({
            type: 'GET',
            url: baseUrl + "work-order/ajax_get_stock_by_booking",
            data: {id_booking: $(this).val()},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                if (data.trim() === '') {
                    stockDataWrapper.html('<p class="text-danger">No stock data available</p>');
                } else {
                    stockDataWrapper.html(data);
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });

        $('#destination-container-wrapper').find('tr[data-row]').remove();
        $('#destination-container-wrapper').find('tr#placeholder').show();

        $('#destination-item-wrapper').find('tr[data-row]').remove();
        $('#destination-item-wrapper').find('tr#placeholder').show();

        $('#total_items').val('0');
    });

    initializeLoaderStock(formDangerReplacement);
});