<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pallet</h3>
        <div class="pull-right">
            <a href="#form-filter-admin-site" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_pallet', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=pallet" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_pallet') ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-pallet">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th class="no_job">No Job</th>
                <th class="aju">Aju</th>
                <th class="date">Tanggal Aktivitas</th>
                <th class="container1">No Container</th>
                <th>Type</th>
                <th>Jenis Aktivitas</th>
                <th>Qty</th>
                <th>Pallet (Pcs)</th>
                <th>Pallet Sisa</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
