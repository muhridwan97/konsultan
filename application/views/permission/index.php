<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Permissions</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-permission">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Module</th>
                <th>Submodule</th>
                <th>Permission</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-permission-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PERMISSION_VIEW)): ?>
                <li>
                    <a href="<?= site_url('permission/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/permission.js') ?>" defer></script>