<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Complain Categories</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CATEGORY_CREATE)): ?>
                <a href="<?= site_url('complain-category/create') ?>" class="btn btn-primary">
                    Create Complain Category
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
					<th>Value Type</th>
                    <th>Category Type</th>
                    <th>Complain Category</th>
					<th>Created At</th>
					<th style="width: 60px">Action</th>
				</tr>


			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($complainCategories as $complainCategory): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $complainCategory['value_type'] ?></td>
                    <td><?= $complainCategory['category_type'] ?></td>
                    <td><?= $complainCategory['category'] ?></td>
                    <td><?= readable_date($complainCategory['created_at']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CATEGORY_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('complain-category/view/' . $complainCategory['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CATEGORY_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('complain-category/edit/' . $complainCategory['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CATEGORY_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('complain-category/delete/' . $complainCategory['id']) ?>" class="btn-delete"
                                           data-id="<?= $complainCategory['id'] ?>"
                                           data-title="Complain Category"
                                           data-label="<?= $complainCategory['category'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CATEGORY_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>