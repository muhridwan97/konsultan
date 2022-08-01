$(function () {
    var select2CurrentOwner = $(".select2CurrentOwner");
    var select2NewOwner = $(".select2NewOwner");
    var formChangeOwnership = $("#form-changeOwnership");
    var stockDataWrapper = formChangeOwnership.find('#stock-data-wrapper');
    var booking = formChangeOwnership.find('#booking');

    select2CurrentOwner.select2({
        placeholder: 'Search Current Owner',
        ajax: {
            url: baseUrl + 'people/ajax_get_people',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    q: params.term,
                    page: params.page || 1
                }

                return query;
            },
            processResults: function (data, params) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    }),
                    pagination: {
                        more: (params.page * 5) < data.total_count
                    }
                };
            }
        }
    });

    select2CurrentOwner.on('change', function () {
        getBookingCustomer($(this).val());
    });

    function getBookingCustomer(customer) {
        booking.empty();
        booking.append($('<option>'));
        booking.prop("disabled", true);
        $.ajax({
            type: "GET",
            url: baseUrl + "booking/ajax_get_stock_booking_by_customer",
            data: {id_customer: customer},
            cache: true,
            accepts: {
                text: "application/json"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));

                $.each(data, function (index, value) {
                    booking.append(
                        $('<option>', {
                            value: value.id
                        }).text(value.no_booking + ' (' + value.no_reference + ')')
                    );
                });

                booking.prop("disabled", false);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    }

    select2NewOwner.select2({
        placeholder: 'Search New Owner',
        ajax: {
            url: baseUrl + 'people/ajax_get_people',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    q: params.term,
                    page: params.page || 1
                }

                return query;
            },
            processResults: function (data, params) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    }),
                    pagination: {
                        more: (params.page * 5) < data.total_count
                    }
                };
            }
        }
    });

    if (booking.val() != null && booking.val() !== '') {
        fetchBookingData(booking.val());
    }

    booking.on('change', function () {
        fetchBookingData($(this).val());
    });

    function fetchBookingData(bookingId) {
        $.ajax({
            type: 'GET',
            url: baseUrl + "work-order/ajax_get_stock_by_booking",
            data: {id_booking: bookingId},
            cache: true,
            headers: {
                Accept: "text/html; charset=utf-8",
                "Content-Type": "text/plain; charset=utf-8"
            },
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                if (data.trim() == '') {
                    stockDataWrapper.html('<p class="text-danger">No stock data available</p>');
                } else {
                    stockDataWrapper.html(data);
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    };

    initializeLoaderHandling();

    function initializeLoaderHandling() {
        formChangeOwnership.on('click', '.btn-take', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableSource = $(this).closest('#source-' + type + '-wrapper');
            var tableDestination = formChangeOwnership.find('#destination-' + type + '-wrapper');

            var rowSource = tableSource.find('[data-row=' + id + ']');
            rowSource.find('input[type=hidden]').attr('disabled', false);
            rowSource.find('#quantity').attr('type', 'number');
            rowSource.find('#quantity-label').addClass('hidden');
            rowSource.find('#tonnage').attr('type', 'number');
            rowSource.find('#tonnage-label').addClass('hidden');
            rowSource.find('#volume').attr('type', 'number');
            rowSource.find('#volume-label').addClass('hidden');
            rowSource.find('.row-job').hide();
            tableDestination.append(rowSource);

            if (tableSource.find('tr[data-row]').length == 0) {
                tableSource.find('#placeholder').show();
            }
            if (tableDestination.find('tr[data-row]').length > 0) {
                tableDestination.find('#placeholder').hide();
            }

            $(this).text('Return').attr('class', 'btn btn-danger btn-block btn-return');
            $('#total_items').val(parseInt($('#total_items').val()) + 1);

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });

        formChangeOwnership.on('click', '.btn-return', function () {
            var type = $(this).data('type');
            var id = $(this).data('id');

            var tableDestination = $(this).closest('#destination-' + type + '-wrapper');
            var tableSource = formChangeOwnership.find('#source-' + type + '-wrapper');

            var rowDestination = tableDestination.find('[data-row=' + id + ']');
            rowDestination.find('input[type=hidden]').attr('disabled', true);
            rowDestination.find('#quantity').attr('type', 'hidden');
            rowDestination.find('#quantity-label').removeClass('hidden');
            rowDestination.find('#tonnage').attr('type', 'hidden');
            rowDestination.find('#tonnage-label').removeClass('hidden');
            rowDestination.find('#volume').attr('type', 'hidden');
            rowDestination.find('#volume-label').removeClass('hidden');
            rowDestination.find('.row-job').show();
            tableSource.append(rowDestination);

            if (tableDestination.find('tr[data-row]').length == 0) {
                tableDestination.find('#placeholder').show();
            }
            if (tableSource.find('tr[data-row]').length > 0) {
                tableSource.find('#placeholder').hide();
            }

            $(this).text('Take').attr('class', 'btn btn-primary btn-block btn-take');
            $('#total_items').val(parseInt($('#total_items').val()) - 1);

            reorderTable(tableSource);
            reorderTable(tableDestination);
        });
    }

    function reorderTable(table) {
        table.find('tr[data-row]').not('#placeholder').not('.skip-ordering')
            .each(function (index) {
                $(this).children('td').first().html(index + 1);
            });
    }

    $(document).on('click', '.btn-delete-changeOwnership', function (e) {
        e.preventDefault();

        var idOwnership = $(this).data('id');
        var labelOwnership = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteOwnership = $('#modal-delete-changeOwnership');
        modalDeleteOwnership.find('form').attr('action', urlDelete);
        modalDeleteOwnership.find('input[id]').val(idOwnership);
        modalDeleteOwnership.find('#changeOwnership-title').text(labelOwnership);

        modalDeleteOwnership.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});