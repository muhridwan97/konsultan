$(function () {
    var tableRawContact = $('#table-raw-contact');
    var controlTemplate = $('#control-raw-contact-template').html();

    tableRawContact.DataTable({
        language: {searchPlaceholder: "Search raw contact"},
        serverSide: true,
        ajax: baseUrl + 'raw_contact/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'company'},
            {data: 'pic'},
            {data: 'address'},
            {data: 'contact'},
            {data: 'email'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{company}}/g, full.company);
            }
        }]
    });
});