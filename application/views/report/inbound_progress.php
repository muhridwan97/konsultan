<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Inbound Progress</h3>
        <div class="pull-right">
            <a href="#form-filter-inbound-progress" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_inbound_progress', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_inbound_progress', ['hidden' => isset($_GET['filter_inbound_progress']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" data-page-length="15" id="table-inbound-progress">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Branch</th>
                <th>Customer</th>
                <th>Booking Type</th>
                <th>No Reference</th>
                <th>No Registration</th>
                <th>No Bill Of Loading</th>
                <th>No Invoice</th>
                <th>Party</th>
                <th>Goods</th>
                <th class="type-numeric">Total Net Weight (Kg)</th>
                <th class="type-numeric">Total Gross Weight (Kg)</th>
                <th>CIF</th>
                <th class="date">ETA Date</th>
                <th class="date">ATA Date</th>
                <th class="date-time">Upload Date</th>
                <th class="date-time">Draft Date</th>
                <th>Type Parties</th>
                <th>Parties</th>
                <th class="date-time">Confirmation Date</th>
                <th class="date-time">DO Date</th>
                <th class="date-time">Expired DO Date</th>
                <th class="date-time">Freetime DO Date</th>
                <th class="date-time">SPPB Date</th>
                <th class="date-time">SPPD Date</th>
                <th class="date-time">Hardcopy Date</th>
                <th>Status</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-progress.js?v=2') ?>" defer></script>