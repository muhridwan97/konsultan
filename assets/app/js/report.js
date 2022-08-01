$(function () {

    // General function, collapse and show filter report
    // the key is href target of button as reference which filter would be collapse or show.
    // another simple way is using data-collapse bootstrap feature, but we need modify button text and reinitialize libs.
    var btnFilterToggle = $('.btn-filter-toggle');
    btnFilterToggle.on('click', function (e) {
        e.preventDefault();
        var targetFilter = $(this).attr('href');
        if ($(targetFilter).is(':visible')) {
            $(this).text('Show Filter');
            $(targetFilter).hide(200)
        } else {
            $(this).text('Hide Filter');
            $(targetFilter).show(200, function () {
                reinitializeSelect2Library();
            });
        }
    });

    //filter locked tally
    var btnFilterLockToggle = $('.btn-lock-toggle');
    btnFilterLockToggle.on('click', function (e) {
        e.preventDefault();
        var targetFilter = $(this).attr('href');
        if ($(targetFilter).is(':visible')) {
            $(this).text('Show Lock Menu');
            $(targetFilter).hide(200)
        } else {
            $(this).text('Hide Lock Menu');
            $(targetFilter).show(200, function () {
                reinitializeSelect2Library();
            });
        }
    });

    // reset filter value
    $(document).on('click', '.btn-reset-filter', function () {
        $('.form-filter .select2').val('').trigger("change");
        $('.form-filter input').val('');
    });

    if ($('#barChartContainer').length) {
        // activity report
        var areaChartData = {
            labels: chartLabel,
            datasets: [
                {
                    label: (mainData == 'inbound') ? 'Outbound' : 'Inbound',
                    fillColor: 'rgba(210, 214, 222, 1)',
                    strokeColor: 'rgba(210, 214, 222, 1)',
                    data: (mainData == 'inbound') ? charDataOut : charDataIn
                },
                {
                    label: (mainData == 'inbound') ? 'Inbound' : 'Outbound',
                    fillColor: 'rgba(60,141,188,0.9)',
                    strokeColor: 'rgba(60,141,188,0.8)',
                    data: (mainData == 'inbound') ? charDataIn : charDataOut
                }
            ]
        }

        var barChartCanvas = $('#barChartContainer').get(0).getContext('2d')
        var barChart = new Chart(barChartCanvas)
        var barChartData = areaChartData
        barChartData.datasets[1].fillColor = '#00a65a'
        barChartData.datasets[1].strokeColor = '#00a65a'
        barChartData.datasets[1].pointColor = '#00a65a'
        var barChartOptions = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Number - Spacing between each of the X value sets
            barValueSpacing: 10,
            //Boolean - whether to make the chart responsive
            responsive: true,
            maintainAspectRatio: true
        }
        barChart.Bar(barChartData, barChartOptions);
    }

    if ($('#barChartGoods').length) {
        // activity report
        var areaChartData = {
            labels: chartGoodsLabel,
            datasets: [
                {
                    label: (mainGoodsData == 'inbound') ? 'Outbound' : 'Inbound',
                    fillColor: 'rgba(210, 214, 222, 1)',
                    strokeColor: 'rgba(210, 214, 222, 1)',
                    data: (mainGoodsData == 'inbound') ? charGoodsDataOut : charGoodsDataIn
                },
                {
                    label: (mainGoodsData == 'inbound') ? 'Inbound' : 'Outbound',
                    fillColor: 'rgba(60,141,188,0.9)',
                    strokeColor: 'rgba(60,141,188,0.8)',
                    data: (mainGoodsData == 'inbound') ? charGoodsDataIn : charGoodsDataOut
                }
            ]
        }

        var barChartCanvas = $('#barChartGoods').get(0).getContext('2d')
        var barChart = new Chart(barChartCanvas)
        var barChartData = areaChartData
        barChartData.datasets[1].fillColor = '#00a65a'
        barChartData.datasets[1].strokeColor = '#00a65a'
        barChartData.datasets[1].pointColor = '#00a65a'
        var barChartOptions = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Number - Spacing between each of the X value sets
            barValueSpacing: 10,
            //Boolean - whether to make the chart responsive
            responsive: true,
            maintainAspectRatio: true
        }
        barChart.Bar(barChartData, barChartOptions);
    }

    if ($('#barChartServiceTimeIn').length) {
        // activity report
        var areaChartData = {
            labels: chartLabelIn,
            datasets: [
                {
                    label: 'Queue',
                    data: charDataQueueIn
                },
                {
                    label: 'Trucking',
                    data: charDataTruckingIn
                },
                {
                    label: 'Gate',
                    data: charDataGateIn
                },
                {
                    label: 'Tally',
                    data: charDataTallyIn
                }
            ]
        }

        var barChartCanvas = $('#barChartServiceTimeIn').get(0).getContext('2d')
        var barChart = new Chart(barChartCanvas)
        var barChartData = areaChartData
        barChartData.datasets[1].fillColor = '#00a65a'
        barChartData.datasets[1].strokeColor = '#00a65a'
        barChartData.datasets[2].fillColor = '#a22917'
        barChartData.datasets[2].strokeColor = '#a22917'
        barChartData.datasets[3].fillColor = '#39abfb'
        barChartData.datasets[3].strokeColor = '#39abfb'
        var barChartOptions = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Number - Spacing between each of the X value sets
            barValueSpacing: 10,
            //Boolean - whether to make the chart responsive
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                yAxes: [{
                    type: "time",
                    time: {
                        unit: 'minute',
                        round: 'minute',
                        displayFormats: {
                            'second': 'h:mm:ss',
                            'minute': 'h:mm:ss',
                        }
                    }
                }]
            }
        }
        barChart.Bar(barChartData, barChartOptions);
    }

    if ($('#barChartServiceTimeOut').length) {
        // activity report
        var areaChartData = {
            labels: chartLabelOut,
            datasets: [
                {
                    label: 'Queue',
                    data: charDataQueueOut
                },
                {
                    label: 'Trucking',
                    data: charDataTruckingOut
                },
                {
                    label: 'Gate',
                    data: charDataGateOut
                },
                {
                    label: 'Tally',
                    data: charDataTallyOut
                }
            ]
        }

        var barChartCanvas = $('#barChartServiceTimeOut').get(0).getContext('2d')
        var barChart = new Chart(barChartCanvas)
        var barChartData = areaChartData
        barChartData.datasets[1].fillColor = '#00a65a'
        barChartData.datasets[1].strokeColor = '#00a65a'
        barChartData.datasets[2].fillColor = '#a22917'
        barChartData.datasets[2].strokeColor = '#a22917'
        barChartData.datasets[3].fillColor = '#39abfb'
        barChartData.datasets[3].strokeColor = '#39abfb'
        var barChartOptions = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Number - Spacing between each of the X value sets
            barValueSpacing: 10,
            //Boolean - whether to make the chart responsive
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                yAxes: [{
                    type: "time",
                    time: {
                        unit: 'minute',
                        round: 'minute',
                        displayFormats: {
                            'second': 'h:mm:ss',
                            'minute': 'h:mm:ss',
                        }
                    }
                }]
            }
        };
        barChart.Bar(barChartData, barChartOptions);
    }

    var queryString = window.location.search.slice(1);

    $('#table-inbound-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl + 'report/activity_container_data/INBOUND?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'booking_type'},
            {data: 'no_booking_in'},  
            {data: 'no_reference_in'},
            {data: 'reference_date_in'},
            {data: 'booking_type_in'},  
            {data: 'booking_date'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner_name'},
            {data: 'no_safe_conduct'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'source_warehouse'},
            {data: 'no_handling'},
            {data: 'no_work_order'},
            {data: 'gate_in_date'},
            {data: 'gate_out_date'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'no_container'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'seal'},
            {data: 'position'},
            {data: 'is_empty'},
            {data: 'is_hold'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'container_description'}, 
        ],
        columnDefs: [{
            targets: ['hidden'],
            createdCell: function (td, cellData, rowData, row, col) {
                $(td).addClass('hidden');
            }
        }, {
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'booking/view/' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'safe-conduct/view/' + full.id_safe_conduct + '">' + data + '</a>';
            }
        }, {
            targets: ['type-handling'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'handling/view/' + full.id_handling + '">' + data + '</a>';
            }
        }, {
            targets: ['type-work-order'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'work-order/view/' + full.id_work_order + '">' + data + '</a>';
            }
        }, {
            targets: ['type-is-empty'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Empty' : 'Full';
            }
        }, {
            targets: ['type-is-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-inbound-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl + 'report/activity_goods_data/INBOUND?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'booking_type'},
            {data: 'no_booking_in'},
            {data: 'no_reference_in'},
            {data: 'reference_date_in'},
            {data: 'booking_type_in'},
            {data: 'booking_date'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner_name'},
            {data: 'no_safe_conduct'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'source_warehouse'},
            {data: 'no_handling'},
            {data: 'no_work_order'},
            {data: 'gate_in_date'},
            {data: 'gate_out_date'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'quantity'},
            {data: 'unit_weight'},
            {data: 'total_weight'},
            {data: 'unit_gross_weight'},
            {data: 'total_gross_weight'},
            {data: 'volume'},
            {data: 'total_volume'},
            {data: 'unit'},
            {data: 'position'},
            {data: 'ex_no_container'},
            {data: 'no_pallet'},
            {data: 'whey_number'},
            {data: 'is_hold'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'goods_description'},
        ],
        columnDefs: [{
            targets: ['hidden'],
            createdCell: function (td, cellData, rowData, row, col) {
                $(td).addClass('hidden');
            }
        }, {
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data, type, full, meta) {
                return setNumeric(data)
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'booking/view/' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'safe-conduct/view/' + full.id_safe_conduct + '">' + data + '</a>';
            }
        }, {
            targets: ['type-handling'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'handling/view/' + full.id_handling + '">' + data + '</a>';
            }
        }, {
            targets: ['type-work-order'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'work-order/view/' + full.id_work_order + '">' + data + '</a>';
            }
        }, {
            targets: ['type-is-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-outbound-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl + 'report/activity_container_data/OUTBOUND?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'no_invoice'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'booking_type'},
            {data: 'no_booking_in'},
            {data: 'no_reference_in'},
            {data: 'reference_date_in'},
            {data: 'booking_type_in'},
            {data: 'booking_date'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner_name'},
            {data: 'no_safe_conduct'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'source_warehouse'},
            {data: 'no_handling'},
            {data: 'no_work_order'},
            {data: 'gate_in_date'},
            {data: 'gate_out_date'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'no_container'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'seal'},
            {data: 'position'},
            {data: 'is_empty'},
            {data: 'is_hold'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'container_description'},
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'booking/view/' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-booking-in'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'booking/view/' + full.id_booking_in + '">' + data + '</a>';
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'safe-conduct/view/' + full.id_safe_conduct + '">' + data + '</a>';
            }
        }, {
            targets: ['type-handling'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'handling/view/' + full.id_handling + '">' + data + '</a>';
            }
        }, {
            targets: ['type-work-order'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'work-order/view/' + full.id_work_order + '">' + data + '</a>';
            }
        }, {
            targets: ['type-is-empty'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Empty' : 'Full';
            }
        }, {
            targets: ['type-is-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-outbound-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl + 'report/activity_goods_data/OUTBOUND?' + queryString,
            type: "POST",
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_registration'},
            {data: 'registration_date'},
            {data: 'no_invoice'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'reference_date'},
            {data: 'booking_type'},
            {data: 'no_booking_in'},
            {data: 'no_reference_in'},
            {data: 'reference_date_in'},
            {data: 'booking_type_in'},
            {data: 'booking_date'},
            {data: 'vessel'},
            {data: 'voyage'},
            {data: 'owner_name'},
            {data: 'no_safe_conduct'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'source_warehouse'},
            {data: 'no_handling'},
            {data: 'no_work_order'},
            {data: 'gate_in_date'},
            {data: 'gate_out_date'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'quantity'},
            {data: 'unit_weight'},
            {data: 'total_weight'},
            {data: 'unit_gross_weight'},
            {data: 'total_gross_weight'},
            {data: 'volume'},
            {data: 'total_volume'},
            {data: 'unit'},
            {data: 'position'},
            {data: 'ex_no_container'},
            {data: 'no_pallet'},
            {data: 'whey_number'},
            {data: 'is_hold'},
            {data: 'status'},
            {data: 'status_danger'},
            {data: 'goods_description'},
        ],
        columnDefs: [{
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-date-time'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
            }
        }, {
            targets: ['type-numeric'],
            render: function (data, type, full, meta) {
                return setNumeric(data)
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'booking/view/' + full.id_booking + '">' + data + '</a>';
            }
        }, {
            targets: ['type-safe-conduct'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'safe-conduct/view/' + full.id_safe_conduct + '">' + data + '</a>';
            }
        }, {
            targets: ['type-handling'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'handling/view/' + full.id_handling + '">' + data + '</a>';
            }
        }, {
            targets: ['type-work-order'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'work-order/view/' + full.id_work_order + '">' + data + '</a>';
            }
        }, {
            targets: ['type-is-hold'],
            render: function (data, type, full, meta) {
                return data == 1 ? 'Yes' : 'No';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

 $('#table-admin-site').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/admin_site_data/?' + queryString,
            "type": "POST"
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'doc_no'},
            {data: 'type'},
            {data: 'created_at'},
            {data: 'creator_name'},
            {data: 'customer_name'},
        ],
        columnDefs: [{
                targets: ['date'],
                render: function (data, type, full, meta) {
                    return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
                }
        }]
    });

    $('#table-pallet').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/pallet_data/?' + queryString,
            "type": "POST"
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_work_order'},
            {data: 'no_reference'},
            {data: 'completed_at'},
            {data: 'no_container'},
            {data: 'size'},
            {data: 'category'},
            {data: 'qty'},
            {data: 'stock_pallet'},
            {data: 'sisa_pallet'},
        ],
        columnDefs: [{
                targets: ['date'],
                render: function (data, type, full, meta) {
                    return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY H:mm');
                }
        },{
            targets: ['aju'],
            render: function (data, type, full, meta) {
                if ($.trim(data) != '') {
                    if ($.trim(full.category) == 'OUTBOUND') {
                        var aju_in = full.no_reference_in.substring(full.no_reference_in.length - 5);
                        var aju = data.substring(data.length - 5);
                        return "IN "+aju_in+" | "+"OUT "+aju;
                    }else{
                        var aju = data.substring(data.length - 5);
                        return "IN "+aju;
                    }
                }
                return "-";
            }
        },{
            targets: ['container1'],
            render: function (data, type, full, meta) {
                if ($.trim(data) == '') {
                    return data;
                }
                return data;
            }
        }]
    });

    $('#table-forklift').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/forklift_data/?' + queryString,
            "type": "POST"
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'minggu'},
            {data: 'own_frt_bln_jkt'},
            {data: 'all_frt_bln_jkt'},
            {data: 'target_jkt'},
            {data: 'own_frt_bln_mdn'},
            {data: 'all_frt_bln_mdn'},
            {data: 'target_mdn'},
            {data: 'own_frt_bln_sby'},
            {data: 'all_frt_bln_sby'},
            {data: 'target_sby'},
            {data: 'avg_frt'},
            {data: 'avg_all_frt'},
        ],
        columnDefs: [{
                targets: ['minggu'],
                render: function (data, type, full, meta) {
                    return $.trim(data) == '' ? '-' : full.tahun+"/"+(+data+ +1);
                }
        }, {
            targets: [2],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=1&tahun=${full.tahun}&minggu=${full.minggu}&ownership=owned">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [3],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=1&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [4],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [5],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=2&tahun=${full.tahun}&minggu=${full.minggu}&ownership=owned">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [6],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=2&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [7],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [8],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=5&tahun=${full.tahun}&minggu=${full.minggu}&ownership=owned">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [9],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=5&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [10],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }]
    });

    $('#table-performance').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/performance_data/?' + queryString,
            "type": "POST"
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'minggu'},
            {data: 'avg_frt_jkt'},
            {data: 'avg_ops_jkt'},
            {data: 'frt_ops_jkt'},
            {data: 'target_jkt'},
            {data: 'avg_ops_core_jkt'},
            {data: 'frt_ops_core_jkt'},
            {data: 'target_jkt_core'},
            {data: 'avg_frt_mdn'},
            {data: 'avg_ops_mdn'},
            {data: 'frt_ops_mdn'},
            {data: 'target_mdn'},
            {data: 'avg_ops_core_mdn'},
            {data: 'frt_ops_core_mdn'},
            {data: 'target_mdn_core'},
            {data: 'avg_frt_sby'},
            {data: 'avg_ops_sby'},
            {data: 'frt_ops_sby'},
            {data: 'target_sby'},
            {data: 'avg_ops_core_sby'},
            {data: 'frt_ops_core_sby'},
            {data: 'target_sby_core'},
            {data: 'avg_frt_tot'},
            {data: 'avg_ops_tot'},
        ],
        columnDefs: [{
                targets: ['minggu'],
                render: function (data, type, full, meta) {
                    return $.trim(data) == '' ? '-' : full.tahun+"/"+(+data+ +1);
                }
        }, {
            targets: [2],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=1&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [3],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=1&tahun=${full.tahun}&minggu=${full.minggu}">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [4],
            render: function (data, type, full) {
                if(full.target_jkt < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [5],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [6],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=1&tahun=${full.tahun}&minggu=${full.minggu}&is_core=true">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [7],
            render: function (data, type, full) {
                if(full.target_jkt_core < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [8],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [9],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=2&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [10],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=2&tahun=${full.tahun}&minggu=${full.minggu}">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [11],
            render: function (data, type, full) {
                if(full.target_mdn < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [12],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [13],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=2&tahun=${full.tahun}&minggu=${full.minggu}&is_core=true">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [14],
            render: function (data, type, full) {
                if(full.target_mdn_core < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [15],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [16],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/forklift-detail?branchId=5&tahun=${full.tahun}&minggu=${full.minggu}&ownership=all">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [17],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=5&tahun=${full.tahun}&minggu=${full.minggu}">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [18],
            render: function (data, type, full) {
                if(full.target_sby < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [19],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }, {
            targets: [20],
            render: function (data, type, full) {
                return `
                    <a href="${baseUrl}report/performance-detail?branchId=5&tahun=${full.tahun}&minggu=${full.minggu}&is_core=true">${$.trim(data) == '' ? '-' : data}</a>
                `;
            }
        }, {
            targets: [21],
            render: function (data, type, full) {
                if(full.target_sby_core < 100){
                    return `<span style="color:red">${data}</span>`;
                }
                return data;
            }
        }, {
            targets: [22],
            render: function (data, type, full) {
                if(data < 100){
                    return `<span style="color:red">${data} %</span>`;
                }
                return data + " %";
            }
        }]
    });

    $('#table-realization-container').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/realization_container_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'booking_date'},
            {data: 'category'},
            {data: 'no_container'},
            {data: 'type'},
            {data: 'size'},
            {data: 'position'},
            {data: 'total_pending_day'},
            {data: 'status_realization'},
        ],
        columnDefs: [{
            targets: 4,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: -2,
            render: function (data, type, full, meta) {
                return full.status_realization === 'REALIZED' ? 'COMPLETED' : data + ' days ago';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-realization-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/realization_goods_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'booking_date'},
            {data: 'category'},
            {data: 'no_goods'},
            {data: 'goods_name'},
            {data: 'unit'},
            {data: 'quantity'},
            {data: 'position'},
            {data: 'no_pallet'},
            {data: 'total_pending_day'},
            {data: 'status_realization'},
        ],
        columnDefs: [{
            targets: 4,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-quantity'],
            render: function (data, type, full, meta) {
                return numberFormat(data, 2, ',', '.') + ' / ' + numberFormat(full.quantity_realization, 2, ',', '.');
            }
        }, {
            targets: -2,
            render: function (data, type, full, meta) {
                return full.status_realization === 'REALIZED' ? 'COMPLETED' : data + ' days ago';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-booking-summary').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/booking_summary_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'owner_name'},
            {data: 'no_reference_inbound'},
            {data: 'booking_date_inbound'},
            {
                class: 'success',
                data: 'total_booking_container_inbound'
            },
            {
                class: 'success',
                data: 'total_booking_goods_inbound'
            },
            {data: 'first_date_inbound'},
            {data: 'last_date_inbound'},
            {data: 'total_container_inbound'},
            {data: 'total_goods_inbound'},
            {data: 'no_reference_outbound'},
            {data: 'booking_date_outbound'},
            {data: 'total_booking_container_outbound'},
            {data: 'total_booking_goods_outbound'},
            {data: 'first_date_outbound'},
            {data: 'last_date_outbound'},
            {
                class: 'danger',
                data: 'total_container_outbound'
            },
            {
                class: 'danger',
                data: 'total_goods_outbound'
            },
            {data: 'stock_container'},
            {data: 'stock_goods'}
        ],
        columnDefs: [{
            targets: ['type-numeric'],
            render: function (data) {
                return data && data !== 'null' ? setNumeric(data) : '-';
            }
        }, {
            targets: ['type-booking'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '' : data.replace(/,/g, '<br>');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-service-time-summary').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/service_time_summary_data?' + queryString,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'booking_type'},
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'no_invoice'},
            {data: 'no_bl'},
            {data: 'assigned_do'},
            {data: 'ata_date'},
            {data: 'do_date'},
            {data: 'st_do'},
            {data: 'sppb_date'},
            {data: 'tila_date'},
            {data: 'no_safe_conduct'},
            {data: 'police_number'},
            {data: 'driver'},
            {data: 'expedition_type'},
            {data: 'no_container'},
            {data: 'security_start'},
            {data: 'security_stop'},
            {data: 'st_inbound'},
            {data: 'st_trucking'},
        ],
        columnDefs: [{
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-service-time').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/service_time_data?' + queryString,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'no_booking'},
            {data: 'no_reference'},
            {data: 'handling_type'},
            {data: 'no_safe_conduct'},
            {data: 'driver'},
            {data: 'no_police'},
            {data: 'expedition'},
            {data: 'owner_name'},
            {data: 'no_container'},
            {data: 'container_type'},
            {data: 'container_size'},
            {data: 'security_in_date'},
            {data: 'security_out_date'},
            {data: 'trucking_service_time'},
            {data: 'tally_name'},
            {data: 'no_work_order'},
            {data: 'queue_duration'},
            {data: 'taken_at'},
            {data: 'completed_at'},
            {data: 'tally_service_time'},
            {data: 'gate_in_date'},
            {data: 'gate_out_date'},
            {data: 'gate_service_time'},
            {data: 'booking_date'},
            {data: 'booking_service_time'},
            {data: 'booking_service_time_days'},
        ],
        columnDefs: [{
            targets: -1,
            render: function (data, type, full, meta) {
                return numberFormat(data, 1, ',', '.') + ' days';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-service-time-monthly').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/service_time_monthly_data?' + queryString,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'year'},
            {data: 'month'},
            {data: 'booking_category'},
            {data: 'trucking_service_time'},
            {data: 'queue_duration'},
            {data: 'tally_service_time'},
            {data: 'gate_service_time'},
            {data: 'booking_service_time'},
            {data: 'booking_service_time_days'},
        ],
        columnDefs: [{
            targets: -1,
            render: function (data, type, full, meta) {
                return numberFormat(data, 1, ',', '.') + ' days';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-service-time-weekly').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/service_time_weekly_data?' + queryString,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'year'},
            {data: 'month'},
            {data: 'week'},
            {data: 'booking_category'},
            {data: 'trucking_service_time'},
            {data: 'queue_duration'},
            {data: 'tally_service_time'},
            {data: 'gate_service_time'},
            {data: 'booking_service_time'},
            {data: 'booking_service_time_days'},
        ],
        columnDefs: [{
            targets: -1,
            render: function (data, type, full, meta) {
                return numberFormat(data, 1, ',', '.') + ' days';
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-report-booking-rating').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'report/booking_rating_data?' + queryString,
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'customer_name'},
            {data: 'no_reference'},
            {data: 'sppb_date'},
            {data: 'completed_date'},
            {data: 'category'},
            {data: 'rated_at'},
            {data: 'rating'},
            {data: 'rating_description'},
        ],
        columnDefs: [{
            targets: 'type-rating',
            createdCell: function (td, cellData, rowData, row, col) {
                if (cellData == '0') {
                    $(td).addClass('danger');
                }
            }
        },{
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    $('#table-aging-goods').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl + 'report/stock_aging_goods_data?' + queryString,
            data: function (data) {
                data['order'].forEach(function (items, index) {
                    data['order'][index]['column'] = data['columns'][items.column]['data'];
                });
            }
        },
        pageLength: 25,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'goods_name'},
            {data: 'unit'},
            {data: 'age_today'},
            {data: 'age_1_30'},
            {data: 'age_31_60'},
            {data: 'age_61_90'},
            {data: 'age_more_than_90'},
            {data: 'age_filter'}
        ],
        columnDefs: [{
            targets: 'type-number',
            render: function (data) {
                return setNumeric(data);
            }
        }]
    });

    $('#table-invoice-summary').DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search data"
        },
        serverSide: true,
        processing: true,
        ajax: {
            "url": baseUrl + 'report/invoice_data?' + queryString,
            "type": "POST"
        },
        order: [[0, "desc"]],
        scrollX: true,
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {data: 'branch'},
            {data: 'invoice_date'},
            {data: 'no_reference'},
            {data: 'no_reference_booking'},
            {data: 'invoice_date'},
            {data: 'type'},
            {data: 'handling_type'},
            {data: 'no_invoice'},
            {data: 'no_faktur'},
            {data: 'customer_name'},
            {data: 'inbound_date'},
            {data: 'outbound_date'},
            {data: 'days'},
            {data: 'party'},
            {data: 'storage'},
            {data: 'lift_on_off'},
            {data: 'moving'},
            {data: 'moving_adjustment'},
            {data: 'seal'},
            {data: 'fcl_prioritas'},
            {data: 'fcl_behandle'},
            {data: 'ob_tps'},
            {data: 'non_ob_tps'},
            {data: ''},
            {data: 'admin_fee'},
            {data: 'dpp'},
            {data: 'tax'},
            {data: 'materai'},
            {data: 'total_price'},
            {data: 'payment_date'},
            {data: 'transfer_bank'},
            {data: 'transfer_amount'},
            {data: 'cash_amount'},
            {data: 'transfer_amount'},
            {data: 'over_payment_amount'},
            {data: 'payment_description'},
        ],
        columnDefs: [{
            targets: 2,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('YYYY');
            }
        }, {
            targets: 3,
            render: function (data, type, full, meta) {
                var referenceLink = '#';
                if (full.type === 'BOOKING FULL' || full.type === 'BOOKING FULL EXTENSION' || full.type === 'BOOKING PARTIAL') {
                    referenceLink = baseUrl + 'booking/view/' + full.id_booking;
                } else if (full.type === 'HANDLING') {
                    referenceLink = baseUrl + 'handling/view/' + full.id_handling;
                } else if (full.type === 'WORK ORDER') {
                    referenceLink = baseUrl + 'work-order/view/' + full.id_work_order;
                }
                return '<a href="' + referenceLink + '">' + data + '</a>';
            }
        }, {
            targets: 5,
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY HH:mm');
            }
        }, {
            targets: 7,
            render: function (data, type, full, meta) {
                if (full.handling_type != '' && full.handling_type != null) {
                    return full.handling_type;
                } else if (full.handling_type_job != '' && full.handling_type_job != null) {
                    return full.handling_type_job;
                } else {
                    return 'BOOKING';
                }
            }
        }, {
            targets: ['type-invoice'],
            render: function (data, type, full, meta) {
                return '<a href="' + baseUrl + 'invoice/view/' + full.id + '">' + data + '</a>';
            }
        }, {
            targets: ['type-date'],
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : moment(data).format('D MMMM YYYY');
            }
        }, {
            targets: ['type-currency'],
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat(data, 0, ',', '.');
            }
        }, {
            targets: ['type-currency-total'],
            render: function (data, type, full, meta) {
                return 'Rp. ' + numberFormat((parseInt(full.transfer_amount) + parseInt(full.cash_amount)), 0, ',', '.');
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    let table = {};
    $(document).on('click', '.green', function (e) {
        e.preventDefault();

        let no = 1;
        let url = 1;
        let isLate=$(this).data('type');
        let bound=$(this).data('bound');
        let color='green';
        let modal = $('#modal-filter-by-color');
        modal.find('form').attr('action', url.toString());
        modal.find('.modal-title').text('Data Report Success');
        modal.find('#job-title').text(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        
        if(table.destroy) {
            table.destroy();
        }
        $('#tanggal1').prop('hidden',true);
        $('#tanggal2').prop('hidden',true);
        $('#coloumn1').prop('hidden',true);
        $('#coloumn2').prop('hidden',true);
        if (isLate=='is_late_draft') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);
            modal.find('#tanggal1').text("upload date");
            modal.find('#tanggal2').text("draft date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'upload_date'},
                    {data: 'draft_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_confirmation') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);
            modal.find('#tanggal1').text("draft date");
            modal.find('#tanggal2').text("confirmation date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'draft_date'},
                    {data: 'confirmation_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_do') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("ATA date");
            modal.find('#tanggal2').text("DO date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'ata_date'},
                    {data: 'do_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_sppb' && bound=='inbound') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("DO date");
            modal.find('#tanggal2').text("SPPB date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'do_date'},
                    {data: 'sppb_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if(isLate=='is_late_sppb' && bound=='outbound'){
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("BPN date");
            modal.find('#tanggal2').text("SPPB date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'bpn_date'},
                    {data: 'sppb_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_security_inbound') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("SPPB date");
            modal.find('#tanggal2').text("Last in TCI");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'sppb_date'},
                    {data: 'last_in_tci'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_bongkar') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("Last in TCI");
            modal.find('#tanggal2').text("Last Unload / Stripping");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'last_in_tci_max'},
                    {data: 'bongkar_date_last'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_billing') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("Confirmation date");
            modal.find('#tanggal2').text("Billing date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'confirmation_date'},
                    {data: 'billing_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_payment') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("Billing date");
            modal.find('#tanggal2').text("BPN date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'billing_date'},
                    {data: 'bpn_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_booking_complete') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("SPPB / Sec_start");
            modal.find('#tanggal2').text("Muat date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'muat_internal_external_max'},
                    {data: 'booking_complete'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (false) {
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: null},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [4] },
                    { "bVisible": false, "aTargets": [5] }
                ]
            });
        }
        
    });

    $(document).on('click', '.red', function (e) {
        e.preventDefault();

        let no = 1;
        let url = 1;
        let isLate=$(this).data('type');
        let bound=$(this).data('bound');
        let color='red';

        let modal = $('#modal-filter-by-color');
        modal.find('form').attr('action', url.toString());
        modal.find('.modal-title').text('Data Report Failed');
        modal.find('#job-title').text(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });

        if(table.destroy) {
            table.destroy();
        }
        
        $('#tanggal1').prop('hidden',true);
        $('#tanggal2').prop('hidden',true);
        $('#coloumn1').prop('hidden',true);
        $('#coloumn2').prop('hidden',true);
        if (isLate=='is_late_bongkar') {
            $('#tanggal2').prop('hidden',false);
            $('#coloumn1').prop('hidden',false);
            $('#coloumn2').prop('hidden',false);
            modal.find('#tanggal2').text("no safe conduct");
            modal.find('#coloumn1').text("Last in TCI");
            modal.find('#coloumn2').text("Last Bongkar");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color + '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'no_safe_conduct'},
                    {data: 'last_in_tci'},
                    {data: 'last_bongkar_date'}
                ],
                columnDefs: [{
                        targets: '_all',
                        render: function (data) {
                            return data;
                        },
                    createdCell: function (td, cellData, rowData, row, col) {
                        // if (rowData.is_late_bongkar == null) {
                        //     $(td).addClass('warning');
                        // }else
                        // if (rowData.is_late_bongkar >= 1) {
                        //     $(td).addClass('danger');
                        // }else{
                        //     $(td).addClass('success');
                        // }
                        // dengan stripping
                        if (rowData.is_late_bongkar_last == null) {
                            $(td).addClass('warning');
                        }else
                        if (rowData.is_late_bongkar_last >= 1) {
                            $(td).addClass('danger');
                        }else{
                            $(td).addClass('success');
                        }
                    }
                }]
            });
        }
        if (isLate=='is_late_security_inbound') {
            $('#tanggal2').prop('hidden',false);
            $('#coloumn1').prop('hidden',false);
            $('#coloumn2').prop('hidden',false);
            modal.find('#tanggal2').text("no safe conduct");
            modal.find('#coloumn1').text("SPPB");
            modal.find('#coloumn2').text("date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color + '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'no_safe_conduct'},
                    {data: 'sppb_date'},
                    {data: 'last_in_tci'}
                ],
                columnDefs: [{
                        targets: '_all',
                        render: function (data) {
                            return data;
                        },
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (rowData.is_late_security_inbound == null) {
                            $(td).addClass('warning');
                        }else
                        if (rowData.is_late_security_inbound >= 1) {
                            $(td).addClass('danger');
                        }else{
                            $(td).addClass('success');
                        }
                    }
                }]
            });
        }
        if (isLate=='is_late_draft') {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);
            modal.find('#tanggal1').text("upload date");
            modal.find('#tanggal2').text("draft date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'upload_date'},
                    {data: 'draft_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_confirmation') {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);
            modal.find('#tanggal1').text("draft date");
            modal.find('#tanggal2').text("confirmation date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'draft_date'},
                    {data: 'confirmation_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_do') {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("ATA date");
            modal.find('#tanggal2').text("DO date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'ata_date'},
                    {data: 'do_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_sppb' && bound=='inbound') {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("DO date");
            modal.find('#tanggal2').text("SPPB date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'do_date'},
                    {data: 'sppb_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_sppb' && bound=='outbound') {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("BPN date");
            modal.find('#tanggal2').text("SPPB date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'bpn_date'},
                    {data: 'sppb_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_billing') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("Confirmation date");
            modal.find('#tanggal2').text("Billing date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'confirmation_date'},
                    {data: 'billing_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_payment') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("Billing date");
            modal.find('#tanggal2').text("BPN date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'billing_date'},
                    {data: 'bpn_date'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (isLate=='is_late_booking_complete') {
            $('#tanggal1').prop('hidden',false);
            $('#tanggal2').prop('hidden',false);

            modal.find('#tanggal1').text("SPPB / Sec_start");
            modal.find('#tanggal2').text("Muat date");
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'muat_internal_external_max'},
                    {data: 'booking_complete'},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [6] }
                ]
            });
        }
        if (false) {
            $('#coloumn1').prop('hidden',true);
            $('#coloumn2').prop('hidden',true);
            table = $('#table-filter-by-color').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color + '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: null},
                    {data: null}
                ],
                aoColumnDefs: [
                    { "bVisible": false, "aTargets": [4] },
                    { "bVisible": false, "aTargets": [5] }
                ]
            });
        }
        
    });

    $(document).on('click', '.yellow', function (e) {
        e.preventDefault();

        let no = 1;
        let url = 1;
        let isLate=$(this).data('type');
        let bound=$(this).data('bound');
        let color='yellow';

        let modal = $('#modal-filter-by-color-yellow');
        modal.find('form').attr('action', url.toString());
        modal.find('.modal-title').text('Data Report Pending');
        modal.find('#job-title').text(no.toString());

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });

        if(table.destroy) {
            table.destroy();
        }
        $('#kolom2').prop('hidden',false);
        if (isLate=='is_late_draft') {
            modal.find('#kolom1').text("Upload");
            modal.find('#kolom2').text("Draft");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'upload_date'},
                    {data: 'draft_date'}
                ]
            });
        }
        if (isLate=='is_late_confirmation') {
            modal.find('#kolom1').text("Draft");
            modal.find('#kolom2').text("Confirm");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'draft_date'},
                    {data: 'confirmation_date'}
                ]
            });
        }
        if (isLate=='is_late_do') {
            modal.find('#kolom1').text("ATA");
            modal.find('#kolom2').text("DO date");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'ata_date'},
                    {data: 'do_date'}
                ]
            });
        }
        if (isLate=='is_late_sppb' && bound=='inbound') {
            modal.find('#kolom1').text("DO");
            modal.find('#kolom2').text("SPPB");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'do_date'},
                    {data: 'sppb_date'}
                ]
            });
        }
        if (isLate=='is_late_security_inbound') {
            modal.find('#kolom1').text("SPPB");
            modal.find('#kolom2').text("LAST IN TCI");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+ '/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'sppb_date'},
                    {data: 'last_in_tci'}
                ]
            });
        }
        if (isLate=='is_late_bongkar') {
            modal.find('#kolom1').text("LAST IN TCI");
            modal.find('#kolom2').text("BOOKING COMPLETE");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'last_in_tci'},
                    {data: 'bongkar_date'}
                ]
            });
        }
        if (isLate=='is_late_billing') {
            modal.find('#kolom1').text("CONFIRM");
            modal.find('#kolom2').text("BILLING");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'confirmation_date'},
                    {data: 'billing_date'}
                ]
            });
        }
        if (isLate=='is_late_payment') {
            modal.find('#kolom1').text("BILLING");
            modal.find('#kolom2').text("PAYMENT");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'billing_date'},
                    {data: 'payment_date'}
                ]
            });
        }
        if (isLate=='is_late_sppb' && bound=='outbound') {
            modal.find('#kolom1').text("BPN");
            modal.find('#kolom2').text("SPPB");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'bpn_date'},
                    {data: 'sppb_date'}
                ]
            });
        }
        if (isLate=='is_late_booking_complete' ) {
            modal.find('#kolom1').text("sppb/ sec_start");
            modal.find('#kolom2').text("booking complete");
            table = $('#table-filter-by-color-yellow').DataTable({
                language: {
                    processing: "Loading...",
                    searchPlaceholder: "Search job"
                },
                ajax: baseUrl + 'report/data_by_color/'+ isLate +'/'+color+'/'+bound+'?' + queryString,
                order: [[0, "asc"]],
                columns: [
                    {data: 'no'},
                    {data: 'no_upload'},
                    {data: 'customer_name'},
                    {data: 'no_reference'},
                    {data: 'sppb_sec_start_date'},
                    {data: 'booking_complete'}
                ]
            });
        }
    });
    
});
