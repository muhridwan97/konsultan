<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            TEP Tracking
            <span class="text-danger">
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)): ?>
                    <?= if_empty(get_url_param('status'), '(NOT LINKED)', '(', ')') ?>
                <?php else: ?>
                    <?= if_empty(get_url_param('status'), '(LINKED ALL)', '(', ')') ?>
                <?php endif; ?>
            </span>
        </h3>
        <div class="pull-right">
            <?php if (!get_url_param('hide_filter', 0)): ?>
                <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                    <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
                </a>
            <?php endif; ?>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)): ?>
                <a href="<?= site_url('transporter-entry-permit-tracking/create') ?>" class="btn btn-primary">
                    Link TEP
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php if (!get_url_param('hide_filter', 0)): ?>
            <?php $this->load->view('transporter_entry_permit_tracking/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>
        <?php endif; ?>
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-tep-tracking-link">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-tep">TEP Code</th>
                <th>Checked Out</th>
                <th>Linked No Vehicle</th>
                <th class="type-date-time">Site Transit</th>
                <th class="type-date-time">Unloading</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-tep-tracking-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <li class="action-view">
                <a href="<?= site_url('transporter-entry-permit-tracking/view/{{id}}') ?>">
                    <i class="fa ion-search"></i> View
                </a>
            </li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)): ?>
                <li class="action-link">
                    <a href="<?= site_url('transporter-entry-permit-tracking/create/{{id_transporter_entry_permit}}') ?>">
                        <i class="fa fa-code-fork"></i> Link
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK_EDIT)): ?>
                <li class="action-edit">
                    <a href="<?= site_url('transporter-entry-permit-tracking/edit/{{id}}') ?>">
                        <i class="fa fa-edit"></i> Edit Date
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_VALIDATE)): ?>
                <li class="action-site-transit">
                    <a href="<?= site_url('transporter-entry-permit-tracking/confirm-site-transit/{{id}}?redirect=' . get_current_url()) ?>">
                        <i class="fa fa-check"></i> Confirm Site Transit
                    </a>
                </li>
                <li class="action-unloading">
                    <a href="<?= site_url('transporter-entry-permit-tracking/confirm-unloading/{{id}}?redirect=' . get_current_url()) ?>">
                        <i class="fa fa-check"></i> Confirm Unloading
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK_EDIT)): ?>
                <li role="separator" class="divider action-delete"></li>
                <li class="action-delete">
                    <a href="<?= site_url('transporter-entry-permit-tracking/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Tracking Link (not delete actual TEP)"
                       data-label="Link {{tep_code}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/tep-tracking-link.js?v=3') ?>" defer></script>