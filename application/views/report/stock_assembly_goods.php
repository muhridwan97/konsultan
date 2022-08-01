
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
            'bookings' => $bookingGoods,
            'warehouses' => $warehouses,
            'container_mode' => false,
            'hidden' => isset($_GET['filter_summary_goods']) ? false : true
        ]) ?>

        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <table class="table table-bordered table-striped no-wrap table-ajax" id="table-summary-assembly-goods">
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
                    <th class="type-numeric">Weight (Kg)</th>
                    <th class="type-numeric">Weight Gross (Kg)</th>
                    <th class="type-numeric">Length (M)</th>
                    <th class="type-numeric">Width (M)</th>
                    <th class="type-numeric">Height (M)</th>
                    <th class="type-numeric">Volume (M<sup>3</sup>)</th>
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
                    <th>Label No</th>
                    <th class="type-goods">Description</th>
                    <th class="type-numeric">Quantity</th>
                    <th>Unit</th>
                    <th class="type-numeric">Tonnage (Kg)</th>
                    <th class="type-numeric">Volume</th>
                    <th>BL Number</th>
                    <th class="type-date">Stock Date</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>
        <?php endif; ?>

        <div class="row mt20">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Total Item</p>
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
                        <p class="lead mb0">Weights</p>
                        <h3 class="mt0">
                            <?= numerical($reportGoodsTonnage, 3, true) ?> <small>Kg</small>
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

    </div>
</div>

<script src="<?= base_url('assets/app/js/report-summary.js?v=1') ?>" defer></script>