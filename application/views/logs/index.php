<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Logs</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-logs">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Type</th>
                <th class="type-json">Data</th>
                <th>Name</th>
                <th class="type-date">Created At</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script id="control-logs-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <li>
                <a href="<?= site_url('logs/view/{{id}}') ?>">
                    <i class="fa ion-search"></i>View
                </a>
            </li>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/logs.js?v=2') ?>" defer></script>