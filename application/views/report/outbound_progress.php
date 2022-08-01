<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Outbound Progress</h3>
        <div class="pull-right">
            <a href="#form-filter-outbound-progress" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_outbound_progress', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_outbound_progress', ['hidden' => isset($_GET['filter_outbound_progress']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" data-page-length="15" id="table-outbound-progress">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Branch</th>
                <th>Customer</th>
                <th>Booking Type</th>
                <th>No Invoice</th>
                <th>No Registration</th>
                <th>No Reference</th>
                <th>No Reference Inbound</th>
                <th>Goods</th>
                <th class="type-numeric">Total Net Weight (Kg)</th>
                <th class="type-numeric">Total Gross Weight (Kg)</th>
                <th>CIF</th>
                <th class="date-time">Upload Date</th>
                <th class="date-time">Draft Date</th>
                <th>Type Parties</th>
                <th>Parties</th>
                <th class="date-time">Confirmation Date</th>
                <th class="date-time">Billing Date</th>
                <th class="date-time">BPN Date</th>
                <th class="date-time">SPPF Date</th>
                <th class="date-time">SPPB Date</th>
                <th class="date-time">SPPD Date</th>
                <th class="date-time">SPPD Inbound Date</th>
                <th>Status</th>
                <th class="type-hold">Is Hold</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-progress.js?v=2') ?>" defer></script>
