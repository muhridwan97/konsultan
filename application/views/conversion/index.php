<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Conversions</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CONVERSION_CREATE)): ?>
                <a href="<?= site_url('conversion/create') ?>" class="btn btn-primary">
                    Create Conversion
                </a>
            <?php endif ?>
        </div>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" id="table-conversion">
			<thead>
				<tr>
					<th style="width: 30px">ID</th>
					<th>Goods</th>
					<th>Unit From</th>
					<th>Value</th>
					<th>Unit To</th>
					<th>Created At</th>
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($conversions as $conversion): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $conversion['name'] ?></td>
                    <td><?= $conversion['unit_from'] ?></td>
                    <td><?= $conversion['value'] ?></td>
                    <td><?= $conversion['unit_to'] ?></td>
                    <td><?= format_date($conversion['created_at'], 'd F Y H:i') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CONVERSION_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('conversion/view/' . $conversion['id']) ?>">
                                            <i class="fa ion-ios-search"></i>View
                                        </a>
                                    </li>
                                <?php endif ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CONVERSION_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('conversion/edit/' . $conversion['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CONVERSION_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('conversion/delete/' . $conversion['id']) ?>" class="btn-delete"
                                        data-id="<?= $conversion['id'] ?>"
                                        data-title="Conversion"
                                        data-label="<?= $conversion['name'] ?> <?= $conversion['unit_from'] ?> to <?= $conversion['unit_to'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif ?>
							</ul>
						</div>
					</td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>


<?php if (AuthorizationModel::isAuthorized(PERMISSION_CONVERSION_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>