if ($('#inbound-outbound-chart').length) {
    var ctxInOut = document.getElementById('inbound-outbound-chart').getContext('2d');
    var chartInOut = new Chart(ctxInOut);

    var dataInOut = {
        labels: chartBookingLabel,
        datasets: [{
            label: "Total Booking In",
            fillColor: '#00a65a',
            pointColor: '#009955',
            data: chartBookingInbound,
        }, {
            label: "Total Booking Out",
            fillColor: 'rgba(221, 75, 57, 0.9)',
            pointColor: '#c94333',
            data: chartBookingOutbound,
        }]
    };

    var optionInOut = {
        showScale: true,
        scaleShowGridLines: true,
        scaleShowHorizontalLines: true,
        maintainAspectRatio: true,
        responsive: true,
        legend: {
            display: true,
            labels: {
                fontColor: 'rgb(255, 99, 132)'
            }
        }
    };

    chartInOut.Line(dataInOut, optionInOut);
}

$('#load-data-stock-summary').on('click', function () {
    $('.stock-summary-loading').html('<i class="fa fa-refresh fa-spin"></i>');
    fetch(`${baseUrl}dashboard/ajax-get-stock-summary`)
        .then(data => data.text())
        .then(function (data) {
            $('.stock-summary-loading').remove();
            $('#table-stock-summary').html(data);

            if ($('#stock-container-chart').length) {
                var ctxContainer = document.getElementById('stock-container-chart').getContext('2d');
                var chartContainer = new Chart(ctxContainer);

                var dataContainer = {
                    labels: chartStockLabel,
                    datasets: [{
                        label: "Container",
                        fillColor: 'rgba(60, 141, 188, 0.9)',
                        pointColor: '#3c95c5',
                        data: chartStockContainer,
                    }]
                };

                var optionContainer = {
                    showScale: true,
                    scaleShowGridLines: true,
                    scaleShowHorizontalLines: true,
                    maintainAspectRatio: false,
                    responsive: true
                };

                chartContainer.Line(dataContainer, optionContainer);
            }

            if ($('#stock-goods-chart').length) {
                var ctxGoods = document.getElementById('stock-goods-chart').getContext('2d');
                ctxGoods.height = 200;
                var chartGoods = new Chart(ctxGoods);

                var dataGoods = {
                    labels: chartStockLabel,
                    datasets: [{
                        label: "Goods",
                        fillColor: 'rgba(221, 75, 57, 0.9)',
                        pointColor: '#cb4333',
                        data: chartStockGoods,
                    }]
                };

                var optionGoods = {
                    showScale: true,
                    scaleShowGridLines: true,
                    scaleShowHorizontalLines: true,
                    maintainAspectRatio: false,
                    responsive: true
                };

                chartGoods.Line(dataGoods, optionGoods);
            }
        })
        .catch(console.log);
});


$('#load-data-handling-summary').on('click', function () {
    $('.handling-summary-loading').html('<i class="fa fa-refresh fa-spin"></i>');
    fetch(`${baseUrl}dashboard/ajax-get-handling-summary`)
        .then(data => data.text())
        .then(function (data) {
            $('.handling-summary-loading').remove();
            $('#table-handling-summary').html(data);

            if ($('#handling-chart').length) {
                var ctxHandling = document.getElementById('handling-chart').getContext('2d');
                var chartHandling = new Chart(ctxHandling);

                var dataHandling = {
                    labels: chartHandlingLabel,
                    datasets: [{
                        label: "Activity",
                        strokeColor: '#fff',
                        data: chartHandlingData,
                    }]
                };

                var optionHandling = {
                    showScale: true,
                    scaleShowGridLines: true,
                    scaleShowHorizontalLines: true,
                    maintainAspectRatio: true,
                    responsive: true
                };

                var chart = chartHandling.Bar(dataHandling, optionHandling);

                if (chartHandlingData.length > 0) {
                    var colors = ["#c73e1d", "#0da245", "#f18f01", "#75704e", "#a23b72", "#82667f", "#16324f", "#18435a", "#2a628f", "#3781b5", "#3499c3"]
                    for (var i = 0; i < chart.datasets[0].bars.length; i++) {
                        if (i < colors.length) {
                            chart.datasets[0].bars[i].fillColor = colors[i];
                        } else {
                            chart.datasets[0].bars[i].fillColor = "#3499c3";
                        }
                    }
                    chart.update();
                }
            }
        })
        .catch(console.log);
});