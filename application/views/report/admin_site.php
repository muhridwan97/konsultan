<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Admin Site Detail</h3>
        <div class="pull-right">
            <a href="#form-filter-admin-site" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_admin_site', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=admin_site" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_admin_site') ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-admin-site">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Document</th>
                <th class="doc_type">Document Type</th>
                <th class="date">Created At</th>
                <th class="created_name">Created Name</th>
                <th class="customer_name">Customer Name</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
