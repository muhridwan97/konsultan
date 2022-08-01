$(function () {
    $('#filter-summary').on('click', function (e) {
        e.preventDefault();
        let summary = $('#summary-body');
        let fromSummary = $('#form-summary');
        let month = fromSummary.find('[name="doc_month"]').children("option:selected").val();
        let year = fromSummary.find('[name="doc_year"]').children("option:selected").val();
        mlist = [ "","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
        
        summary.find('#tanggal_summary').text('on '+mlist[month]+' '+year);
        summary.find('#average').text("-");

        $.post(baseUrl + 'report/summary_report_compliance', {month: month, year: year})
        .done(function (data) {
            if (data === false) {
                alert('Generate pallet failed');
            } else {
                summary.find('#average').text(data);
            }
        })
        .fail(function () {
            alert("Something went wrong");
        })
    });
    $('#filter-summary').click();

    $('#filter-summary-draft').on('click', function (e) {
        e.preventDefault();
        let summary = $('#summary-body-draft');
        let fromSummary = $('#form-summary-draft');
        let month = fromSummary.find('[name="doc_month"]').children("option:selected").val();
        let year = fromSummary.find('[name="doc_year"]').children("option:selected").val();
        mlist = [ "","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
        
        summary.find('#tanggal_summary').text('on '+mlist[month]+' '+year);
        summary.find('#average').text("-");

        $.post(baseUrl + 'report/summary_report_compliance_draft', {month: month, year: year})
        .done(function (data) {
            if (data === false) {
                alert('Generate pallet failed');
            } else {
                summary.find('#average').text(data);
            }
        })
        .fail(function () {
            alert("Something went wrong");
        })
    });
    $('#filter-summary-draft').click();
});