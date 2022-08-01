$(function () {
    const tableTEPRequestPriority = $('#table-tep-request-priority.table-ajax');
    const controlTemplate = $('#control-tep-request-priority-template').html();
    tableTEPRequestPriority.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search goods"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'transporter-entry-permit-request-priority/ajax-get-data?' + window.location.search.slice(1),
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
            {data: 'customer_name'},
            {data: 'upload_description'},
            {data: 'goods_name'},
            {data: 'unit'},
            {data: 'no_requests'},
            {data: 'unload_locations'},
            {data: 'priorities'},
            {data: 'id'}
        ],
        columnDefs: [{
            orderable: false,
            targets: ['type-no'],
            render: function (data, type, full, meta) {
                return `
                    <div class="checkbox icheck">
                        <label for="check_row_${data}">
                            <input type="checkbox" id="check_row_${data}" 
                                ${full.hold_statuses.includes('HOLD') ? 'disabled' : ''}
                                class="check-rows" name="check_row_${data}" value="">
                        </label>
                    </div>
                `;
            }
        }, {
            targets: ['type-goods'],
            render: function (data, type, full) {
                return `${data}<br><small class="text-muted">${full.no_goods}</small>`;
            }
        }, {
            targets: ['type-request'],
            className: 'text-nowrap',
            render: function (data, type, full) {
                let label = '';
                if (full.hold_statuses === 'HOLD') {
                    label = '<br><span class="label label-danger">HOLD</span>';
                }
                return data ? ('<ul class="mb0" style="padding-left: 10px"><li>' + (data || '').replace(/,/g, '</li><li>') + label) : '';
            }
        }, {
            targets: ['type-action'],
            render: function (data, type, full) {
                let control = controlTemplate
                    .replace(/{{id}}/g, getRowId(full))
                    .replace(/{{id_upload}}/g, full.id_upload)
                    .replace(/{{id_booking}}/g, full.id_booking)
                    .replace(/{{id_goods}}/g, full.id_goods)
                    .replace(/{{id_unit}}/g, full.id_unit)
                    .replace(/{{ex_no_container}}/g, full.ex_no_container || '');

                control = $.parseHTML(control);

                if (full.hold_statuses.includes('HOLD')) {
                    $(control).find('.action-edit').addClass('disabled');
                }

                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: '_all',
            render: function (data) {
                return $.trim(data) === '' ? '-' : data;
            }
        }],
        drawCallback: function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        }
    });

    function getRowId(full) {
        return `id_upload=${full.id_upload}&id_booking=${full.id_booking_outbound}&id_goods=${full.id_goods}&id_unit=${full.id_unit}&ex_no_container=${full.ex_no_container || ''}`;
    }

    tableTEPRequestPriority.on('click', '.action-edit.disabled a', function(e) {
        e.preventDefault();
    });

    const btnSetPriorityBatch = $('#btn-set-priority-batch');
    tableTEPRequestPriority.on('ifChanged', '.check-rows', function () {
        const totalChecked = tableTEPRequestPriority.find('.check-rows:checked').length;
        if (totalChecked > 0) {
            btnSetPriorityBatch.show();

            let params = '';
            const checkedRow = $(".check-rows:checked");
            checkedRow.each(function () {
                const rowData = $(this).closest('tr').find('.row-data');
                const itemData = rowData.data();

                const uploadId = itemData.idUpload || rowData.data('upload-id');
                const bookingId = itemData.idBooking || rowData.data('booking-id');
                const goodsId = itemData.idGoods || rowData.data('goods-id');
                const unitId = itemData.idUnit || rowData.data('unit-id');
                const exNoContainer = itemData.exNoContainer || rowData.data('ex-no-container');

                if (params !== '') {
                    params += '&';
                }
                params += `id_upload[]=${uploadId}&id_booking[]=${bookingId}&id_goods[]=${goodsId}&id_unit[]=${unitId}&ex_no_container[]=${exNoContainer || ''}`
            });

            btnSetPriorityBatch.prop('href', baseUrl + 'transporter-entry-permit-request-priority/edit-batch?' + params)
        } else {
            btnSetPriorityBatch.hide();
        }
    });

    tableTEPRequestPriority.on('click', '.action-edit a', function (e) {
        const checkRow = $(this).closest('tr').find('.check-rows');
        if (checkRow.is(':checked')) {
            btnSetPriorityBatch.get(0).click();
            e.preventDefault();
        }
    });

});