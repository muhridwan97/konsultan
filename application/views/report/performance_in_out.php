<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Performance In Out</h3>
        <div class="pull-right">
			<a href="#filter_performance" class="btn btn-primary btn-filter-toggle">
				<?= get_url_param('filter_performance', 0) ? 'Hide' : 'Show' ?> Filter
			</a>
			<a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
				<i class="fa fa-file-excel-o"></i>
			</a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_performance', [
            'hidden' => isset($_GET['filter_performance']) ? false : true
        ]) ?>
        <div class="box-body">
            <div class="table-responsive">
            <table class="table table-bordered table-striped no-wrap no-datatable" id="production">
                <thead>
                <tr>
					<th style="width: 25px">No</th>
                    <th style="width: 25px">Week</th>
                    <th>Date Range</th>
                    <th>BC 1.6 to IN</th>
                    <th>BC 2.8 - Req</th>
                    <th>Req - Out PLB</th>
                    <th>Total in (box)</th>
                    <th>Total Out (vessel)</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                    <?php $number = 0; 
                    if(!empty($reportPerformances)): ?>
                    <?php foreach ($reportPerformances as $reportPerformance): ?>
                    <tr>
						<td><?= ++$number; ?></td>
                        <td><?= $reportPerformance['week']; ?></td>
                        <td class="text-nowrap">
                            <?php $weekDate = get_week_date_range_sql_mode_2($reportPerformance['week'], $reportPerformance['year']); ?>
                            (<?= $weekDate['week_start'] ?> &nbsp; - &nbsp; <?= ($weekDate['week_end']) ?>)
                        </td>
                        <td><a href="<?= site_url() ?>report/performance-in-out-detail-sppb-complete?year_week=<?= $reportPerformance['year_week'] ?><?= empty(get_url_param('customer')) ? '':'&'.http_build_query(array('customer'=>get_url_param('customer'))) ?>" target="_blank"><?= if_empty($reportPerformance['sppb_complete'],0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/performance-in-out-detail-st-sppb-request?year_week=<?= $reportPerformance['year_week'] ?><?= empty(get_url_param('customer')) ? '':'&'.http_build_query(array('customer'=>get_url_param('customer'))) ?>" target="_blank"><?= if_empty($reportPerformance['st_sppb_req'],0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/performance-in-out-detail-st-request-complete?year_week=<?= $reportPerformance['year_week'] ?><?= empty(get_url_param('customer')) ? '':'&'.http_build_query(array('customer'=>get_url_param('customer'))) ?>" target="_blank"><?= if_empty($reportPerformance['st_req_complete'],0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/performance-in-out-detail-total-lcl-container?year_week=<?= $reportPerformance['year_week'] ?><?= empty(get_url_param('customer')) ? '':'&'.http_build_query(array('customer'=>get_url_param('customer'))) ?>" target="_blank"><?= if_empty(($reportPerformance['total_container']+$reportPerformance['total_lcl']),0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/performance-in-out-detail-total-fleet?year_week=<?= $reportPerformance['year_week'] ?><?= empty(get_url_param('customer')) ? '':'&'.http_build_query(array('customer'=>get_url_param('customer'))) ?>" target="_blank"><?= if_empty($reportPerformance['total_fleet'],0) ?></a></td>
                        <td><?= if_empty(($reportPerformance['total_container']+$reportPerformance['total_lcl']+$reportPerformance['total_fleet']),0) ?></td>
                        
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9"> No Data Available </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<script>
//     $(document).ready(function() {
//     $('#production').DataTable( {
//         "order": [[ 0, "asc" ]]
//     } );
// } );
</script>
<div class="box box-primary">
    <div class="box-body">
		<h4 class="box-title">Performance Chart <?= get_url_param('year', date('Y')) ?></h4>
		<div>
			<canvas id="satisfaction-chart" height="100"></canvas>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script defer>
	const datasetOption = {
		label: "Label",
		fill: false,
		lineTension: 0.1,
		backgroundColor: "rgba(0,0,0,0.2)",
		borderColor: "rgba(0,0,0,1)",
		borderCapStyle: 'butt',
		borderDash: [],
		borderDashOffset: 0.0,
		borderJoinStyle: 'miter',
		pointBorderColor: "rgba(0,0,0,1)",
		pointBackgroundColor: "#fff",
		pointBorderWidth: 1,
		pointHoverRadius: 6,
		pointHoverBackgroundColor: "rgba(0,0,0,1)",
		pointHoverBorderColor: "rgba(220,220,220,1)",
		pointHoverBorderWidth: 2,
		pointRadius: 2,
		pointHitRadius: 50,
		data: [],
		spanGaps: false,
	};
	<?php $reportChart = array_reverse($reportPerformances); ?>
	const data = {
		labels: <?= json_encode(array_column($reportChart, 'week')) ?>,
		datasets: [
			{
				...datasetOption,
				label: "BC 1.6 to IN",
				backgroundColor: "rgba(255,112,112,0.2)",
				borderColor: "rgb(255,112,112)",
				pointBorderColor: "rgba(255,112,112,1)",
				pointHoverBackgroundColor: "rgba(255,112,112,1)",
				data: <?= json_encode(array_column($reportChart, 'sppb_complete')) ?>,
				hidden: false,
			}, {
				...datasetOption,
				label: "BC 2.8 - Req",
				backgroundColor: "rgba(75,192,192,0.4)",
				borderColor: "rgba(75,192,192,1)",
				pointBorderColor: "rgba(75,192,192,1)",
				pointHoverBackgroundColor: "rgba(75,192,192,1)",
				data: <?= json_encode(array_column($reportChart, 'st_sppb_req')) ?>,
				hidden: true,
			}, {
				...datasetOption,
				label: "Req - Out PLB",
				backgroundColor: "rgba(179,181,198,0.2)",
				borderColor: "rgba(179,181,198,1)",
				pointBorderColor: "rgba(179,181,198,1)",
				pointHoverBackgroundColor: "rgba(179,181,198,1)",
				data: <?= json_encode(array_column($reportChart, 'st_req_complete')) ?>,
				hidden: true,
			}, {
				...datasetOption,
				label: "Total In (box)",
				backgroundColor: "rgba(179,181,98,0.2)",
				borderColor: "rgba(179,181,98,1)",
				pointBorderColor: "rgba(179,181,98,1)",
				pointHoverBackgroundColor: "rgba(179,181,98,1)",
				data: <?= json_encode(array_column($reportChart, 'total_in')) ?>,
				hidden: false,
			}, {
				...datasetOption,
				label: 'Total Out (vessel)',
				backgroundColor: 'rgba(125,48,193,0.2)',
				borderColor: 'rgba(125,48,193,1)',
				pointBorderColor: 'rgba(125,48,193,1)',
				pointHoverBackgroundColor: 'rgba(125,48,193,1)',
				data: <?= json_encode(array_column($reportChart, 'total_fleet')) ?>,
				hidden: true,
			}, {
				...datasetOption,
				label: 'Total',
				backgroundColor: 'rgba(183,35,158,0.2)',
				borderColor: 'rgba(183,35,158,1)',
				pointBorderColor: 'rgba(183,35,158,1)',
				pointHoverBackgroundColor: 'rgba(183,35,158,1)',
				data: <?= json_encode(array_column($reportChart, 'total_all')) ?>,
				hidden: true,
			},
		]
	};
	const ctx = document.getElementById("satisfaction-chart");
	const chart = new Chart(ctx, {
		type: 'line',
		data: data,
		options: {
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			scales: {
				yAxes: [{
					scaleLabel: {
						display: true,
						labelString: 'Total'
					},
					ticks: {
						min: 0
					}
				}],
				xAxes: [{
					scaleLabel: {
						display: true,
						labelString: 'Week'
					}
				}]
			}
		}
	});
</script>