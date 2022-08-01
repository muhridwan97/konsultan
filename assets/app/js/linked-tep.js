$(function () {
    const formLinkedTEP = $('#form-linked-tep');
    const selectCustomer = formLinkedTEP.find('#customer');
    const selectLinkedTep = formLinkedTEP.find('#linked_tep');

    selectCustomer.on('change', function () {
        const customerId = $(this).val();
        selectLinkedTep
            .empty()
            .append($('<option>'))
            .prop('disabled', true)
            .data("placeholder", "Fetching data...")
            .select2();
        $.ajax({
            type: 'GET',
            url: `${baseUrl}linked-entry-permit/get-tep-reference-customer/${customerId}`,
            success: function (data) {
                console.log(data);
                if (data) {
                    data.forEach(row => {
                        selectLinkedTep.append(
                            $('<option>', {value: row.id_tep})
                                .text(`${row.tep_code} (${row.created_at} : ${row.branch} - ${row.customer_name}`)
                        );
                    });
                }
                selectLinkedTep
                    .prop('disabled', false)
                    .data("placeholder", "Select linked tep")
                    .select2();
            },
            error: function () {
                alert("Error fetching reference data");
            }
        });
    });

    if (selectCustomer.val()) {
        selectCustomer.trigger('change');
    }
});