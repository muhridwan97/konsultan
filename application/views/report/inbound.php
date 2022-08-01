<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Inbound Containers</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_container', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_container', ['hidden' => isset($_GET['filter_container']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-inbound-container">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Registration</th>
                <th class="type-date">Registration Date</th>
                <th class="type-booking">No Booking</th>
                <th>No Reference</th>
                <th class="type-date">Reference Date</th>
                <th>Booking Type</th>
                <th class="hidden">No Booking In</th>
                <th class="hidden">No Reference In</th>
                <th class="hidden">Reference Date In</th>
                <th class="hidden">Booking Type In</th>
                <th class="type-date-time">Booking Date</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th>Owner</th>
                <th class="type-safe-conduct">No Safe Conduct</th>
                <th>Driver</th>
                <th>No Police</th>
                <th>Expedition</th>
                <th class="type-date-time">Trucking Start</th>
                <th class="type-date-time">Trucking Finish</th>
                <th>Source Warehouse</th>
                <th class="type-handling">No Handling</th>
                <th class="type-work-order">No Job</th>
                <th class="type-date-time">Gate In</th>
                <th class="type-date-time">Gate Out</th>
                <th class="type-date-time">Tally Start</th>
                <th class="type-date-time">Tally Finish</th>
                <th>No Container</th>
                <th>Type</th>
                <th>Size</th>
                <th>Seal</th>
                <th>Position</th>
                <th class="type-is-empty">Is Empty</th>
                <th class="type-is-hold">Is Hold</th>
                <th>Status Condition</th>
                <th>Status Danger</th>
                <th>Description</th>
            </tr>
            </thead>
        </table>

        <div class="row mt20">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Total Inbound</p>
                        <h3 class="mt0">
                            <?= numerical($reportContainerTotals, 0, true) ?> <small>containers</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">20 Feet</p>
                        <h3 class="mt0">
                            <?= numerical($reportContainer20, 0, true) ?> <small>containers</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">40 Feet</p>
                        <h3 class="mt0">
                            <?= numerical($reportContainer40, 0, true) ?> <small>containers</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">45 Feet</p>
                        <h3 class="mt0">
                            <?= numerical($reportContainer45, 0, true) ?> <small>containers</small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <span class="pull-right mt10">January <?= date('Y') ?> - December <?= date('Y') ?></span>
                <h3 class="box-title">Inbound container / <small>Outbound</small></h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChartContainer" style="height:230px"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Inbound Goods</h3>
        <div class="pull-right">
            <a href="#form-filter-goods" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_goods', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_goods', ['hidden' => isset($_GET['filter_goods']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-inbound-goods">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Registration</th>
                <th class="type-date">Registration Date</th>
                <th class="type-booking">No Booking</th>
                <th class="type-booking">No Reference</th>
                <th class="type-date">Reference Date</th>
                <th>Booking Type</th>
                <th class="hidden">No Booking In</th>
                <th class="hidden">No Reference In</th>
                <th class="hidden">Reference Date In</th>
                <th class="hidden">Booking Type In</th>
                <th class="type-date">Booking Date</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th>Owner</th>
                <th class="type-safe-conduct">No Safe Conduct</th>
                <th>Driver</th>
                <th>No Police</th>
                <th>Expedition</th>
                <th class="type-date-time">Trucking Start</th>
                <th class="type-date-time">Trucking Finish</th>
                <th>Source Warehouse</th>
                <th class="type-handling">No Handling</th>
                <th class="type-work-order">No Job</th>
                <th class="type-date-time">Gate In</th>
                <th class="type-date-time">Gate Out</th>
                <th class="type-date-time">Tally Start</th>
                <th class="type-date-time">Tally Finish</th>
                <th>No Goods</th>
                <th>Goods Name</th>
                <th class="type-numeric">Quantity</th>
                <th class="type-numeric">Unit Weight (Kg)</th>
                <th class="type-numeric">Total Weight (Kg)</th>
                <th class="type-numeric">Unit Gross Weight (Kg)</th>
                <th class="type-numeric">Total Gross Weight (Kg)</th>
                <th class="type-numeric">Unit Volume (M<sup>3</sup>)</th>
                <th class="type-numeric">Total Volume (M<sup>3</sup>)</th>
                <th>Unit</th>
                <th>Position</th>
                <th class="hidden">Ex No Container</th>
                <th>No Pallet</th>
                <th>Whey / Label Number</th>
                <th class="type-is-hold">Is Hold</th>
                <th>Status Condition</th>
                <th>Status Danger</th>
                <th>Description</th>
            </tr>
            </thead>
        </table>

        <div class="row mt20">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Total Inbound</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsTotals, 3, true) ?> <small>items</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Quantity</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsQuantity, 3, true) ?> <small>mix units</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Weight</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsWeight / 1000, 3, true) ?> <small>Ton</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Volumes</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsVolume, 3, true) ?> <small>M<sup>3</sup></small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Inbound goods / <small>Outbound</small></h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChartGoods" style="height:230px"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    var mainData = 'inbound';
    var chartLabel = <?= json_encode(array_column($reportContainerChart, 'month')) ?>;
    var charDataIn = <?= json_encode(array_column($reportContainerChart, 'total_in')) ?>;
    var charDataOut = <?= json_encode(array_column($reportContainerChart, 'total_out')) ?>;

    var mainGoodsData = 'inbound';
    var chartGoodsLabel = <?= json_encode(array_column($reportGoodsChart, 'month')) ?>;
    var charGoodsDataIn = <?= json_encode(array_column($reportGoodsChart, 'total_in')) ?>;
    var charGoodsDataOut = <?= json_encode(array_column($reportGoodsChart, 'total_out')) ?>;
</script>
<script src="<?= base_url('assets/plugins/chartjs/Chart.min.js?v=1') ?>"></script>