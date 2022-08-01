<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Shipping Line Report</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                Show Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report_tpp/_filter_shipping_line', ['hidden' => isset($_GET['filter_activity']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-shipping-line-stock">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Container</th>
                <th>Container Size</th>
                <th>Container Type</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th>Consignee</th>
                <th>Position</th>
                <th>Gate In Date</th>
                <th>Gate In Time</th>
                <th>Seal</th>
                <th>BC 1.1</th>
                <th>BC 1.1 Date</th>
                <th>BC 1.1 Pos</th>
                <th>No BL</th>
                <th>Goods</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Status Date</th>
                <th>Shipping Line</th>
                <th>TPS</th>
                <th>NHP No</th>
                <th>NHP Date</th>
                <th>Dok Kep</th>
                <th>Kep Date</th>
                <th>Doc Out</th>
                <th>Out Date</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-tpp.js?v=1') ?>" defer></script>