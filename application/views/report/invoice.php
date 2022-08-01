<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Invoice</h3>
        <div class="pull-right">
            <a href="#form-filter-invoice" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_invoice', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_invoice', ['hidden' => isset($_GET['filter_invoice']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-invoice-summary">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Branch</th>
                <th>Year</th>
                <th>No Reference</th>
                <th>No Reference Booking</th>
                <th class="type-date">Invoice Date</th>
                <th>Invoice Type</th>
                <th>Data Type</th>
                <th class="type-invoice">No Invoice</th>
                <th>No Faktur</th>
                <th>Customer</th>
                <th class="type-date">Inbound Date</th>
                <th class="type-date">Outbound Date</th>
                <th>Total Day</th>
                <th>Item Summary</th>
                <th class="type-currency">Storage</th>
                <th class="type-currency">Lift On/Off</th>
                <th class="type-currency">Moving</th>
                <th class="type-currency">Moving Adjustment</th>
                <th class="type-currency">Seal</th>
                <th class="type-currency">Pencacahan Prioritas</th>
                <th class="type-currency">Pencacahan Behandle</th>
                <th class="type-currency">OB TPS</th>
                <th class="type-currency">Non OB TPS</th>
                <th class="type-currency">Discount</th>
                <th class="type-currency">Admin Fee</th>
                <th class="type-currency">DPP</th>
                <th class="type-currency">PPN</th>
                <th class="type-currency">Materai</th>
                <th class="type-currency">Total</th>
                <th>Payment Date</th>
                <th class="type-currency">Bank Transfer</th>
                <th class="type-currency">Transfer Amount</th>
                <th class="type-currency">Cash Amount</th>
                <th class="type-currency-total">Total Payment</th>
                <th class="type-currency">Over Payment Amount</th>
                <th>Payment Description</th>
            </tr>
            </thead>
        </table>
    </div>
</div>