<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Summary</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=summary" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-service-time-summary">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Customer Name</th>
                <th>Booking Type</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>No Invoice</th>
                <th>No BL</th>
                <th>Assignment Delivery Order</th>
                <th>ATA Date</th>
                <th>DO/ Delivery Order Date</th>
                <th>ST Delivery Order</th>
                <th>SPPB Date</th>
                <th>TILA Date</th>
                <th>No Safe Conduct</th>
                <th>No Police</th>
                <th>Driver</th>
                <th>Expedition Type</th>
                <th>No Container</th>
                <th>Security In (Start)</th>
                <th>Security Out (Stop)</th>
                <th>ST Inbound</th>
                <th>ST Trucking</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Container</h3>
        <div class="pull-right">
            <a href="#form-filter-service-time" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_service_time', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=all" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('report/_filter_service_time', ['hidden' => isset($_GET['filter_service_time']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-service-time">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>Type</th>
                <th>No Safe Conduct</th>
                <th>Driver</th>
                <th>No Police</th>
                <th>Expedition</th>
                <th>Owner</th>
                <th>No Container</th>
                <th>Container Type</th>
                <th>Container Size</th>
                <th>Security In (Start)</th>
                <th>Security Out (Stop)</th>
                <th>Trucking Service Time</th>
                <th>Tally Name</th>
                <th>No Job</th>
                <th>Queue Duration</th>
                <th>Tally Taken At (Start)</th>
                <th>Tally Completed At (Stop)</th>
                <th>Tally Service Time</th>
                <th>Gate In (Start)</th>
                <th>Gate Out (Stop)</th>
                <th>Gate Service Time</th>
                <th>Booking Date</th>
                <th>Total Service Time</th>
                <th>Total Service Time (days)</th>
            </tr>
            </thead>
        </table>

        <div class="box box-success mt20">
            <div class="box-header with-border">
                <h3 class="box-title">Service Time Summary</h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <div>
                        <span class="pull-right mt10">January <?= date('Y') ?> - December <?= date('Y') ?></span>
                        <p class="lead mb10">Inbound</p>
                        <canvas id="barChartServiceTimeIn" style="height:150px"></canvas>
                    </div>
                    <div>
                        <span class="pull-right mt10">January <?= date('Y') ?> - December <?= date('Y') ?></span>
                        <p class="lead mb10">Outbound</p>
                        <canvas id="barChartServiceTimeOut" style="height:150px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Monthly</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=monthly" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-service-time-monthly">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Year</th>
                <th>Month</th>
                <th>Category</th>
                <th>Avg Trucking Service Time</th>
                <th>Avg Queue Duration</th>
                <th>Avg Tally Service Time</th>
                <th>Avg Gate Service Time</th>
                <th>Avg Total Service Time</th>
                <th>Avg Total Service Time (days)</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Weekly</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=weekly" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-service-time-weekly">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Year</th>
                <th>Month</th>
                <th>Week</th>
                <th>Category</th>
                <th>Avg Trucking Service Time</th>
                <th>Avg Queue Duration</th>
                <th>Avg Tally Service Time</th>
                <th>Avg Gate Service Time</th>
                <th>Avg Total Service Time</th>
                <th>Avg Total Service Time (days)</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Driver</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=driver" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-responsive">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Driver Name</th>
                <th>Avg Trucking Service Time</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($serviceTimesDriver as $serviceTime): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $serviceTime['driver'] ?></td>
                    <td><?= if_empty($serviceTime['trucking_service_time'], '-') ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Tally (Field)</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=tally_field" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-responsive">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Tally Name</th>
                <th>Avg Queue Duration</th>
                <th>Avg Tally Service Time</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($serviceTimesTallyField as $serviceTime): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $serviceTime['tally_name'] ?></td>
                    <td><?= if_empty($serviceTime['queue_duration'], '-') ?></td>
                    <td><?= if_empty($serviceTime['tally_service_time'], '-') ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Time Tally (Warehouse)</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=tally_warehouse" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-responsive">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Tally Name</th>
                <th>Avg Queue Duration</th>
                <th>Avg Tally Service Time</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($serviceTimesTallyWarehouse as $serviceTime): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $serviceTime['tally_name'] ?></td>
                    <td><?= if_empty($serviceTime['queue_duration'], '-') ?></td>
                    <td><?= if_empty($serviceTime['tally_service_time'], '-') ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    var chartLabelIn = <?= json_encode(array_column($serviceTimeChartIn, 'week')) ?>;
    var charDataQueueIn = <?= json_encode(array_column($serviceTimeChartIn, 'queue_duration')) ?>;
    var charDataGateIn = <?= json_encode(array_column($serviceTimeChartIn, 'gate_service_time')) ?>;
    var charDataTruckingIn = <?= json_encode(array_column($serviceTimeChartIn, 'tally_service_time')) ?>;
    var charDataTallyIn = <?= json_encode(array_column($serviceTimeChartIn, 'trucking_service_time')) ?>;
    var charDataBookingIn = <?= json_encode(array_column($serviceTimeChartIn, 'booking_service_time')) ?>;

    var chartLabelOut = <?= json_encode(array_column($serviceTimeChartOut, 'week')) ?>;
    var charDataQueueOut = <?= json_encode(array_column($serviceTimeChartOut, 'queue_duration')) ?>;
    var charDataGateOut = <?= json_encode(array_column($serviceTimeChartOut, 'gate_service_time')) ?>;
    var charDataTruckingOut = <?= json_encode(array_column($serviceTimeChartOut, 'tally_service_time')) ?>;
    var charDataTallyOut = <?= json_encode(array_column($serviceTimeChartOut, 'trucking_service_time')) ?>;
    var charDataBookingOut = <?= json_encode(array_column($serviceTimeChartOut, 'booking_service_time')) ?>;
</script>
<script src="<?= base_url('assets/plugins/chartjs/Chart.min.js') ?>"></script>