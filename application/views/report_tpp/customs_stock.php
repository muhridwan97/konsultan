<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Customs Stock Report</h3>
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
        <?php $this->load->view('report_tpp/_filter_customs_stock', ['hidden' => isset($_GET['filter_date']) ? false : true]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-custom-stock">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th colspan="3" class="text-center">Dokumen Masuk</th>
                <th colspan="2" class="text-center">BA Segel</th>
                <th colspan="2" class="text-center">BA Serah Pemindahan</th>
                <th rowspan="2">Tgl Masuk</th>
                <th rowspan="2">FCL/LCL</th>
                <th rowspan="2">No Container</th>
                <th rowspan="2">Ex Container</th>
                <th rowspan="2">20</th>
                <th rowspan="2">40</th>
                <th rowspan="2">45</th>
                <th rowspan="2">Consignee</th>
                <th rowspan="2">TPS</th>
                <th rowspan="2">WIL</th>
                <th rowspan="2">Uraian Barang</th>
                <th rowspan="2">Quantity</th>
                <th rowspan="2">Unit</th>
                <th colspan="2" class="text-center">NHP</th>
                <th rowspan="2">Status Akhir</th>
                <th colspan="2">Handling Date</th>
                <th rowspan="2">Outbound Date</th>
                <th colspan="2" class="text-center">Dok Kep</th>
                <th colspan="2" class="text-center">Dok Out</th>
                <th rowspan="2">Note</th>
                <th colspan="2" class="text-center">IN</th>
                <th colspan="2" class="text-center">OUT</th>
            </tr>
            <tr>
                <th>Status Awal</th>
                <th>No</th>
                <th>Date</th>
                <th>No</th>
                <th>Date</th>
                <th>No</th>
                <th>Date</th>
                <th>No</th>
                <th>Date</th>
                <th>Stripping</th>
                <th>Stuffing</th>
                <th>No</th>
                <th>Date</th>
                <th>No</th>
                <th>Date</th>
                <th>Container</th>
                <th>LCL</th>
                <th>Container</th>
                <th>LCL</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-tpp.js?v=1') ?>" defer></script>