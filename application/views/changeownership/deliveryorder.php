<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Ownership Histories <strong><?= $ownershiphistories[0]['no_delivery_order'] ?></strong></h3>
        <a href="<?= site_url('change_ownership/create/'.$ownershiphistories[0]['id_delivery_order']) ?>" class="btn btn-primary pull-right">
            <i class="fa ion-plus-round"></i> Change Ownership
		</a>
	</div>
    <!-- /.box-header -->
    <div class="box-body">
        <!-- alert -->
        <?php if ($this->session->flashdata('status') != NULL): ?>
		<div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
			</button>
			<p><?= $this->session->flashdata('message'); ?></p>
		</div>
        <?php endif; ?>
        <!-- end of alert -->
		
        <table class="table table-bordered table-striped" id="table-history">
            <thead>
				<tr>
					<th>No</th>
					<th>No DO</th>
					<th>Owner</th>
					<th>Change Date</th>
					<th>Description</th>
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
            <tbody>
				<?php $no = 1;
				foreach ($ownershiphistories as $ownershiphistory): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $ownershiphistory['no_delivery_order'] ?></td>
                    <td><?= $ownershiphistory['name'] ?></td>
                    <td><?= $ownershiphistory['change_date'] ?></td>
                    <td><?= $ownershiphistory['description'] ?></td>
                    <td>
                        <!-- Single button -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('change_ownership/view/' . $ownershiphistory['id']) ?>">
                                            <i class="fa ion-ios-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if((end($ownershiphistories)['id'] == $ownershiphistory['id']) && (end($ownershiphistories)['id'] != reset($ownershiphistories)['id'])) : ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_EDIT)): ?>
                                        <li>
                                            <a href="<?= site_url('change_ownership/edit/' . $ownershiphistory['id']) ?>">
                                                <i class="fa ion-ios-compose-outline"></i>Edit
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_DELETE)): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="<?= site_url('change_ownership/delete/' . $ownershiphistory['id']) ?>"
                                               class="btn-delete-ownershiphistory"
                                               data-id="<?= $ownershiphistory['id'] ?>"
                                               data-label="<?= $ownershiphistory['name'] ?>">
                                                <i class="fa ion-ios-trash-outline"></i> Delete
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
				</tr>
				<?php endforeach; ?>
			</tbody>
            <tfoot>
				<tr>
					<th>No</th>
					<th>No DO</th>
					<th>Owner</th>
					<th>Change Date</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
			</tfoot>
		</table>
	</div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-history">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
					</button>
                    <h4 class="modal-title">Delete History</h4>
				</div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to delete history
					<strong id="history-title"></strong>?</p>
                    <p class="small text-danger">
                        This action will perform soft delete, actual data still exist on database.
					</p>
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete History</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?= base_url('assets/app/js/ownership.js') ?>" defer></script>