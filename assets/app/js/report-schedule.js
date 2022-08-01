$(function () {
    const formReportSchedule = $('#form-report-schedule');
    const selectRecurringPeriod = formReportSchedule.find('#recurring-period');

    selectRecurringPeriod.on('change', setPeriodControl);
    setPeriodControl(null, false);

    function setPeriodControl(e, reset = true) {
        if (reset) {
            formReportSchedule.find('#triggered_at').val('').prop('disabled', true);
            formReportSchedule.find('#triggered_month').val('').trigger('change').prop('disabled', true);
            formReportSchedule.find('#triggered_date').val('').trigger('change').prop('disabled', true);
            formReportSchedule.find('#triggered_day').val('').trigger('change').prop('disabled', true);
            formReportSchedule.find('#triggered_time').val('').trigger('change').prop('disabled', true);
        }

        switch (selectRecurringPeriod.val()) {
            case 'ONE TIME':
                formReportSchedule.find('#triggered_at').prop('disabled', false);
                formReportSchedule.find('#triggered_time').prop('disabled', false);
                break;
            case 'DAILY':
                formReportSchedule.find('#triggered_time').prop('disabled', false);
                break;
            case 'WEEKLY':
                formReportSchedule.find('#triggered_day').prop('disabled', false);
                formReportSchedule.find('#triggered_time').prop('disabled', false);
                break;
            case 'MONTHLY':
                formReportSchedule.find('#triggered_date').prop('disabled', false);
                formReportSchedule.find('#triggered_time').prop('disabled', false);
                break;
            case 'ANNUAL':
                formReportSchedule.find('#triggered_month').prop('disabled', false);
                formReportSchedule.find('#triggered_date').prop('disabled', false);
                formReportSchedule.find('#triggered_time').prop('disabled', false);
                break;
        }
    }
});