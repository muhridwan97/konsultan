<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Containers</h3>
        <div class="pull-right">
            <a href="#filter_summary_container" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_summary_container', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_summary', [
            'filter_summary' => 'filter_summary_container',
            'warehouses' => $warehouses,
            'container_mode' => true,
            'hidden' => isset($_GET['filter_summary_container']) ? false : true
        ]) ?>

        <table class="table table-bordered table-striped no-wrap table-responsive table-ajax" id="table-summary-container">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Owner</th>
                <th class="type-booking">No Reference</th>
                <th class="type-container">No Container</th>
                <th>Type</th>
                <th>Size</th>
                <th class="type-number">Qty</th>
                <th>Type DO</th>
                <th class="type-date">Expired DO</th>
                <th class="type-date">Freetime DO</th>
                <th class="type-position">Position</th>
                <th>Warehouse</th>
                <th>Source Warehouse</th>
                <th>Document Status</th>
                <th>Seal</th>
                <th>Status</th>
                <th class="type-danger">Danger</th>
                <th class="type-empty">Is Empty</th>
                <th class="type-hold">Is Hold</th>
                <th class="type-age">Age</th>
                <th>Inbound Date</th>
                <th>Description</th>
            </tr>
            </thead>
        </table>

        <div class="row mt20">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Total Stock</p>
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
    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Goods</h3>
        <div class="pull-right">
            <a href="#filter_summary_goods" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_summary_goods', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_summary', [
            'filter_summary' => 'filter_summary_goods',
            'warehouses' => $warehouses,
            'container_mode' => false,
            'hidden' => isset($_GET['filter_summary_goods']) ? false : true
        ]) ?>

        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <table class="table table-bordered table-striped no-wrap table-ajax" id="table-summary-goods">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Owner</th>
                    <th class="type-booking">No Reference</th>
                    <th>No Invoice</th>
                    <th>No BL</th>
                    <th class="type-goods">No Goods</th>
                    <th>Goods Name</th>
                    <th class="type-assembly-goods">Assembly Goods</th>
                    <th>Whey / Label Number</th>
                    <th class="type-position">Position</th>
                    <th>Warehouse</th>
                    <th>No Pallet</th>
                    <th class="type-numeric">Quantity</th>
                    <th>Unit</th>
                    <th class="type-numeric">Unit Weight (Kg)</th>
                    <th class="type-numeric">Total Weight (Kg)</th>
                    <th class="type-numeric">Unit Gross Weight (Kg)</th>
                    <th class="type-numeric">Total Gross Weight (Kg)</th>
                    <th class="type-numeric">Unit Length (M)</th>
                    <th class="type-numeric">Unit Width (M)</th>
                    <th class="type-numeric">Unit Height (M)</th>
                    <th class="type-numeric">Unit Volume (M<sup>3</sup>)</th>
                    <th class="type-numeric">Total Volume (M<sup>3</sup>)</th>
                    <th>Status</th>
                    <th class="type-danger">Danger</th>
                    <th class="type-hold">Is Hold</th>
                    <th>Ex Container</th>
                    <th class="type-age">Age</th>
                    <th>Inbound Date</th>
                    <th>Description</th>
                </tr>
                </thead>
            </table>
        <?php else: ?>
            <table class="table table-bordered table-striped no-wrap table-ajax" id="table-summary-goods-external">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Invoice Number</th>
                    <th class="type-booking">No Reference</th>
                    <th>Ex No Container</th>
                    <th>Goods No</th>
                    <th>Label No</th>
                    <th class="type-goods">Description</th>
                    <th class="type-numeric">Quantity</th>
                    <th>Unit</th>
                    <th class="type-numeric">Weight (Kg)</th>
                    <th class="type-numeric">Volume M<sup>3</sup></th>
                    <th>BL Number</th>
                    <th class="type-date">Stock Date</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>
        <?php endif ?>

        <div class="row mt20">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Quantity</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsQuantity, 2, true) ?> <small>mix units</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Net Weight</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsWeight / 1000, 2, true) ?> <small>Ton</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Volume</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsVolume, 2, true) ?> <small>M<sup>3</sup></small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="<?= base_url('assets/app/js/report-summary.js?v=6') ?>" defer></script>