$(function () {
    var formTargetBranch = $('#form-target-branch');
    var totalColumns = formTargetBranch.first().find('th').length;
    var tableDetailInputTargetBranch = $('#table-detail-target-branch tbody');
    var buttonAddTargetBranch = formTargetBranch.find('#btn-add-target-branch');
    var targetBranchTemplate = $('#row-target-branch-template').html();

    buttonAddTargetBranch.on('click', function (e) {
        e.preventDefault();

        if (getTotalItem() == 0) {
            tableDetailInputTargetBranch.empty();
        }

        tableDetailInputTargetBranch.append(targetBranchTemplate);
        reorderItem();
    });

    tableDetailInputTargetBranch.on('click', '.btn-remove-target-branch', function (e) {
        e.preventDefault();

        $(this).closest('tr').remove();
        reorderItem();
        if (getTotalItem() == 0) {
            tableDetailInputTargetBranch.append(
                $('<tr>').append($('<td>', {
                    colspan: totalColumns,
                    class: 'text-center'
                }).html('Click <strong>Add New Target Branch</strong> to insert new record'))
            );
        }
    });

    function reorderItem() {
        tableDetailInputTargetBranch.find('tr').each(function (index) {
            $(this).children('td').first().html(index + 1);
        });
        $('.select2').select2();
    }

    function getTotalItem() {
        return parseInt(tableDetailInputTargetBranch.find('tr.row-target-branch').length);
    }
});