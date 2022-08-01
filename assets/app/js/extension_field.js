$(function () {
    var fieldTextTemplate = $('#option-text-template').html();
    var fieldNumberTemplate = $('#option-number-template').html();
    var fieldMultiTemplate = $('#option-multi-template').html();
    var rowMultiTemplate = $('#row-multi-template').html();
    var optionWrapper = $('#option-wrapper');

    function setTextOption() {
        optionWrapper.find('.box-body').html(fieldTextTemplate);
    }

    function setNumberOption() {
        optionWrapper.find('.box-body').html(fieldNumberTemplate);
    }

    function setMultiOption() {
        optionWrapper.find('.box-body').html(fieldMultiTemplate);
    }

    function setDateOption() {
        optionWrapper.find('.box-body').html('No additional setting');
    }

    function switchFields(mode) {
        optionWrapper.show();
        switch (mode) {
            case 'SHORT TEXT':
            case 'LONG TEXT':
            case 'EMAIL':
                setTextOption();
                break;
            case 'DATE':
            case 'DATE TIME':
                setDateOption();
                break;
            case 'NUMBER':
                setNumberOption();
                break;
            case 'CHECKBOX':
                setMultiOption();
                break;
            case 'RADIO':
                setMultiOption();
                break;
            case 'SELECT':
                setMultiOption();
                break;
            default:
                break;
        }
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    }

    $('.select-extension-type').on('change', function () {
        switchFields($(this).val())
    });

    if ($('#extension-create').length) {
        if ($('.select-extension-type').val() != '') {
            switchFields($('.select-extension-type').val())
        }
    }

    optionWrapper.on('click', '.btn-remove-field', function () {
        $(this).closest('.row').remove();
    });

    optionWrapper.on('click', '#btn-add-field', function () {
        $('#fields-wrapper').append(rowMultiTemplate);
    });

});