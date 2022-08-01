<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Safe Conduct Handover</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('safe_conduct_handover/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-safe-conduct-handover">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-safe-conduct">No Safe Conduct</th>
                <th class="type-tep">Tep Code</th>
                <th class="type-vehicle">No Vehicle</th>
                <th class="type-date-time">Received Date</th>
                <th class="type-date-time">Driver Handover</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_EDIT)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-safe-conduct-handover-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <li class="action-view">
                <a href="<?= site_url('safe-conduct-handover/view/{{id}}') ?>">
                    <i class="fa ion-search"></i> View
                </a>
            </li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER)): ?>
                <li class="action-handover">
                    <a href="<?= site_url('safe-conduct-handover/create/{{id_safe_conduct}}') ?>">
                        <i class="fa fa-hand-paper-o"></i> Handover
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized([PERMISSION_SAFE_CONDUCT_EDIT, PERMISSION_SAFE_CONDUCT_HANDOVER])): ?>
                <li class="action-edit<?= AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_EDIT) ? ' allow-edit-all' : '' ?>">
                    <a href="<?= site_url('safe-conduct-handover/edit/{{id}}') ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li role="separator" class="divider action-delete"></li>
                <li class="action-delete">
                    <a href="<?= site_url('safe-conduct-handover/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Handover safe conduct (not delete actual TEP)"
                       data-label="Handover {{no_safe_conduct}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/safe-conduct-handover.js?v=2') ?>" defer></script>
<?php $this->load->view('template/_modal_delete') ?>
<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>