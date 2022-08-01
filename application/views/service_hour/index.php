<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Service Hour Data</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if(get_url_param('view_all') == '1'): ?>
                <a href="<?= site_url('service-hour') ?>" class="btn btn-danger">
                    View Active Only
                </a>
            <?php else: ?>
                <a href="<?= site_url('service-hour?view_all=1') ?>" class="btn btn-success">
                    View All
                </a>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_CREATE)): ?>
                <a href="<?= site_url('service-hour/create') ?>" class="btn btn-primary">
                    Create Service Hour
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-service-hour">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Service Day</th>
                <th class="type-hour">Start Hour</th>
                <th class="type-hour">End Hour</th>
                <th class="type-date">Effective Date</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-service-hour-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_VIEW)): ?>
                <li>
                    <a href="<?= site_url('service-hour/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_PRINT)): ?>
                <li>
                    <a href="<?= site_url('service-hour/print-service-hour/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('service-hour/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('service-hour/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Service hour"
                       data-label="{{service_hour_label}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/service-hour.js') ?>" defer></script>