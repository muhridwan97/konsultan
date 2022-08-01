<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Users</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_CREATE)): ?>
                <a href="<?= site_url('user/create') ?>" class="btn btn-primary">
                    Create User
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive table-ajax" id="table-user">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Name</th>
                <th>Username</th>
                <th class="type-email">Email</th>
                <th class="type-status">Status</th>
                <th class="type-role">Total Role</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-user-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_VIEW)): ?>
                <li>
                    <a href="<?= site_url('user/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_EDIT)): ?>
                <li>
                    <a href="<?= site_url('user/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('user/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="User"
                       data-label="{{name}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/user.js') ?>" defer></script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>