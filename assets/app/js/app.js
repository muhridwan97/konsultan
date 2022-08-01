$(function () {
    $('.table').not('.table-scroll').not('.no-datatable').not('.table-ajax').DataTable({
        language: {searchPlaceholder: "Search data"},
        pageLength: 25
    });

    $('.table.table-scroll').not('.no-datatable').not('.table-ajax').DataTable({
        language: {searchPlaceholder: "Search data"},
        pageLength: 25,
        scrollX: true,
    });

    $.extend(true, $.fn.dataTable.defaults, {
        initComplete: function(settings, json) {
            setTableViewport();
        },
        drawCallback: function () {
            setTableViewport();
        }
    });

    setTableViewport();
    window.onresize = function() {
        setTableViewport();
    };

    function checkOnTouchSubmit(form) {
        var buttonSubmit = $(form).find('[data-toggle=one-touch]');
        if (buttonSubmit.length) {
            var message = buttonSubmit.data('touch-message');
            if (message == undefined) {
                message = 'Submitted...';
            }
            $('span#loader').css('display', 'block');
            if ($(window).width() > 768 ) {
                $('p#loader').text('Loading, please wait...');
            }
            buttonSubmit.attr('disabled', true).html(message);
        }
    }

    $('form').on('submit', function () {
        if ($(this).hasClass('need-validation')) {
            if($(this).valid()) {
                checkOnTouchSubmit($(this));
            }
        } else {
            checkOnTouchSubmit($(this));
        }
        return true;
    });

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

    $('[data-toggle="tooltip"]').tooltip({container: 'body', trigger: "hover"});

    reinitializeDateLibrary();

    reinitializeSelect2Library();

    $('#btn-copy-to-clipboard').on('click', function (e) {
        e.preventDefault();
        copyToClipboard($('#copy-to-clipboard-target').text());
    });

    $('[data-clickable=true]').on('click', function () {
        var url = $(this).data('url');
        window.location.href = url;
    });

    $(document).on('input', '.input-uppercase', function () {
        $(this).val($(this).val().toUpperCase());
    });

    $(document).on('keyup', '.currency', function () {
        var value = $(this).val();
        $(this).val(setCurrencyValue(value, 'Rp. '));
        if($(this).val() === 'Rp. ') {
            $(this).val('');
        }
    });

    $(document).on('keyup', '.numeric', function () {
        var value = $(this).val();
        $(this).val(setCurrencyValue(value, ''));
    });

    $.validator.setDefaults({
        submitHandler: function (form) {
            return true;
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        ignore: ":hidden, .ignore-validation",
        errorElement: "span",
        errorClass: "help-block",
        errorPlacement: function (error, element) {
            if (element.hasClass('select2') || element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.next('span.select2'));
            }
            else if (element.parent(".input-group").length) {
                error.insertAfter(element.parent());
            }
            else {
                error.insertAfter(element);
            }
        }
    });
    const forms = $('.need-validation');
    forms.each(function (index, form) {
        $(form).validate();
    });
});

const setTableViewport = function() {
    // screen.width
    if ($(window).width() > 768 ) {
        $('table.responsive .responsive-label').remove();
        $('table.responsive td').find('.dropdown').css('display', '');
        $('table.responsive td').css('text-align', '');
    }
    else {
        $('table.responsive').each(function(i, table) {
            let head = [];
            $(table).find('>thead th').each(function(i, th) {
                head.push($(th).text());
            });
            $(table).find('tbody > tr').each(function(i, tr) {
                if($(tr).find('td .responsive-label').length === 0) {
                    if($(tr).find('td:visible').length === head.length) {
                        $(tr).find('td:visible').each(function(i, td) {
                            $(td).prepend(`<span class="responsive-label">${head[i]}</span>`);
                            $(td).css('maxWidth', '');
                            $(td).find('input').css('maxWidth', '');
                            $(td).css('text-align', 'left');
                        });
                        $(tr).find('.dropdown').css('display', 'inline-block');
                    }
                }
            });
            if ($(table).hasClass('table-ajax') && $(table).hasClass('responsive')) {
                $(table).css('width', 'auto');
            }
        });
    }
};

