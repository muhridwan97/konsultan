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
        <?php $this->load->view('report_tpp/_filter_summary', [
            'filter_summary' => 'filter_summary_container',
            'bookings' => $bookingContainers,
            'warehouses' => $warehouses,
            'hidden' => isset($_GET['filter_summary_container']) ? false : true
        ]) ?>

        <table class="table table-bordered table-striped no-wrap table-responsive table-ajax" id="table-summary-container">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Owner</th>
                <th class="type-booking">No Reference</th>
                <th class="type-date">Reference Date</th>
                <th>No NHP</th>
                <th class="type-date">NHP Date</th>
                <th>No BC 1.1</th>
                <th class="type-date">BC 1.1 Date</th>
                <th>No BA Segel</th>
                <th class="type-date">BA Segel Date</th>
                <th>No Kep</th>
                <th class="type-date">Kep Date</th>
                <th>No BL</th>
                <th class="type-date">BL Date</th>
                <th>Document Status</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th>Shipping Line</th>
                <th class="type-container">No Container</th>
                <th>Type</th>
                <th>Size</th>
                <th class="type-number">Qty</th>
                <th>Position</th>
                <th>Warehouse</th>
                <th>Seal</th>
                <th>Status</th>
                <th class="type-danger">Danger</th>
                <th class="type-empty">Is Empty</th>
                <th class="type-hold">Is Hold</th>
                <th class="type-age">Age</th>
                <th class="type-date">Inbound Date</th>
                <th class="type-date">Outbound Date</th>
                <th class="type-date">Lelang 1</th>
                <th class="type-date">Lelang 2</th>
                <th class="type-date">Lelang 3</th>
                <th>Description</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-tpp.js') ?>" defer></script>