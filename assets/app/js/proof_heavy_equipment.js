$(function () {
    const formFilter = $('#filter_proof');
    const formEditPrint = $('#form-edit-print');
    formFilter.find("#type").on('change', function(){
        var type = $(this).val();
        formFilter.find('#heavy_equipment').children('option').remove();
        formFilter.find('#heavy_equipment').data("placeholder", "Fetching data...");
        formFilter.find('#heavy_equipment').select2();
        if (type=='INTERNAL') {
            var urlTo = 'heavy_equipment/ajax_get_heavy_equipment';
        } else {
            var urlTo = 'heavy_equipment_entry_permit/ajax_get_heep_all';
        }
        $.ajax({
            url: baseUrl + urlTo,
            type: 'POST',
            data: {
            },
            success: function (data) {
                data.forEach(function(data){
                    if (type=='INTERNAL') {
                        formFilter.find('#heavy_equipment').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.name)); 
                    } else {
                        var request_title = data.request_title;
                        formFilter.find('#heavy_equipment').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.no_requisition+" - "+request_title.substring(0, 20)));                         
                    }
                  });
                if (data == '') {
                    formFilter.find('#heavy_equipment').data("placeholder", "No data...");
                    formFilter.find('#heavy_equipment').select2();
                }
            },
            error: function() { 
                alert("gagal memperoleh heavy equipment");
            }  
        });
    });

    formFilter.find("#proof-print").on('click', function(){
        var type = formFilter.find("#type").val();
        var id_reference = formFilter.find("#heavy_equipment").val();
        var date = formFilter.find("#date").val();
        var buttonSubmit = $(this);
        buttonSubmit.prop('disabled', true).html('Printing...');
        var dateInput = moment(date);
        var now = moment();
        if (dateInput>now) {
            alert('Please fill in the date now or past');
            buttonSubmit.prop('disabled', false).html('Print');
        }
        if (date=='') {
            alert('Please fill in the date');
            buttonSubmit.prop('disabled', false).html('Print');
        }
        if (type=='INTERNAL') {
            var urlTo = 'proof-heavy-equipment/ajax_get_heavy_equipment_internal';
        } else {
            var urlTo = 'proof-heavy-equipment/ajax_get_heavy_equipment_external';
        }
        $.ajax({
            url: baseUrl + urlTo,
            type: 'POST',
            data: {
                id_reference : id_reference,
                date : date,
            },
            success: function (data) {
                if (data[0].name_heavy_equipment == null) {
                    alert('You cant print, because no data available');
                    buttonSubmit.prop('disabled', false).html('Print');
                }else{
                    window.location.replace(baseUrl+'proof-heavy-equipment/proof_print?type='+type+'&heavy_equipment='+id_reference+'&date='+date+'');
                }
            },
            error: function() { 
                alert("eror contact your administrator");
            }  
        });
    });

    formFilter.find("#edit-print").on('click', function(){
        var type = formFilter.find("#type").val();
        var id_reference = formFilter.find("#heavy_equipment").val();
        var customer = formFilter.find("#customer").val();
        var date = formFilter.find("#date").val();
        var buttonSubmit = $(this);
        buttonSubmit.prop('disabled', true).html('Printing...');
        var dateInput = moment(date);
        var now = moment();
        if (dateInput>now) {
            alert('Please fill in the date now or past');
            buttonSubmit.prop('disabled', false).html('Print');
        }
        if (date=='') {
            alert('Please fill in the date');
            buttonSubmit.prop('disabled', false).html('Print');
        }
        if (type=='INTERNAL') {
            var urlTo = 'proof-heavy-equipment/ajax_get_heavy_equipment_internal';
        } else {
            var urlTo = 'proof-heavy-equipment/ajax_get_heavy_equipment_external';
        }
        $.ajax({
            url: baseUrl + urlTo,
            type: 'POST',
            data: {
                id_reference : id_reference,
                date : date,
                customer : customer,
            },
            success: function (data) {
                if (data.length != 0) {
                    if (data[0].name_heavy_equipment == null) {
                        alert('You cant print, because no data available');
                        buttonSubmit.prop('disabled', false).html('Print');
                    }else{
                        window.location.replace(baseUrl+'proof-heavy-equipment/edit_print?type='+type+'&heavy_equipment='+id_reference+'&date='+date+'&customer='+customer);
                    } 
                }else{
                    alert('You cant print, because no data available');
                    buttonSubmit.prop('disabled', false).html('Print');
                }
                
            },
            error: function() { 
                alert("eror contact your administrator");
            }  
        });
    });
    formEditPrint.find("#date").on('change', function(){
        var date = $(this).val();
        var d = new Date(date);
        var dayNames = ["SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"];
        $("#hari").val(dayNames[d.getDay()]);
    });
});