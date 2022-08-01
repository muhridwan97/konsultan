<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Target</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TARGET_CREATE)): ?>
                <a href="<?= site_url('target/create') ?>" class="btn btn-primary">
                    Create Target
                </a>
            <?php endif; ?>
        </div>
	</div>
	<div class="box-body">

        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" id="table-unit">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>Name</th>
                    <th>Description</th>
					<th>Created At</th>
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($targets as $target): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= values($target['target_name'],'-') ?></td>
                    <td class="responsive-title"><?= values($target['description'],'No description') ?></td>
                    <td><?= readable_date($target['created_at']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_TARGET_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('target/view/' . $target['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_TARGET_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('target/edit/' . $target['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_TARGET_DELETE) && $target['is_reserved']!=1): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('target/delete/' . $target['id']) ?>" class="btn-delete"
                                           data-id="<?= $target['id'] ?>"
                                           data-title="Target"
                                           data-label="<?= $target['description'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif; ?>

							</ul>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_TARGET_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>