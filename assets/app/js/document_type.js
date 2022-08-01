$(function () {

    if (($('.reminder').find('#reminder').is(":checked"))) {
        $('.document-reminder').show();
        $('.reminder-overdue').show();
        $('.expired-reminder').show();
    }else{
        $('.document-reminder').hide();
        $('.reminder-overdue').hide();
        $('.expired-reminder').hide();
    }

    $('.reminder').find('#reminder').on('ifChanged', function () {

        if ($(this).is(":checked")) { 
            $('.reminder-overdue').show();
            $('.document-reminder').show();
            $('.expired-reminder').show();
            $('.document-reminder').find('#reminder_document').attr('required', true);
            $('.document-reminder').find('#upload_document').attr('required', true);
            $('.reminder-overdue').find('#reminder_overdue_day').attr('required', true);
        }else{
            $('.reminder-overdue').hide();
            $('.document-reminder').hide();
            $('.expired-reminder').hide();
            $('.document-reminder').find('#reminder_document').attr('required', false);
            $('.document-reminder').find('#upload_document').attr('required', false);
            $('.reminder-overdue').find('#reminder_overdue_day').attr('required', false);
        }
      
    });  

    $('.document-reminder').find('#reminder_document').val() != '' && $('.document-reminder').find('#upload_document').val() != '' ? 
    $('.document-reminder').find('#upload_document').attr('disabled', false) : $('.document-reminder').find('#upload_document').attr('disabled', true);
    
    $('.document-reminder').find('#reminder_document').on('change', function () {
        var reminder_document = $(this).val();
        $('.document-reminder').find('#upload_document').val('').trigger("change");
        $('.document-reminder').find('#upload_document').attr('disabled', false);
        $('#upload_document').find("option").removeAttr('disabled');
        $('#upload_document').find("option[value='"+reminder_document+"']").attr("disabled",true);
        $('.document-reminder').find('#upload_document').select2();
    });

    console.log($('.expired-reminder').find('#is_expired').val())
    if ($('.expired-reminder').find('#is_expired').val() == '1') {
        $('.active-day-expired').show();
        $('.active-day-expired').find('#active_day').prop('required',true);
    }else{
        $('.active-day-expired').hide();
        $('.active-day-expired').find('#active_day').prop('required',false);
    }

    $('.expired-reminder').find('#is_expired').on('change', function () {
        var isExpired = $(this).val();
        $('.active-day-expired').find('#active_day').val('').trigger("change");
        if (isExpired == '1') {
            $('.active-day-expired').show();
            $('.active-day-expired').find('#active_day').prop('required',true);
        } else {
            $('.active-day-expired').hide();
            $('.active-day-expired').find('#active_day').prop('required',false);
        }
    });
});