$(function () {
    // filter index component per customer
    var formComponentPriceFilter = $('#form-component-price-filter');
    formComponentPriceFilter.find('#customer').on('change', function () {
        formComponentPriceFilter.submit();
    });

    var tableInvoice = $('#table-component-price.table-ajax');
    var controlTemplate = $('#control-invoice-template').html();
    tableInvoice.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search price"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'component_price/component_price_data?customer=' + (getAllUrlParams().customer == undefined ? '' : getAllUrlParams().customer),
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
            {data: 'price_type'},
            {data: 'price_subtype'},
            {data: 'handling_type_name'},
            {data: 'component_name'},
            {data: 'rule'},
            {data: 'price'},
            {data: 'status'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 1,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? 'ALL CUSTOMER' : data;
            }
        }, {
            targets: 4,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? 'No handling type' : data;
            }
        }, {
            targets: 5,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? 'No component' : data;
            }
        }, {
            targets: 6,
            render: function (data, type, full, meta) {
                return data.split(',').join('<br>');
            }
        }, {
            targets: 7,
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: ['type-action'],
            data: 'id',
            render: function (data, type, full, meta) {
                var control = controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{price_label}}/g, full.price_type + ' (Rp. ' + numberFormat(full.price, 0, ',', '.') + ')');

                control = $.parseHTML(control);
                if (full.status === 'APPROVED') {
                    $(control).find('.edit').remove();
                }
                if (full.status !== 'PENDING' && !(!full.status && full.total_files > 0)) {
                    $(control).find('.btn-validate').remove();
                }
                return $('<div />').append($(control).clone()).html();
            }
        }, {
            targets: ['type-status'],
            render: function (data) {
                var statuses = {
                    'PENDING': 'default',
                    'APPROVED': 'success',
                    'REJECTED': 'danger'
                };
                return "<span class='label label-" + statuses[data] + "'>" + data.toUpperCase() + "</span>";
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-component-price').on('click', '.btn-delete-component-price', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var label = $(this).data('label');
        var urlDelete = $(this).attr('href');

        var modalDelete = $('#modal-delete-component-price');
        modalDelete.find('form').attr('action', urlDelete);
        modalDelete.find('input[name=id]').val(id);
        modalDelete.find('#component-price-title').text(label);

        modalDelete.modal({
            backdrop: 'static',
            keyboard: false
        });
    });


    // rules price form
    var formComponentPrice = $('#form-component-price');
    var priceTypeSelect = formComponentPrice.find('#price_type');
    var priceSubTypeSelect = formComponentPrice.find('#price_subtype');
    var handlingTypeWrapper = formComponentPrice.find('#handling-type-wrapper');
    var handlingTypeComponentWrapper = formComponentPrice.find('#handling-component-wrapper');
    var fieldRules = formComponentPrice.find('#rules-field');

    // table price form
    var fieldPrice = formComponentPrice.find('#price-field');
    var priceActivity = formComponentPrice.find('#activity-price');

    // special rule
    var perVolume = formComponentPrice.find('#per_volume_rule');
    var perTonnage = formComponentPrice.find('#per_tonnage_rule');
    var perUnit = formComponentPrice.find('#per_unit_rule');


    function checkNeedPerDay() {
        if (priceTypeSelect.val() !== '' && priceSubTypeSelect.val() !== '') {
            fieldRules.show();
            if (priceTypeSelect.val() === 'STORAGE') {
                fieldRules.find('.per-day-wrapper').show();
                fieldRules.find('.per-day-wrapper input').removeAttr('disabled');
            } else {
                fieldRules.find('.per-day-wrapper').hide();
                fieldRules.find('.per-day-wrapper input').attr('disabled', true).iCheck('uncheck');
                if (priceSubTypeSelect.val() === 'ACTIVITY') {
                    fieldRules.hide();
                }
            }
        }
    }

    priceTypeSelect.on('change', function () {
        // hide description by default (only invoice type)
        formComponentPrice.find('#description-wrapper').hide();
        formComponentPrice.find('#description-wrapper textarea').attr('required', false);

        // hide handling and component by default
        handlingTypeWrapper.hide();
        handlingTypeComponentWrapper.hide();
        handlingTypeWrapper.find('select').removeAttr('required');
        handlingTypeComponentWrapper.find('select').attr('required');

        switch ($(this).val()) {
            case 'INVOICE':
                formComponentPrice.find('#description-wrapper').show();
                formComponentPrice.find('#description-wrapper textarea').attr('required', true);
                break;
            case 'HANDLING':
                handlingTypeWrapper.addClass('col-md-12').removeClass('col-md-6').show();
                handlingTypeComponentWrapper.hide();
                handlingTypeWrapper.find('select').attr('required', true);
                break;
            case 'COMPONENT':
                handlingTypeWrapper.addClass('col-md-6').removeClass('col-md-12').show();
                handlingTypeComponentWrapper.show();
                handlingTypeWrapper.find('select').attr('required', true);
                handlingTypeComponentWrapper.find('select').attr('required', true);
                break;
        }
        checkNeedPerDay();
    });


    priceSubTypeSelect.on('change', function subTypeSelectCheck() {
        fieldRules.find('input[type=checkbox]').iCheck('uncheck');
        fieldRules.find('input').attr('disabled', true);
        fieldRules.find('[class*=col-]').hide();

        switch ($(this).val()) {
            case 'ACTIVITY':
                activityRulesCheck();
                break;
            case 'CONTAINER':
                containerRulesCheck();
                break;
            case 'GOODS':
                goodsRulesCheck();
                break;
        }
        checkNeedPerDay();
    });


    function activityRulesCheck() {
        priceActivity.show();
        priceActivity.find('input').attr('disabled', false);
    }

    function containerRulesCheck() {
        activityRulesCheck();

        fieldRules.find('.per-day-wrapper').show();
        fieldRules.find('.per-day-wrapper input').attr('disabled', false);

        fieldRules.find('.per-size-wrapper').show();
        fieldRules.find('.per-size-wrapper input').attr('disabled', false);

        fieldRules.find('.per-type-wrapper').show();
        fieldRules.find('.per-type-wrapper input').attr('disabled', false);

        fieldRules.find('.per-empty-wrapper').show();
        fieldRules.find('.per-empty-wrapper input').attr('disabled', false);

        fieldRules.find('.per-danger-wrapper').show();
        fieldRules.find('.per-danger-wrapper input').attr('disabled', false);

        fieldRules.find('.per-condition-wrapper').show();
        fieldRules.find('.per-condition-wrapper input').attr('disabled', false);
    }

    function goodsRulesCheck() {
        activityRulesCheck();

        fieldRules.find('.per-day-wrapper').show();
        fieldRules.find('.per-day-wrapper input').attr('disabled', false);

        fieldRules.find('.per-danger-wrapper').show();
        fieldRules.find('.per-danger-wrapper input').attr('disabled', false);

        fieldRules.find('.per-condition-wrapper').show();
        fieldRules.find('.per-condition-wrapper input').attr('disabled', false);

        fieldRules.find('.per-volume-wrapper').show();
        fieldRules.find('.per-volume-wrapper input').attr('disabled', false);

        fieldRules.find('.per-tonnage-wrapper').show();
        fieldRules.find('.per-tonnage-wrapper input').attr('disabled', false);

        fieldRules.find('.per-unit-wrapper').show();
        fieldRules.find('.per-unit-wrapper input').attr('disabled', false);
    }

    formComponentPrice.on('ifChanged', '#per_unit_rule', function () {
        if ($(this).is(':checked')) {
            perTonnage.iCheck('uncheck');
            perVolume.iCheck('uncheck');
        }
    });

    formComponentPrice.on('ifChanged', '#per_volume_rule', function () {
        if ($(this).is(':checked')) {
            perUnit.iCheck('uncheck');
            perTonnage.iCheck('uncheck');
        }
    });

    formComponentPrice.on('ifChanged', '#per_tonnage_rule', function () {
        if ($(this).is(':checked')) {
            perUnit.iCheck('uncheck');
            perVolume.iCheck('uncheck');
        }
    });


    // define constant of price component attributes,
    // the key of object must be same with value of input .rule-check (as reference hash-table method)
    const priceAttributes = {
        'PER_SIZE': [20, 40, 45],
        'PER_TYPE': ['STD', 'HC', 'OT', 'FR', 'TANK'],
        'PER_EMPTY': ['EMPTY', 'FULL'],
        'PER_DANGER': ['NOT DANGER', 'DANGER TYPE 1', 'DANGER TYPE 2'],
        'PER_CONDITION': ['GOOD', 'DAMAGE', 'USED'],

        'PER_VOLUME': ['> 0 M<sup>3</sup>', '> 5 M<sup>3</sup>'],
        'PER_TONNAGE': ['> 0 Ton', '> 5 Ton'],
        'PER_UNIT': ['select_unit'] //convert to select unit list
    };

    // crazy recursive trigger of mapping dynamic loop
    function buildPriceMap(maxIndices, func) {
        recursivePriceMap(maxIndices, func, [], 0);
    }

    // recursive function to build index of table price, result of row will be passed to callback function
    function recursivePriceMap(maxIndices, func, args, index) {
        if (maxIndices.length === 0) {
            func(args);
        } else {
            var rest = maxIndices.slice(1);
            for (args[index] = 0; args[index] < priceAttributes[maxIndices[0]].length; ++args[index]) {
                recursivePriceMap(rest, func, args, index + 1);
            }
        }
    }

    // [x] PER_SIZE [x] PER_TYPE [ ] PER_DANGER ... etc
    // collect all checked rule, join the value as id to identify combination and recall tha table
    // when it's needed latter so we do not lose out of input data or regenerate the table again!
    // from example above we get a table with id #PER_SIZE-PER_TYPE and contain combination of
    // [20, 40, 45] by ['STD', 'HC', 'OT', 'FR'] in column-row of table price
    formComponentPrice.on('ifChanged', '.rule-check', function () {
        // hide all table price and disable inputs, so what we post will clear of unnecessary data
        fieldPrice.find('.table-price').hide();
        fieldPrice.find('input').attr('disabled', true);

        // hide single price container field as well
        priceActivity.hide();

        // get all rule checked [x] PER_SIZE [x] PER_TYPE
        // this variable IS SO IMPORTANT AND CRUCIAL, you find a lot after this line
        // result array [PER_SIZE, PER_TYPE, ... ] depend of checked rule
        var checkedValues = $('.rule-check:checked').map(function () {
            return this.value;
        }).get();

        // if any of rules is checked then build table markup string, here seems like spaghetti code,
        // just look line by line, it's tagging a table, build <thead>, then <tbody> which is contain label and inputs.
        if (checkedValues.length > 0) {
            // now build rule price table, we begin to add an ID as identifier of the table (we need latter).
            // [x] PER_SIZE [x] PER_TYPE [ ] PER_DANGER  --->  #PER_SIZE-PER_TYPE  (id of table price checked combination)
            // [ ] PER_SIZE [x] PER_TYPE [x] PER_DANGER  --->  #PER_TYPE-PER_DANGER (id of table price checked combination)
            var generatedTablePriceId = checkedValues.join('-');
            var generatedTablePrice = fieldPrice.find('#' + generatedTablePriceId);

            // if we found table by joined id then we had built the table before,
            // so rather than generate from beginning and lose out inputs, then show them up instead.
            if (generatedTablePrice.length) {
                generatedTablePrice.find('input').removeAttr('disabled');
                generatedTablePrice.show();
            } else {
                // we don't have any generated table price before, then build one (depend on checked rule)
                // here just variable that hold the HTML table markup, pay attention to this --------------------v bellow here add id of table so we could identify that we have generated this table price, look line code above!
                var tableWrapper = '<table class="table no-datatable table-bordered table-price" id="' + generatedTablePriceId + '">';
                var tableHead = '<thead>';
                var tableHeadClose = '</thead>';
                var tableBody = '<tbody>';
                var tableBodyClose = '</tbody>';
                var tableWrapperClose = '</table>';

                // build head of table price:
                // var tableHead (which is <thead>)
                // <th>PER_SIZE</th>
                // <th>PER_TYPE</th>
                // <th>....</th> depends on checked rules
                // <th>PRICE</th> required
                // var tableHeadClose (which is </thead>)
                var tablePriceHead = '<tr>';
                for (var i = 0; i < checkedValues.length; i++) {
                    tablePriceHead += '<th>' + checkedValues[i] + '</th>';
                }
                tablePriceHead += '<th>PRICE</th>';
                tablePriceHead += '</tr>';

                // build body of table price:
                // <td>20 <input type='hidden' name='prices[index][PER_SIZE]' value='20'></td>
                // <td>STD <input type='hidden' name='prices[index][PER_TYPE]' value='STD'></td>
                // <td>...</td> depends on checked rules (dynamic nested loop with recursive -- made me crazy T_T)
                // <td><input name='prices[index][PRICE]'></td>
                //
                // result of posted data to server will be
                // prices [
                //      0 => [PER_SIZE: 20, PER_TYPE: STD, ...(depends on checked rules), PRICE: Rp. 200.000]
                //      1 => [PER_SIZE: 20, PER_TYPE: HC, ...(depends on checked rules), PRICE: Rp. 50.000]
                // ]
                var tablePriceBody = '';
                var counterRow = 0;
                buildPriceMap(checkedValues, function (data) {
                    // param data: result [index] each row of combination
                    // [x] PER_SIZE [x] PER_TYPE ---> [0][0] ---> [20][STD] ---> data
                    // [x] PER_SIZE [x] PER_TYPE ---> [0][1] ---> [20][HC] ----> next data
                    // [x] PER_SIZE [x] PER_TYPE ---> [0][2] ---> [20][OT] ----> next data again
                    // etc
                    var row = '<tr>';
                    for (var i = 0; i < data.length; i++) {
                        var valueLabel = priceAttributes[checkedValues[i]][data[i]];
                        var valueInput = valueLabel;

                        if (checkedValues[i] === 'PER_VOLUME' || checkedValues[i] === 'PER_TONNAGE') {
                            var resultLabel = / \d* /g.exec(valueLabel);
                            if (resultLabel.length) {
                                valueInput = resultLabel[0].trim();
                            }
                        }
                        else if (checkedValues[i] === 'PER_UNIT') {
                            // exception for this, replace with select unit
                            valueLabel = $('#row-unit-template').html().replace(/prices\[\]/g, 'prices[' + counterRow + ']');
                        }

                        var hiddenInput = "<input type='hidden' name='prices[" + counterRow + "][" + checkedValues[i] + "]' value='" + valueInput + "'>"
                        if (checkedValues[i] === 'PER_UNIT') {
                            hiddenInput = ''; // replaced with select unit above, so we don't need hidden field to hold the value
                        }

                        row += '<td>' + valueLabel + ' ' + hiddenInput + '</td>';
                    }
                    row += '<td><input type="text" class="form-control currency" placeholder="Price value" name="prices[' + counterRow + '][PRICE]" required></td>';
                    row += '</tr>';

                    tablePriceBody += row;
                    counterRow++;
                });
                var tablePrice = tableWrapper + tableHead + tablePriceHead + tableHeadClose + tableBody + tablePriceBody + tableBodyClose + tableWrapperClose;

                fieldPrice.append(tablePrice);
                reinitializeSelect2Library();
            }
        } else {
            // we don't need combination price table, show single price table
            priceActivity.show();
            priceActivity.find('input').removeAttr('disabled');
        }
    });

});