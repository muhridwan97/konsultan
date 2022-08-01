$(function () {
    var modalCheckToken = $('#modal-check-token');
    var targetAction = null;
    modalCheckToken.find('.btn-check').on('click', function () {
        modalCheckToken.find('.btn-check').text('Checking...').attr('disabled', true);
        $.ajax({
            type: "GET",
            url: baseUrl + "token/ajax_check_token",
            data: {
                token: modalCheckToken.find('#token').val(),
                permission: (targetAction == null) ? '' : $(targetAction).data('permission')
            },
            success: function (data) {
                modalCheckToken.find('.btn-check').text('Check Token').attr('disabled', false).blur();
                if (targetAction == null) {
                    alert('Target action is null, contact your administrator');
                } else {
                    if (data.status == 'success') {
                        if (data.is_authorized) {
                            modalCheckToken.modal('hide');
                            setTimeout(function () {
                                $('[data-permission=' + $(targetAction).data('permission') + ']').attr('data-authorized', true);
                                $(targetAction).click();
                            }, 300);
                        } else {
                            modalCheckToken.find('.token-field-group').addClass('has-error');
                            modalCheckToken.find('.token-field-group .help-block').text(data.token_owner.name + ' do not have permission ' + data.permission);
                        }
                    } else {
                        modalCheckToken.find('.token-field-group').addClass('has-error');
                        modalCheckToken.find('.token-field-group .help-block').text(data.message);
                    }
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

    document.body.addEventListener('click', function (e) {
        if (e.target.getAttribute('data-authorized') == 'false') {
            e.preventDefault();
            e.stopPropagation();
            targetAction = e.target;

            modalCheckToken.find('.token-field-group').removeClass('has-error');
            modalCheckToken.find('.token-field-group .help-block').text('');
            modalCheckToken.modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    }, true);
});