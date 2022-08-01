$(function () {
    var modalValidate = $('#modal-validate');
    var buttonValidate = modalValidate.find('[data-submit]');
    var buttonDismiss = modalValidate.find('[data-dismiss]');
    var form = modalValidate.find('form');
    const formViewInvestigation = $('#form-view-investigation,#form-view-response');
    const btnApproval = formViewInvestigation.find('.btn-approval');
    const formViewComplain = $('#form-view-complain');
    const btnConclusion = formViewComplain.find('.btn-conclusion');
    const btnDisprove = formViewComplain.find('.btn-disprove');
    const btnFinal = formViewComplain.find('.btn-final');
    const btnFinalResponse = formViewComplain.find('.btn-final-response');

    const tableComplain = $('#table-complain');
    const btnRating = tableComplain.find('.btn-rating');

    buttonValidate.on('click', function () {
        form.submit();
    });

    $(document).on('click', '.btn-validate', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        if (!url) {
            url = $(this).attr('href');
        }
        form.attr('action', url);
        modalValidate.find('.validate-title').text($(this).data('title'));
        modalValidate.find('.validate-label').text($(this).data('label'));

        modalValidate.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    buttonDismiss.on('click', function () {
        form.attr('action', '#');
        form.find('.validate-title').text('');
        form.find('.validate-label').text('');
    });


    $('.department').on('change', function () {
         $.ajax({
            url: baseUrl + 'complain/ajax_get_department_detail_by_department_name',
            type: 'GET',
            data: {
                department : $(this).val()
            },
            success: function (data) {
                if(data != null){
                    if(data.email_pic != null){
                        $('.form-complain').find('#email-pic').val(data.email_pic);
                    }else{
                        $('.form-complain').find('#email-pic').val("");
                    }
                }else{
                    $('.form-complain').find('#email-pic').val("");
                }
            }
        });
    });

    $(document).on('click', '.btn-upload', function (e) {
        e.preventDefault();

        var id_goods = null;
        var label = $(this).closest('.btn-upload').data('label');
        var urlCheck = $(this).attr('href');

        var modalCheckOut = $('#modal-complain');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.find('#complain-title').text(label);

        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    }); 

     $('#modal-complain').find('#form-upload-complain').on('click', '.btn-save-complain', function (e) {
        e.preventDefault();

        var modalCheckOut = $('#modal-complain');
        var attachment = $('#modal-complain').find('#attachment').val();
        var urlCheck = $(this).attr('href');

        if(attachment === null || attachment === ""){
            alert("Please fill out this field !");
        }else{
            modalCheckOut.find('form').submit();
        }
    }); 

    btnApproval.on('click', function (e) {
        e.preventDefault();

        let approval = $(this).data('approval');
        let label = $(this).data('label');
        let id = $(this).data('id');

        let modal = $('#modal-approval');
        modal.find('.approval-title').text(label);
        modal.find('#id_complain').val(id);
        modal.find('#approval').val(approval);
        modal.find('button[type="submit"]').html(label);
        if(approval=='APPROVE'){
            modal.find('button[type="submit"]').addClass('btn-success');
        }else{
            modal.find('button[type="submit"]').addClass('btn-danger');
        }

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    }); 

    btnConclusion.on('click', function (e) {
        e.preventDefault();
        console.log('tes');
        let id = $(this).data('id');

        let modal = $('#modal-conclusion');
        modal.find('#id_complain').val(id);

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    }); 

    btnDisprove.on('click', function (e) {
        e.preventDefault();

        let disprove = $(this).data('disprove');
        let label = $(this).data('label');
        let id = $(this).data('id');

        let modal = $('#modal-disprove');
        modal.find('.disprove-title').text(label);
        modal.find('#id_complain').val(id);
        modal.find('#disprove').val(disprove);
        modal.find('button[type="submit"]').html(label);
        if(disprove=='DISPROVE'){
            modal.find('button[type="submit"]').addClass('btn-warning');
            modal.find('#label-note').html('Reason');
            modal.find('#note').prop('required',true);
            $('#rating-wrapper').hide();
            $('#note-wrapper').show();
            $('#rating-wrapper').find('#rate_very_good').prop('required',false);
            $('#rating-wrapper').find('#reason').prop('required',false);
        }else{
            modal.find('button[type="submit"]').addClass('btn-success');
            modal.find('#label-note').html('Message');
            modal.find('#note').prop('required',false);
            $('#note-wrapper').hide();
            $('#rating-wrapper').show();
            $('#rating-wrapper').find('#rate_very_good').prop('required',true);
            $('#rating-wrapper').find('#reason').prop('required',true);
        }

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    let modalRating = $('#modal-rating');
    btnRating.on('click', function (e) {
        e.preventDefault();

        const url = $(this).attr('href') || $(this).data('url');
        const rating = $(this).data('rating') || 0;
        const ratingReason = $(this).data('rating-reason');

        modalRating.find('form').attr('action', url);
        modalRating.find('[name=rating]').iCheck('uncheck');
        modalRating.find('[name=rating][value=' + rating + ']').iCheck('check');
        modalRating.find('[name=reason]').val(ratingReason);

        modalRating.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    const modalSetFinal = $('#modal-set-final');
    btnFinal.on('click', function(e) {
        e.preventDefault();

        modalSetFinal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    const modalFinalResponse = $('#modal-final-response');
    btnFinalResponse.on('click', function(e) {
        e.preventDefault();

        let url = $(this).data('url');
        modalFinalResponse.find('form').prop('action', url);

        modalFinalResponse.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

});