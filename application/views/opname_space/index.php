<div class="box box-primary">
	<div class="box-header">
        <form action="<?= site_url('opname-space/create/') ?>" class="form" method="post">
		<h3 class="box-title">Opname Space</h3>
        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_CREATE)): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
                <button type="submit" class="btn btn-primary">Generate Opname Space</button>
                <p><?= $this->session->flashdata('message_check'); ?></p>
            <?php endif; ?>
        </div>
        </form>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" data-page-length="10" id="table-opname-space">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>Branch</th>	
                    <th>Opname Space Number</th> 
					<th>Opname Space Date</th>
                    <th>Opname Space Status</th>	
                    <th>Opname Space Description</th>    
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;?>
				<?php foreach($opnameSpaces as $opnameSpace): ?>
                <?php 
                    $dateTimeNow = strtotime(date('Y-m-d')); 
                    $opnameSpaceDate = strtotime(format_date($opnameSpace['opname_space_date'], 'Y-m-d'));
                ?>
				<tr> 
					<td><?= $no++ ?></td>
					<td> 
						<a href="<?= site_url('branch/view/' . $opnameSpace['id_branch']) ?>">
                            <?= $opnameSpace['branch'] ?>
                        </a>
					</td>
                    <td><?= ($opnameSpace['no_opname_space']) ?></td>
					<td><?= format_date($opnameSpace['opname_space_date'], 'd F Y') ?></td>
                    <td>
                        <?php
                            $labelStatus = [
                                'PENDING' => 'default',
                                'PROCESSED' => 'primary',
                                'REOPENED' => 'primary',
                                'APPROVED' => 'success',
                                'REJECTED' => 'danger',
                                'NOT PROCESSED' => 'warning',
                                'COMPLETED' => 'success',
                                'VALIDATED' => 'primary',
                            ];
                        ?>
                        <span class="label label-<?= $dateTimeNow != $opnameSpaceDate && $opnameSpace['status'] == ($opnameSpace['status'] == OpnameSpaceModel::STATUS_PENDING || $opnameSpace['status'] == OpnameSpaceModel::STATUS_REOPENED) ? $labelStatus['NOT PROCESSED'] : $labelStatus[$opnameSpace['status']]?>">
                            <?= $dateTimeNow != $opnameSpaceDate && ($opnameSpace['status'] == OpnameSpaceModel::STATUS_PENDING || $opnameSpace['status'] == OpnameSpaceModel::STATUS_REOPENED) ? 'NOT PROCESSED' : $opnameSpace['status']  ?>
                        </span>
                    </td>
                    <td><?= if_empty($opnameSpace['description'], 'No Description') ?></td>
					<td>
						 <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('opname-space/view/' . $opnameSpace['id']) ?>">
                                            <i class="fa ion-search"></i>View Details
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_EDIT)): ?>
                                    <?php if( (($opnameSpace['status'] == OpnameSpaceModel::STATUS_COMPLETED)) && $opnameSpaceDate == $dateTimeNow): ?>
                                    <li>
                                        <a href="<?= site_url('opname-space/edit/' . $opnameSpace['id']) ?>">
                                            <i class="fa fa-edit"></i>Edit
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_PROCESS)): ?>
                                    <?php if( (($opnameSpace['status'] == OpnameSpaceModel::STATUS_PENDING) || ($opnameSpace['status'] == OpnameSpaceModel::STATUS_PROCESSED) || ($opnameSpace['status'] == OpnameSpaceModel::STATUS_REOPENED)) && $opnameSpaceDate == $dateTimeNow): ?>
                                    <li>
                                        <a href="<?= site_url('opname-space/process/' . $opnameSpace['id']) ?>">
                                            <i class="fa fa-pencil"></i>Process
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_PROCESS)): ?>
                                    <?php if( ($opnameSpace['status'] == OpnameSpaceModel::STATUS_PROCESSED) && $opnameSpaceDate == $dateTimeNow ): ?>
                                    <li>
                                        <a href="<?= site_url('opname-space/edit-status/' . $opnameSpace['id']) ?>">
                                            <i class="fa fa-thumbs-o-up"></i>Complete
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE) && ($opnameSpace['status'] != OpnameSpaceModel::STATUS_PENDING)): ?>
                                    <li>
                                        <a href="<?= site_url('')?>report/over-capacity-opname?filter_over_capacity=1&opname=<?= $opnameSpace['id'] ?>" target="_blank">
                                            <i class="fa ion-checkmark"></i>Review
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (($opnameSpace['status'] == OpnameSpaceModel::STATUS_COMPLETED)): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE) && $opnameSpaceDate == $dateTimeNow): ?>
                                        <li>
                                            <a href="<?= site_url('opname-space/validate/valid/' . $opnameSpace['id']) ?>"
                                                class="btn-validate-opname-space"
                                                data-id="<?= $opnameSpace['id'] ?>"
                                                data-label="<?= $opnameSpace['no_opname_space'] ?>">
                                                <i class="fa ion-checkmark"></i>Validate
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('opname-space/delete/' . $opnameSpace['id']) ?>" class="btn-delete-opname-space"
                                           data-id="<?= $opnameSpace['id'] ?>"
                                           data-title="opname_space"
                                           data-label="<?= $opnameSpace['no_opname_space'] ?>">
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
<?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE)): ?>
   <?php $this->load->view('opname_space/_modal_opname_validate') ?>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_SPACES_DELETE)): ?>
   <?php $this->load->view('opname_space/_modal_opname_delete') ?>
<?php endif; ?>
<script type="text/javascript">
    $(document).on('click', '.btn-generate', function (e) {
        e.preventDefault();

        var urlCheck = $(this).attr('href');

        var modalCheckOut = $('#modal-opname-space');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
</script>

<script src="<?= base_url('assets/app/js/opname_space.js') ?>" defer></script>
