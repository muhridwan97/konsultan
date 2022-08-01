$(function () {
    $('#document_subtype').on('change', function () {
        var document_subtype = $(this).val();

        $('.expired_date').show();
        $('#expired_date').val("").trigger("change");
        $('#expired_date').attr("required", true);

        if(document_subtype == "SOC"){
            $('.freetime_date').hide();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", false);
        }
        if(document_subtype == "COC"){
            $('.freetime_date').show();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", true);
        }

        if(document_subtype == "LCL" || document_subtype == ""){
            $('.freetime_date').hide();
            $('#freetime_date').val("").trigger("change");
            $('#freetime_date').attr("required", false);

            $('.expired_date').hide();
            $('#expired_date').val("").trigger("change");
            $('#expired_date').attr("required", false);
        }
    });
});


