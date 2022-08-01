<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            TEP Request Release
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
            <a href="<?= site_url('transporter-entry-permit-request-release/create') ?>" class="btn btn-primary">
                Request Release
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('transporter_entry_permit_request_release/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-tep-request-release">
            <thead>
            <tr class="text-nowrap">
                <th style="width: 30px">No</th>
                <th class="type-tep">No Release Ref</th>
                <th>Customer</th>
                <th class="type-hold-type">Type</th>
                <th>Description</th>
                <th class="type-goods">Goods</th>
                <th class="type-hold-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php $this->load->view('template/_modal_delete'); ?>
<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>

<script id="control-tep-request-release-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <li class="action-view">
                <a href="<?= site_url('transporter-entry-permit-request-release/view/{{id}}') ?>">
                    <i class="fa ion-search"></i> View
                </a>
            </li>
            <li role="separator" class="divider action-delete"></li>
            <li class="action-delete">
                <a href="<?= site_url('transporter-entry-permit-request-release/delete/{{id}}') ?>"
                   class="btn-delete"
                   data-title="Request Released"
                   data-label="{{no_hold_reference}}">
                    <i class="fa ion-trash-a"></i> Delete
                </a>
            </li>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/tep-request-release.js?v=1') ?>" defer></script>