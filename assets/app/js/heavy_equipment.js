$(function () {
    const formFilter = $('#filter_heavy_equipment');
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
                console.log(data);
                data.forEach(function(data){
                    if (type=='INTERNAL') {
                        formFilter.find('#heavy_equipment').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.name)); 
                    } else {
                        formFilter.find('#heavy_equipment').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.name+" - "+data.no_requisition+" - "+moment(data.created_at).format("DD MMMM YYYY")));                         
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
    formFilter.find("#period").on('change', function(){
        var period = $(this).val();
        var date = new Date();
        var firstDay = new Date(date.getFullYear(),period-1,1);
        var lastDay = new Date(date.getFullYear(),period,0);
        firstDay = moment(firstDay).format("DD MMMM YYYY");
        lastDay = moment(lastDay).format("DD MMMM YYYY");
        formFilter.find('#date_from').val(firstDay.toString());
        formFilter.find('#date_to').val(lastDay.toString());
    });
});