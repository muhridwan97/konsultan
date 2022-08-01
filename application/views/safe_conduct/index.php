<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Safe Conduct</h3>
        <div class="pull-right">
            <form action="<?= site_url('safe-conduct/index') ?>" id="form-safe-conduct-filter" style="display: inline-block">
                <?php
                $filterType = isset($_GET['type']) ? $_GET['type'] : '';
                ?>
                <select class="select2" name="type" id="type">
                    <option value="ALL">ALL DATA</option>
                    <option value="INBOUND" <?= $filterType == 'INBOUND' ? 'selected' : '' ?>>INBOUND</option>
                    <option value="OUTBOUND" <?= $filterType == 'OUTBOUND' ? 'selected' : '' ?>>OUTBOUND</option>
                </select>
            </form>
            <a href="<?= site_url('safe-conduct-group') ?>" class="btn btn-info">
                View Group
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_CREATE) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_CREATE)): ?>
                <a href="<?= site_url('safe-conduct/create') ?>" class="btn btn-primary">
                    Create
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-safe-conduct">
            <thead>
            <tr>
                <th>No</th>
                <th class="type-no-safe-conduct">No Safe Conduct</th>
                <th>No Booking</th>
                <th>Type</th>
                <th>Expedition Type</th>
                <th>No Police</th>
                <th>Driver</th>
                <th>Loading</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-safe-conduct-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-safe-conduct"
            data-id="{{id}}"
            data-no="{{no_safe_conduct}}"
            data-print-total="{{print_total}}"
            data-print-max="{{print_max}}">

            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_VIEW) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('safe-conduct/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View Detail
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('safe-conduct/view-container-submission/{{id}}') ?>">
                        <i class="fa ion-search"></i> View Submission
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_PRINT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW)): ?>
                <li>
                    <a class="btn-print-safe-conduct" href="<?= site_url('safe-conduct/print-safe-conduct/{{id}}?redirect=' . base_url(uri_string())) ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('safe-conduct/print-safe-conduct-mode2/{{id}}?redirect=' . base_url(uri_string())) ?>">
                        <i class="fa fa-print"></i> Print Mode 2
                    </a>
                </li>
                <li class="print_eir">
                    <a href="<?= site_url('work-order/print-eir/{{id_work_order}}?redirect=' . base_url(uri_string())) ?>"
                       class="btn-print-eir">
                        <i class="fa fa-print"></i> Print EIR
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_UPDATE_DATA)): ?>
                <li class="edit">
                    <a href="<?= site_url('safe-conduct/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
                <li class="update-data">
                    <a href="<?= site_url('safe-conduct/edit/{{id}}?allowgoods=true') ?>">
                        <i class="fa ion-compose"></i> Edit Loading
                    </a>
                </li>
                <li class="edit-tps-data">
                    <a href="<?= site_url('safe-conduct/edit-tps/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit TPS
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('safe-conduct/update-print-max/{{id}}') ?>"
                       class="btn-update-max-print">
                        <i class="fa ion-compose"></i> Update Max Print
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('safe-conduct/upload-attachment/{{id}}') ?>" class="btn-attachment-safe-conduct">
                        <i class="fa ion-upload"></i> Upload Handover
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_EDIT)): ?>
                <li class="update-desc">
                    <a href="<?= site_url('safe-conduct/edit-safe-conduct/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit Safe Conduct
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT)): ?>
                <li class="update-desc">
                    <a href="<?= site_url('safe-conduct/edit-description/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit Description
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_DELETE) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('safe-conduct/delete/{{id}}') ?>"
                       class="btn-delete-safe-conduct"
                       data-id="{{id}}"
                       data-label="{{no_safe_conduct}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT)): ?>
    <?php $this->load->view('safe_conduct/_modal_update_max_print') ?>
    <?php $this->load->view('safe_conduct/_modal_attachment') ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_DELETE) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT)): ?>
    <?php $this->load->view('safe_conduct/_modal_confirm_delete') ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_PRINT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT)): ?>
    <?php $this->load->view('safe_conduct/_modal_confirm_print') ?>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/safe_conduct.js?v=19') ?>" defer></script>
