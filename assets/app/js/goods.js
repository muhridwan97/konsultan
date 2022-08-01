$(function () {
    var tableGoods = $('#table-goods.table-ajax');
    var controlTemplate = $('#control-goods-template').html();

    tableGoods.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search goods"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'goods/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-hide',
                data: 'customer_name'
            },
            {data: 'no_goods'},
            {data: 'no_hs'},
            {data: 'whey_number'},
            {data: 'name'},
            {data: 'no_assembly'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-customer'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? 'No Customer' : data;
            }
        }, {
            targets: ['type-goods-name'],
            render: function (data, type, full, meta) {
                return `${data}<br><small>${setNumeric(full.unit_length)} x ${setNumeric(full.unit_width)} x ${setNumeric(full.unit_height)} = 
                    <b>${setNumeric(full.unit_volume)}M<sup>3</sup></b><br>
                    <b>Net ${setNumeric(full.unit_weight)}KG</b>, <b>Gross ${setNumeric(full.unit_gross_weight)}KG</b></small>`;
            }
        }, {
            targets: ['type-action'],
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{goods}}/g, full.name);
            }
        }, {
            targets: ['type-assembly-goods'],
            render: function (data, type, full) {
                return $.trim(data) == '' ? '<a href="' + baseUrl + 'assembly-goods/create?goods=' + full.id + '"> Create </a>' : '<a href="' + baseUrl + 'assembly-goods/view?goods=' + full.id + '">' + data + '</a>';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    /**
     * Toggle button between calculate the input that related by quantity or manual edit.
     * if the input switches from manual to calculate mode then call calculateByQuantity() function to reset the value.
     */
    const formGoods = $('#form-goods');
    formGoods.find('.btn-edit-volume').on('click', function () {
        const input = $(this).closest('.input-group').find('input');
        if (input.prop('readonly')) {
            input.prop('readonly', false);
            $(this).tooltip('hide').attr('data-original-title', 'Calculate from length width height');
            $(this).html(`<i class="fa fa-calculator"></i>`);

            formGoods.find('#unit_length').prop('readonly', true).val('');
            formGoods.find('#unit_width').prop('readonly', true).val('');
            formGoods.find('#unit_height').prop('readonly', true).val('');
        } else {
            input.prop('readonly', true);
            $(this).tooltip('hide').attr('data-original-title', 'Edit manual volume will remove length, width, and height');
            $(this).html(`<i class="ion-compose"></i>`);

            formGoods.find('#unit_length').prop('readonly', false).val('');
            formGoods.find('#unit_width').prop('readonly', false).val('');
            formGoods.find('#unit_height').prop('readonly', false).val('');
        }
        input.val('');
    });

    /**
     * Rounded floating point into nearest group precision.
     * @param value
     * @param precision
     * @returns {number}
     */
    function roundVal(value, precision = 9) {
        return (+value).toFixed(precision).replace(/([0-9]+(\.[0-9]+[1-9])?)(\.?0+$)/,'$1');

        const multiplier = (function () {
            if (precision === 3) return 1000;

            let digit = '1';
            for (let i = 0; i < precision; i++) {
                digit += '0';
            }
            return Number(digit);
        })();
        return Math.round(value * multiplier) / multiplier;
    }

    /**
     * Calculate volume payload when input value in length, width and height fields is typed.
     */
    formGoods.find('#unit_length, #unit_width, #unit_height').on('input', function () {
        if (formGoods.find('#unit_volume').prop('readonly')) {
            const length = getCurrencyValue(formGoods.find('#unit_length').val());
            const width = getCurrencyValue(formGoods.find('#unit_width').val());
            const height = getCurrencyValue(formGoods.find('#unit_height').val());

            // for precaution just check if all is number otherwise just put in zero
            if (!isNaN(length) && !isNaN(width) && !isNaN(height)) {
                const volume = roundVal(length * width * height);
                formGoods.find('#unit_volume').val(setCurrencyValue(volume, '', ',', '.'));
            } else {
                formGoods.find('#unit_volume').val(0);
            }
        }
    });

});