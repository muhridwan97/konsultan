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
            'container_mode' => false,
            'hidden' => isset($_GET['filter_summary_goods']) ? false : true
        ]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-status-goods">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Packing List</th>
                <th>No Container</th>
                <th>No Goods</th>
                <th>Whey Number</th>
                <th>Goods Name</th>
                <th class="numeric">Quantity</th>
                <th>Unit</th>
                <th>No Reference</th>
                <th>Payment Status</th>
                <th>BCF Status</th>
                <th>Remarks</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report_stock_status.js') ?>" defer></script>