$(function () {

    const modalContainerEditor = $('#modal-container-editor');
    const modalContainerInput = $('#modal-container-input');

    $('.btn-create-container').on('click', function (e) {
        e.preventDefault();

        modalContainerEditor.find('.modal-title').text('Create Goods');
        modalContainerEditor.find('#btn-submit')
            .addClass('btn-primary')
            .removeClass('btn-warning')
            .text('Create New Container');

        modalContainerEditor.find('input[name=id]').val('');
        modalContainerEditor.find('#no_container').val('');
        modalContainerEditor.find('#shipping_line').val('').trigger("change");
        modalContainerEditor.find('#type').val('').trigger("change");
        modalContainerEditor.find('#size').val('').trigger("change");
        modalContainerEditor.find('#description').val('');
        modalContainerEditor.modal({
            backdrop: 'static',
            keyboard: false
        });
        modalContainerEditor.find('.alert').hide();
    });

    modalContainerInput.find('.btn-edit-container').on('click', function (e) {
        e.preventDefault();

        modalContainerEditor.find('.modal-title').text('Edit Container');
        modalContainerEditor.find('#btn-submit')
            .addClass('btn-warning')
            .removeClass('btn-primary')
            .text('Update Container');

        const containerId = modalContainerInput.find('#no_container').val();
        if (containerId) {
            modalContainerEditor.find('input[name=id]').val(containerId);
            $.ajax({
                type: 'GET',
                url: baseUrl + "container/ajax-get-container",
                data: {id: containerId},
                success: function (data) {
                    if (data) {
                        modalContainerEditor.find('#shipping_line').html(
                            $('<option>', {value: data.id_shipping_line}).text(`${data.shipping_line}`)
                        ).trigger('change');
                        modalContainerEditor.find('#no_container').val(data.no_container);
                        modalContainerEditor.find('#type').val(data.type).trigger("change");
                        modalContainerEditor.find('#size').val(data.size).trigger("change");
                        modalContainerEditor.find('#description').val(data.description);
                    } else {
                        alert('Container not found');
                        modalContainerEditor.modal('hide');
                    }
                }
            });

            modalContainerEditor.modal({
                backdrop: 'static',
                keyboard: false
            });
            modalContainerEditor.find('.alert').hide();
        } else {
            alert('Please select goods first');
        }
    });

    modalContainerEditor.find('#btn-submit').on('click', function (e) {
        e.preventDefault();

        const buttonSubmit = $(this);
        buttonSubmit.attr('disabled', true);

        $.ajax({
            type: 'POST',
            url: baseUrl + "container/ajax_save",
            data: modalContainerEditor.find('form').serialize(),
            success: function (data) {
                console.log(JSON.stringify(data, null, 2));
                modalContainerEditor.find('.alert').show();
                if (data.status === 'success') {
                    modalContainerEditor.find('.alert').addClass('alert-success').removeClass('alert-danger');
                    modalContainerEditor.find('.messages').html(data.message);
                    setTimeout(function () {
                        modalContainerEditor.modal('hide');
                        buttonSubmit.attr('disabled', false);

                        if (modalContainerInput.length) {
                            modalContainerInput.find('#no_container').data('data', data.container);
                            modalContainerInput.find('#no_container').html(
                                $('<option>', {value: data.container.id}).text(`${data.container.no_container} - ${data.container.size}`)
                            ).trigger('change');
                        }
                    }, 500);
                } else {
                    modalContainerEditor.find('.alert').addClass('alert-danger').removeClass('alert-success');
                    modalContainerEditor.find('.messages').html(data.message);
                    buttonSubmit.attr('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                buttonSubmit.attr('disabled', false);
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
    });

});