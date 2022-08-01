$(function () {

    var tableGoodsAssembly = $('#table-assembly-goods');
    var totalColumns = tableGoodsAssembly.first().find('th').length;
    var tableDetailGoodsAssembly = $('#table-assembly-goods tbody');
    var assemblyGoodsDetailTemplate = $('#row-assembly-goods-item-template').html();
    var selectAssemblyGoods = $('#id_goods');

    var dataAssemblyGoods = {};

    $(document).on('click', '.btn-add-record-assembly-goods', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailGoodsAssembly.empty();
        }

        var idAssemblyGoods = selectAssemblyGoods.select2('data')[0].id;
        var noGoods = selectAssemblyGoods.select2('data')[0].text;
        var compoGoods = idAssemblyGoods + "_" + noGoods;
        if (!(compoGoods in dataAssemblyGoods)) {
            dataAssemblyGoods[compoGoods] = selectAssemblyGoods.select2('data')[0];
        }

        if(selectAssemblyGoods.find('option:selected').text() == $("#goods").val()){
            alert("please, select another item !");
        }else{
            var assemblyGoodsRowTemplate = assemblyGoodsDetailTemplate
                .replace(/{{goods_name}}/g, selectAssemblyGoods.find('option:selected').text())
                .replace(/{{id_goods}}/g, dataAssemblyGoods[compoGoods].id)
            tableDetailGoodsAssembly.append(assemblyGoodsRowTemplate);              
            reorderItem();
        }


        tableGoodsAssembly.find('input.qty-assembly-goods').on('keyup', function (e) {
            e.preventDefault();

            // skip for arrow keys
              if(event.which >= 37 && event.which <= 40) return;

              // format number
              $(this).val(function(index, value) {

                if(value == 0){
                    return value
                    .replace(/[0]/g, ""); //bernilai 0
                }else{
                    return value
                    .replace(/\D/g, "") // selain huruf
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "."); 
                }
              });

        });

    });

    function getTotalItem() {
        return parseInt(tableGoodsAssembly.find('tr.row-assembly-goods-item').length);
    }

    function reorderItem() {
        tableDetailGoodsAssembly.find('tr.row-assembly-goods-item').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        reinitializeSelect2Library();
    }

    tableGoodsAssembly.on('click', '.btn-delete-assembly-goods-detail', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();

        reorderItem();
    });

    tableGoodsAssembly.find('input.qty-assembly-goods').on('keyup', function (e) {
        e.preventDefault();

        // skip for arrow keys
          if(event.which >= 37 && event.which <= 40) return;

          // format number
          $(this).val(function(index, value) {

            if(value == 0){
                return value
                .replace(/[0]/g, ""); //bernilai 0
            }else{
                return value
                .replace(/\D/g, "") // selain huruf
                .replace(/\B(?=(\d{3})+(?!\d))/g, "."); 
            }
          });

    });

});