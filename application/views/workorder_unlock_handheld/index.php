<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Work Order Overtime Data</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-unlock-handheld" class="btn btn-info btn-filter-toggle">
                <?= !empty($_GET) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= site_url('work-order-unlock-handheld/unlock') ?>" class="btn btn-primary">
                Create Unlock
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('workorder_unlock_handheld/_filter', ['hidden' => !empty($_GET) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-work-order-unlock-handheld">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Work Order</th>
                <th>Customer</th>
                <th class="type-date">Unlocked Until</th>
                <th>Description</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-work-order-unlock-handheld-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_UNLOCK_HANDHELD)): ?>
                <li>
                    <a href="<?= site_url('work-order-unlock-handheld/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order-unlock-handheld/unlock/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('work-order-unlock-handheld/delete/{{id}}') ?>"
                       class="btn-delete" data-title="Unlock" data-label="{{no_work_order}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php $this->load->view('template/_modal_delete'); ?>
<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/work-order-unlock-handheld.js') ?>" defer></script>