function template(data) {
    if (!data.id || !data.template) {
        return data.text;
    }

    if (data.template === 'handling-shifting') {
        // alert(JSON.stringify(data));
        var controlTemplate = $('#control-shifting-record-template').html();
        return $(controlTemplate
            .replace(/{{quantity}}/g, numberFormat(data.quantity, 2, ',', '.'))
            .replace(/{{container_goods_name}}/g, data.text)
            .replace(/{{no_reference}}/g, data.no_reference)
            .replace(/{{last_position}}/g, data.last_position)
            .replace(/{{customer_name}}/g, data.customer_name));
    }
    return data.text;
}

function reinitializeSelect2Library() {
    $(".select2").select2();

    var select2Self = null;
    $(".select2.select2-ajax").select2({
        ajax: {
            url: function (params) {
                select2Self = $(this);
                return $(this).data('url');
            },
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                var query = {
                    q: params.term,
                    page: params.page || 1
                };
                var addParams = select2Self.data('params');
                if (addParams) {
                    addParams = addParams.split(',');
                    addParams.forEach(function (value) {
                        var param = value.split('=');
                        query[param[0]] = param[1]
                    });
                }
                return query;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                var dataResult = $.map(data.results, function (item) {
                    var sublabel = '';
                    if (select2Self.data('key-sublabel') && item[select2Self.data('key-sublabel')]) {
                        sublabel = ' - ' + item[select2Self.data('key-sublabel')];
                    }
                    var sublabel2 = '';
                    if (select2Self.data('key-sublabel2') && item[select2Self.data('key-sublabel2')]) {
                        sublabel2 = ' - ' + item[select2Self.data('key-sublabel2')];
                    }

                    /*
                    return {
                        id: item[select2Self.data('key-id')],
                        text: item[select2Self.data('key-label')] + sublabel
                    }
                    */

                    item.id = item[select2Self.data('key-id')];
                    item.text = item[select2Self.data('key-label')] + sublabel + sublabel2;
                    return item;
                });

                if (select2Self.data('template') != undefined) {
                    if (select2Self.data('template') === 'handling-shifting') {
                        dataResult = $.map(data.results, function (item) {
                            item.template = select2Self.data('template');
                            item.id = item[select2Self.data('key-id')];
                            item.text = item[select2Self.data('key-label')];

                            return item;
                        });
                    } else {
                        // other data template
                    }
                }

                if (select2Self.data('add-empty-value') != undefined) {
                    if (select2Self.data('empty-added') !== 1 || params.page === 1) {
                        dataResult.unshift({
                            id: select2Self.data('empty-value') || '0',
                            text: select2Self.data('add-empty-value')
                        });
                        select2Self.data('empty-added', 1);
                    }
                }

                if (select2Self.data('add-all-customer') == true) {
                    if (select2Self.data('all-added') !== 1 || params.page === 1) {
                        dataResult.unshift({
                            id: '0',
                            text: 'ALL CUSTOMER'
                        });
                        select2Self.data('all-added', 1);
                    }
                }

                return {
                    results: dataResult,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            }
        },
        templateResult: template
    });
}

function reinitializeDateLibrary() {
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd MM yyyy',
    });
    $('.timepicker').timepicker({
        showInputs: false,
        showMeridian: false,
        defaultTime: false,
        minuteStep: 1,
        minTime: '10:15:00',
    });

    $('.datepicker-today').datepicker({
        autoclose: true,
        format: 'dd MM yyyy',
        startDate: "today"
    });

    if (typeof dateRangePickerSettings !== 'undefined') {
        $('.daterangepicker2').daterangepicker(dateRangePickerSettings);
    } else {
        $('.daterangepicker2').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: false,
            startDate: moment().format('DD MMMM YYYY HH:mm:ss'),
            locale: {
                format: 'DD MMMM YYYY HH:mm:ss'
            }
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
    }

    $('.time-picker').timepicker({
        showSeconds: false,
        showMeridian: false,
        defaultTime: false,
        minuteStep: 5,
    });
}

function openWindowReload(link, e) {
    e.preventDefault();
    var href = link.href;
    if (href.substring((href.length - 1), href.length) == "#") {
        var modalPrintError = $('#modal-print-error');
        modalPrintError.modal({
            backdrop: 'static',
            keyboard: false
        });
    } else {
        window.open(href, '_blank');
        document.location.reload(true);
    }
}

function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData("Text", text);

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            var result = document.execCommand("copy");  // Security exception may be thrown by some browsers.
            alert('"' + text + '" copied to clipboard!');
            return result;
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

