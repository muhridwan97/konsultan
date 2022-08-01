<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Items</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <a href="#filter_item_compliance" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_item_compliance', 1) ? 'Hide' : 'Show' ?> Filter
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ITEM_COMPLIANCE_CREATE)): ?>
                <a href="<?= site_url('item_compliance/create') ?>" class="btn btn-primary">
                    Create Item
                </a>
            <?php endif ?>
        </div>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <?php $this->load->view('item_compliance/_filter', [
            'filter_item_compliance' => 'filter_item_compliance',
            'hidden' => false
        ]) ?>
		<table class="table table-bordered table-striped table-ajax responsive" id="table-item-compliance">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>Name</th>
					<th>HS</th>
					<th>Unit</th>
                    <th>Customer</th>
                    <th class="type-action" style="width: 60px">Action</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<script id="control-item-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ITEM_COMPLIANCE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('item-compliance/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ITEM_COMPLIANCE_EDIT)): ?>
                <li>
                    <a href="<?= site_url('item-compliance/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ITEM_COMPLIANCE_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('item-compliance/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-id="{{id}}"
                       data-title="Item Compliance"
                       data-label="{{item}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_ITEM_COMPLIANCE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/item-compliance.js') ?>" defer></script>