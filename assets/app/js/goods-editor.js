$(function () {

    const modalGoodsEditor = $('#modal-goods-editor');
    const modalGoodsInput = $('#modal-goods-input');

    modalGoodsInput.find('.btn-create-goods').on('click', function (e) {
        e.preventDefault();

        modalGoodsEditor.find('.modal-title').text('Create Goods');
        modalGoodsEditor.find('#btn-submit')
            .addClass('btn-primary')
            .removeClass('btn-warning')
            .text('Create New Goods');

        modalGoodsEditor.find('input[name=id]').val('');
        modalGoodsEditor.find('#customer').val('').trigger("change");
        modalGoodsEditor.find('#no_hs').val('');
        modalGoodsEditor.find('#no_goods').val('');
        modalGoodsEditor.find('#name').val('');
        modalGoodsEditor.find('#shrink_tolerance').val('');
        modalGoodsEditor.find('#whey_number').val('');
        modalGoodsEditor.find('#type_goods').val('');
        modalGoodsEditor.find('#unit_weight').val('');
        modalGoodsEditor.find('#unit_gross_weight').val('');
        modalGoodsEditor.find('#unit_length').val('');
        modalGoodsEditor.find('#unit_width').val('');
        modalGoodsEditor.find('#unit_height').val('');
        modalGoodsEditor.find('#unit_volume').val('');
        modalGoodsEditor.find('#description').val('');
        modalGoodsEditor.modal({
            backdrop: 'static',
            keyboard: false
        });
        modalGoodsEditor.find('.alert').hide();
    });

    modalGoodsInput.find('.btn-edit-goods').on('click', function (e) {
        e.preventDefault();

        modalGoodsEditor.find('.modal-title').text('Edit Goods');
        modalGoodsEditor.find('#btn-submit')
            .addClass('btn-warning')
            .removeClass('btn-primary')
            .text('Update Goods');

        const goodsId = modalGoodsInput.find('#goods').val();
        if (goodsId) {
            modalGoodsEditor.find('input[name=id]').val(goodsId);
            $.ajax({
                type: 'GET',
                url: baseUrl + "goods/ajax-get-goods",
                data: {id: goodsId},
                success: function (data) {
                    if (data) {
                        modalGoodsEditor.find('#customer').html(
                            $('<option>', {value: data.id_customer}).text(`${data.customer_name}`)
                        ).trigger('change');
                        modalGoodsEditor.find('#no_hs').val(data.no_hs);
                        modalGoodsEditor.find('#no_goods').val(data.no_goods);
                        modalGoodsEditor.find('#name').val(data.name);
                        modalGoodsEditor.find('#shrink_tolerance').val(data.shrink_tolerance);
                        modalGoodsEditor.find('#whey_number').val(data.whey_number);
                        modalGoodsEditor.find('#type_goods').val(data.type_goods);
                        modalGoodsEditor.find('#unit_weight').val(setNumeric(data.unit_weight));
                        modalGoodsEditor.find('#unit_gross_weight').val(setNumeric(data.unit_gross_weight));
                        modalGoodsEditor.find('#unit_length').val(setNumeric(data.unit_length));
                        modalGoodsEditor.find('#unit_width').val(setNumeric(data.unit_width));
                        modalGoodsEditor.find('#unit_height').val(setNumeric(data.unit_height));
                        modalGoodsEditor.find('#unit_volume').val(setNumeric(data.unit_volume));
                        modalGoodsEditor.find('#description').val(data.description);
                    } else {
                        alert('Goods not found');
                        modalGoodsEditor.modal('hide');
                    }
                }
            });

            modalGoodsEditor.modal({
                backdrop: 'static',
                keyboard: false
            });
            modalGoodsEditor.find('.alert').hide();
        } else {
            alert('Please select goods first');
        }
    });

    modalGoodsEditor.find('#btn-submit').on('click', function (e) {
        e.preventDefault();

        const buttonSubmit = $(this);
        buttonSubmit.attr('disabled', true);

        $.ajax({
            type: 'POST',
            url: baseUrl + "goods/ajax-save",
            data: modalGoodsEditor.find('form').serialize(),
            success: function (data) {
                modalGoodsEditor.find('.alert').show();
                if (data.status === 'success') {
                    modalGoodsEditor.find('.alert').addClass('alert-success').removeClass('alert-danger');
                    modalGoodsEditor.find('.messages').html(data.message);
                    setTimeout(function () {
                        modalGoodsEditor.modal('hide');
                        buttonSubmit.attr('disabled', false);

                        if (modalGoodsInput.length) {
                            modalGoodsInput.find('#goods').data('data', data.goods);
                            modalGoodsInput.find('#goods').html(
                                $('<option>', {value: data.goods.id}).text(`${data.goods.name}`)
                            ).trigger('change');
                        }
                    }, 500);
                } else {
                    modalGoodsEditor.find('.alert').addClass('alert-danger').removeClass('alert-success');
                    modalGoodsEditor.find('.messages').html(data.message);
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