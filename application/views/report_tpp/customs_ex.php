<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Customs Ex Report</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                Show Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter"
              id="form-filter-container" <?= isset($_GET['filter_ex']) ? '' : 'style="display:none"' ?>>
            <input type="hidden" name="filter_ex" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="date_type">Date To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Date to"
                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="mb20 clearfix">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true&type=btd" class="btn btn-success pull-right">
                Export Excel
            </a>
            <h4 class="mt10">Report BTD</h4>
        </div>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-ex-btd">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th colspan="2" class="text-center">BCF</th>
                <th colspan="2" class="text-center">Container</th>
                <th rowspan="2">Consignee</th>
                <th rowspan="2">Uraian Barang</th>
                <th colspan="2" class="text-center">Surat Perintah Tarik</th>
                <th rowspan="2">Tanggal Masuk</th>
                <th rowspan="2">Tanggal Pencacahan</th>
                <th rowspan="2">Tanggal Keluar</th>
                <th colspan="2">Dokumen Pengeluaran</th>
                <th rowspan="2">Tanggal Dokumen</th>
                <th colspan="4" class="text-center">Nomor & Tanggal</th>
                <th rowspan="2">Tanggal Lelang 1</th>
                <th rowspan="2">Tanggal Lelang 2</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Ukuran</th>
                <th>No Container</th>
                <th>No</th>
                <th>Tanggal</th>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kep BTD Lelang</th>
                <th>Tanggal</th>
                <th>Kep BMN</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <hr>

        <div class="mb20 clearfix">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true&type=bdn" class="btn btn-success pull-right">
                Export Excel
            </a>
            <h4 class="mt10">Report BDN</h4>
        </div>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-ex-bdn">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th colspan="2" class="text-center">KEP BDN</th>
                <th colspan="2" class="text-center">Container</th>
                <th rowspan="2">Consignee</th>
                <th rowspan="2">Uraian Barang</th>
                <th colspan="2" class="text-center">Surat Perintah Tarik</th>
                <th rowspan="2">Tanggal Masuk</th>
                <th rowspan="2">Tanggal Pencacahan</th>
                <th rowspan="2">Tanggal Keluar</th>
                <th rowspan="2">Dokumen Pengeluaran</th>
                <th colspan="2" class="text-center">BMN</th>
                <th rowspan="2">Dokumen Penyelesaian Lain</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Ukuran</th>
                <th>No Container</th>
                <th>No</th>
                <th>Tanggal</th>
                <th>No</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <hr>

        <div class="mb20 clearfix">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true&type=bmn" class="btn btn-success pull-right">
                Export Excel
            </a>
            <h4 class="mt10">Report BMN</h4>
        </div>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-ex-bmn">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th colspan="2" class="text-center">KEP BMN</th>
                <th colspan="2" class="text-center">Container</th>
                <th rowspan="2">Consignee</th>
                <th rowspan="2">Uraian Barang</th>
                <th colspan="2" class="text-center">Surat Perintah Tarik</th>
                <th rowspan="2" class="type-date">Tanggal Masuk</th>
                <th rowspan="2" class="type-date">Tanggal Pencacahan</th>
                <th rowspan="2" class="type-date">Tanggal Keluar</th>
                <th colspan="2">Dokumen Pengeluaran</th>
                <th colspan="2">Persetujuan DJKN</th>
                <th rowspan="2" class="type-date">Tanggal Lelang 1</th>
                <th rowspan="2" class="type-date">Tanggal Lelang 2</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>No</th>
                <th class="type-date">Tanggal</th>
                <th>Ukuran</th>
                <th>No Container</th>
                <th>No</th>
                <th class="type-date">Tanggal</th>
                <th>Dokumen</th>
                <th class="type-date">Tanggal</th>
                <th>No</th>
                <th class="type-date">Tanggal</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <hr>

        <div class="mb20 clearfix">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true&type=tegahan" class="btn btn-success pull-right">
                Export Excel
            </a>
            <h4 class="mt10">Report TEGAHAN</h4>
        </div>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-ex-tegahan">
            <thead>
            <tr>
                <th rowspan="2" style="width: 25px">No</th>
                <th colspan="3" class="text-center">Dokumen Status Masuk</th>
                <th colspan="2" class="text-center">BA Segel</th>
                <th colspan="2" class="text-center">BA Serah Pemindahan</th>
                <th rowspan="2">Tanggal Masuk</th>
                <th rowspan="2">FCL / LCL</th>
                <th rowspan="2">No Container</th>
                <th rowspan="2">20</th>
                <th rowspan="2">40</th>
                <th rowspan="2">45</th>
                <th rowspan="2">Consignee</th>
                <th rowspan="2">TPS Asal</th>
                <th rowspan="2">Wil</th>
                <th rowspan="2">Uraian Barang</th>
                <th rowspan="2">Jumlah</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Status Akhir</th>
                <th rowspan="2">Tanggal Keluar</th>
                <th rowspan="2">Dok No</th>
                <th rowspan="2">Tanggal</th>
            </tr>
            <tr>
                <th>Awal</th>
                <th>No</th>
                <th>Tanggal</th>
                <th>No</th>
                <th>Tanggal</th>
                <th>No</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>


<script src="<?= base_url('assets/app/js/report_ex.js') ?>"></script>