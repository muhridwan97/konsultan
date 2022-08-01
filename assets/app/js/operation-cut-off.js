$(function () {
    const formOperationCutOff = $('#form-operation-cut-off');
    const selectBranch = formOperationCutOff.find('#branch');
    const inputShift = formOperationCutOff.find('#shift');
    const inputStart = formOperationCutOff.find('#start');

    selectBranch.on('change', function () {
        const option = $(this).find('option:selected');

        const nextShift = option.data('next-shift');
        const nextStart = option.data('next-start');

        if (nextShift) {
            inputShift.prop('readonly', true);
        } else {
            inputShift.prop('readonly', false);
        }

        if (nextStart) {
            inputStart.prop('readonly', true);
        } else {
            inputStart.prop('readonly', false);
        }

        inputShift.val(nextShift);
        inputStart.val(nextStart);
    });

    if (selectBranch.val()) {
        selectBranch.trigger('change');
    }

});