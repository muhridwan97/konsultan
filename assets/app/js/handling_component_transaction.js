$(function () {

    $('#table-component-transaction').on('click', '.btn-validate-component-transaction', function (e) {
        e.preventDefault();

        var idComponentTransaction = $(this).closest('.row-component-transaction').data('id');
        var labelComponentTransaction = $(this).closest('.row-component-transaction').data('label');
        var urlValidation = $(this).attr('href');

        var modalValidateComponentTransaction = $('#modal-validate-component-transaction');
        modalValidateComponentTransaction.find('form').attr('action', urlValidation);
        modalValidateComponentTransaction.find('input[name=id]').val(idComponentTransaction);
        modalValidateComponentTransaction.find('#payment-title').text(labelComponentTransaction);

        modalValidateComponentTransaction.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-component-transaction').on('click', '.btn-delete-component-transaction', function (e) {
        e.preventDefault();

        var idComponentTransaction = $(this).closest('.row-component-transaction').data('id');
        var labelComponentTransaction = $(this).closest('.row-component-transaction').data('label');
        var urlDelete = $(this).attr('href');

        var modalDeleteComponentTransaction = $('#modal-delete-component-transaction');
        modalDeleteComponentTransaction.find('form').attr('action', urlDelete);
        modalDeleteComponentTransaction.find('input[id]').val(idComponentTransaction);
        modalDeleteComponentTransaction.find('#component-transaction-title').text(labelComponentTransaction);

        modalDeleteComponentTransaction.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#table-component-transaction').on('click', '.btn-void-component-transaction', function (e) {
        e.preventDefault();

        var idComponentTransaction = $(this).closest('.row-component-transaction').data('id');
        var labelComponentTransaction = $(this).closest('.row-component-transaction').data('label');
        var urlDelete = $(this).attr('href');

        var modalVoidComponentTransaction = $('#modal-void-component-transaction');
        modalVoidComponentTransaction.find('form').attr('action', urlDelete);
        modalVoidComponentTransaction.find('input[id]').val(idComponentTransaction);
        modalVoidComponentTransaction.find('#component-transaction-title').text(labelComponentTransaction);

        modalVoidComponentTransaction.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

});