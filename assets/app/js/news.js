$(function () {
    var tableNews = $('#table-news');
    var controlTemplate = $('#control-news-template').html();

    tableNews.DataTable({
        language: {searchPlaceholder: "Search news"},
        serverSide: true,
        ajax: baseUrl + 'news/data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'title'},
            {data: 'content'},
            {data: 'type'},
            {data: 'is_sticky'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: 1,
            render: function (data, type, full, meta) {
                return "<a href='" + baseUrl + "news/view/" + full.id + "'>" + data + "</a>";
            }
        }, {
            targets: 2,
            render: function (data, type, full, meta) {
                return data.substring(0, 150) + '...';
            }
        }, {
            targets: 4,
            render: function (data, type, full, meta) {
                var status = {
                    0: {label: 'default', class: 'primary'},
                    1: {label: 'sticky', class: 'success'}
                };
                return "<span class='label label-" + status[data].class + "'>" + status[data].label.toUpperCase() + "</span>";
            }
        }, {
            targets: -1,
            data: 'id',
            render: function (data, type, full, meta) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{title}}/g, full.title);
            }
        }]
    });
});