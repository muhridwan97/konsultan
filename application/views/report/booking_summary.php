<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Summary</h3>
        <div class="pull-right">
            <a href="#form-filter-booking-summary" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_booking_summary', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_booking_summary', ['hidden' => isset($_GET['filter_booking_summary']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax table-responsive" id="table-booking-summary">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th rowspan="2">Owner</th>
                <th colspan="8" class="success text-center">INBOUND</th>
                <th colspan="8" class="danger text-center">OUTBOUND</th>
                <th rowspan="2" class="text-center type-numeric">STOCK CONTAINER</th>
                <th rowspan="2" class="text-center type-numeric">STOCK GOODS</th>
            </tr>
            <tr>
                <th>No Reference</th>
                <th>Booking Date</th>
                <th class="type-numeric">Booking Container</th>
                <th class="type-numeric">Booking Goods</th>
                <th>First Entry</th>
                <th>Last Entry</th>
                <th class="type-numeric">Total Container In</th>
                <th class="type-numeric">Total Goods In</th>
                <th class="type-booking">No Reference</th>
                <th class="type-booking">Booking Date</th>
                <th class="type-numeric">Booking Container</th>
                <th class="type-numeric">Booking Goods</th>
                <th>First Entry</th>
                <th>Last Entry</th>
                <th class="type-numeric">Total Container Out</th>
                <th class="type-numeric">Total Goods Out</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