function setCurrencyValue(value, prefix, ths = '.', dec = ',', thsTarget = '.', decTarget = ',') {
    var pattern = new RegExp("[^" + dec + "\\\d]", 'g');
    var number_string = value.toString().replace(pattern, '').toString(),
        splitDecimal = number_string.split(dec),
        groupThousand = splitDecimal[0].length % 3,
        currency = splitDecimal[0].substr(0, groupThousand),
        thousands = splitDecimal[0].substr(groupThousand).match(/\d{3}/gi);
    if (thousands) {
        var separator = groupThousand ? thsTarget : '';
        currency += separator + thousands.join(thsTarget);
    }
    currency = splitDecimal[1] != undefined ? currency + decTarget + splitDecimal[1] : currency;
    return prefix + (value < 0 ? '-' : '') + currency;
}

function setNumeric(value, prefix = '', ths = ',', dec = '.', thsTarget = '.', decTarget = ',') {
    const trimmedValue = (+value).toFixed(9).replace(/([0-9]+(\.[0-9]+[1-9])?)(\.?0+$)/,'$1');
    return setCurrencyValue(trimmedValue || 0, prefix, ths, dec);
}

function getCurrencyValue(value, returnAsString = false) {
    var val = value.toString().replace(/[^0-9,\-]/g, '').replace(/,/, '.');

    if (returnAsString) {
        return val;
    }

    return Number(val) || 0;
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function searchTable(input, tableId, column) {
    // Declare variables
    var filter, table, tr, td, i;
    filter = input.value.toUpperCase().trim();
    table = document.getElementById(tableId);
    tr = $(table).find('tbody tr').not('#placeholder').not('.skip-ordering');

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[column];
        if (td) {
            if (td.innerHTML.toUpperCase().trim().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function getAllUrlParams(url) {

    // get query string from url (optional) or window
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    // we'll store the parameters here
    var obj = {};

    // if query string exists
    if (queryString) {

        // stuff after # is not part of query string, so get rid of it
        queryString = queryString.split('#')[0];

        // split our query string into its component parts
        var arr = queryString.split('&');

        for (var i = 0; i < arr.length; i++) {
            // separate the keys and the values
            var a = arr[i].split('=');

            // in case params look like: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function (v) {
                paramNum = v.slice(1, -1);
                return '';
            });

            // set parameter value (use 'true' if empty)
            var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

            // (optional) keep case consistent
            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();

            // if parameter name already exists
            if (obj[paramName]) {
                // convert value to array (if still string)
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // if no array index number specified...
                if (typeof paramNum === 'undefined') {
                    // put the value on the end of the array
                    obj[paramName].push(paramValue);
                }
                // if array index number specified...
                else {
                    // put the value at that index number
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // if param name doesn't exist yet, set it
            else {
                obj[paramName] = paramValue;
            }
        }
    }

    return obj;
}

function numberFormat(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

$(function ($) {
    var body = $('body');
    // On click, capture state and save it in localStorage
    $('.sidebar-toggle').click(function () {
        localStorage.setItem('tci-sidebar', body.hasClass('sidebar-collapse') ? 1 : 0);
    });

    /* replaced with head script
    // On ready, read the set state and collapse if needed
    if (localStorage.getItem('tci-sidebar') === '0') {
        body.addClass('disable-animations sidebar-collapse');
        requestAnimationFrame(function () {
            body.removeClass('disable-animations');
        });
    }
    */
});


function initializeLoaderStock(formStock) {
    formStock.on('click', '.btn-take-all', function () {
        var type = $(this).data('type');

        var tableSource = formStock.find('#source-' + type + '-wrapper');
        var tableDestination = formStock.find('#destination-' + type + '-wrapper');

        var rowSource = tableSource.find('tr[data-takeable=1]');
        rowSource.find('input[type=hidden]').attr('disabled', false);
        rowSource.find('#quantity').attr('type', 'number');
        rowSource.find('#quantity-label').addClass('hidden');
        rowSource.find('#tonnage').attr('type', 'number');
        rowSource.find('#tonnage-label').addClass('hidden');
        rowSource.find('#tonnage-gross').attr('type', 'number');
        rowSource.find('#tonnage-gross-label').addClass('hidden');
        rowSource.find('#volume').attr('type', 'number');
        rowSource.find('#volume-label').addClass('hidden');
        rowSource.find('.row-job').hide();
        tableDestination.append(rowSource);

        if (tableSource.find('tr[data-row]').length === 0) {
            tableSource.find('#placeholder').show();
        }
        if (tableDestination.find('tr[data-row]').length > 0) {
            tableDestination.find('#placeholder').hide();
        }

        tableDestination.find('.btn-take').text('Return').attr('class', 'btn btn-danger btn-block btn-return');
        $('#total_items').val(parseInt($('#total_items').val()) + rowSource.length);

        reorderTable(tableSource);
        reorderTable(tableDestination);
    });

    formStock.on('click', '.btn-take', function () {
        var type = $(this).data('type');
        var id = $(this).data('id');

        var tableSource = $(this).closest('#source-' + type + '-wrapper');
        var tableDestination = formStock.find('#destination-' + type + '-wrapper');

        var rowSource = tableSource.find('[data-row=' + id + ']');
        rowSource.find('input[type=hidden]').attr('disabled', false);
        rowSource.find('#quantity').attr('type', 'number');
        rowSource.find('#quantity-label').addClass('hidden');
        rowSource.find('#tonnage').attr('type', 'number');
        rowSource.find('#tonnage-label').addClass('hidden');
        rowSource.find('#tonnage-gross').attr('type', 'number');
        rowSource.find('#tonnage-gross-label').addClass('hidden');
        rowSource.find('#volume').attr('type', 'number');
        rowSource.find('#volume-label').addClass('hidden');
        rowSource.find('.row-job').hide();
        tableDestination.append(rowSource);

        if (tableSource.find('tr[data-row]').length === 0) {
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

    formStock.on('click', '.btn-return', function () {
        var type = $(this).data('type');
        var id = $(this).data('id');

        var tableDestination = $(this).closest('#destination-' + type + '-wrapper');
        var tableSource = formStock.find('#source-' + type + '-wrapper');

        var rowDestination = tableDestination.find('[data-row=' + id + ']');
        rowDestination.find('input[type=hidden]').attr('disabled', true);
        rowDestination.find('#quantity').attr('type', 'hidden');
        rowDestination.find('#quantity-label').removeClass('hidden');
        rowDestination.find('#tonnage').attr('type', 'hidden');
        rowDestination.find('#tonnage-label').removeClass('hidden');
        rowDestination.find('#tonnage-gross').attr('type', 'hidden');
        rowDestination.find('#tonnage-gross-label').removeClass('hidden');
        rowDestination.find('#volume').attr('type', 'hidden');
        rowDestination.find('#volume-label').removeClass('hidden');
        rowDestination.find('.row-job').show();
        tableSource.append(rowDestination);

        if (tableDestination.find('tr[data-row]').length === 0) {
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

    function reorderTable(table) {
        table.find('tr[data-row]').not('#placeholder').not('.skip-ordering')
            .each(function (index) {
                $(this).children('td').first().html(index + 1);
            });
    }
    
}
$(document).on('change', '.file-upload-default', function () {
    if (this.files && this.files[0]) {
        let maxFile = $(this).data('max-size');
        if (this.files[0].size > maxFile) {
            showAlert('File too large', 'Maximum file must be less than ' + (maxFile / 1000000) + 'MB');
        } else {
            $(this).closest('.form-group').find('.file-upload-info').val(this.files[0].name);
        }
    }
});

$(document).on('click', '.btn-simple-upload', function () {
    $(this).closest('.form-group').find('[type=file]').click();
});

//function used to generate unique number//
function uniqueId() {
    var ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var ID_LENGTH = 8;
    var rtn = '';
    for (var i = 0; i < ID_LENGTH; i++) {
        rtn += ALPHABET.charAt(Math.floor(Math.random() * ALPHABET.length));
    }
    return rtn;
}

var truncate = function (fullStr, strLen, separator) {
    if (fullStr.length <= strLen) return fullStr;

    separator = separator || '...';

    var sepLen = separator.length,
        charsToShow = strLen - sepLen,
        frontChars = Math.ceil(charsToShow/2),
        backChars = Math.floor(charsToShow/2);

    return fullStr.substr(0, frontChars) +
        separator +
        fullStr.substr(fullStr.length - backChars);
};