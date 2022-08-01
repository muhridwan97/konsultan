$(function () {
    var modalValidation = $('#modal-validation');
    var fieldValidationMessage = modalValidation.find('#field-validation-message');
    var form = modalValidation.find('form');

    $(document).on('click', '.btn-validate', function (e) {
        e.preventDefault();

        if ($(this).parent().hasClass('disabled') || $(this).hasClass('disabled')) {
            return;
        }

        var withMessage = $(this).data('with-message');
        if(typeof withMessage === 'undefined') {
            withMessage = true;
        }

        var url = $(this).data('url');
        if (!url) {
            url = $(this).attr('href');
        }
        var type = $(this).data('validate');
        var theme = $(this).data('theme');

        form.attr('action', url);
        modalValidation.find('.modal-title').text($(this).data('validate-title') || 'Validation');
        modalValidation.find('.validate-type').text($(this).data('validate'));
        modalValidation.find('.validate-label').html(decodeURIComponent($(this).data('label')));
        modalValidation.find('[type=submit]').text(type.charAt(0).toUpperCase() + type.substr(1));

        if (!withMessage) {
            fieldValidationMessage.val('').hide();
        } else {
            fieldValidationMessage.show();
        }

        modalValidation.find('button[type=submit]')
            .removeClass('btn-danger')
            .removeClass('btn-success')
            .removeClass('btn-primary');

        switch (type.toLowerCase()) {
            case 'approve':
            case 'validate':
            case 'release':
                modalValidation.find('button[type=submit]').addClass('btn-success');
                break;
            case 'warning':
            case 'revise':
                modalValidation.find('button[type=submit]').addClass('btn-warning');
                break;
            case 'reject':
            case 'cancel':
            case 'invalid':
            case 'error':
            case 'skip':
            case 'hold':
                modalValidation.find('button[type=submit]').addClass('btn-danger');
                break;
            default:
                modalValidation.find('button[type=submit]').addClass('btn-primary');
                break;
        }

        if (theme) {
            modalValidation.find('button[type=submit]').addClass('btn-' + theme);
        }

        modalValidation.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
