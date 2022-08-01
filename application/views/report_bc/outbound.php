<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Outbound</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                Show Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report_bc/_filter_activity', ['hidden' => isset($_GET['filter_activity']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax no-wrap" id="table-outbound-bc">
            <thead>
            <tr>
                <th>No</th>
                <th>Branch</th>
                <th>Registration No</th>
                <th>Registration Date</th>
                <th>Warehouse</th>
                <th>BC Doc In / Ref No In</th>
                <th>BC Doc In / Ref Date In</th>
                <th>BC Doc In / Booking Type In</th>
                <th>Booking No In</th>
                <th>Booking Date In</th>
                <th>BC Doc / Ref No</th>
                <th>BC Doc / Ref Date</th>
                <th>BC Doc / Booking Type</th>
                <th>Booking No</th>
                <th>Booking Date</th>
                <th>Supplier</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th>Owner</th>
                <th>No Safe Conduct</th>
                <th>Safe Conduct Date</th>
                <th>Vehicle Type</th>
                <th>Driver</th>
                <th>Police No</th>
                <th>Expedition</th>
                <th>E-seal</th>
                <th>Job No</th>
                <th>Transaction Date</th>
                <th>Item Category</th>
                <th>Item No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Weight (Kg)</th>
                <th>Volume (M<sup>3</sup>)</th>
                <th>Position</th>
                <th>Cont. Type</th>
                <th>Cont. Size</th>
                <th>Cont. Seal</th>
                <th>Cont. Status</th>
                <th>Item Condition</th>
                <th>Status Danger</th>
                <th>Ex No Container</th>
                <th>Pallet No</th>
                <th>Description</th>
                <th>Admin User</th>
                <th>Tally User</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-bc.js?v=1') ?>" defer></script>