<div class="box box-primary">
	<div class="box-header">
        <form action="<?= site_url('put-away/create/') ?>" class="form" method="post">
		<h3 class="box-title">Put Away Audit</h3>
        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_CREATE)): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
                <button type="submit" class="btn btn-primary">Generate Put Away</button>
                <p><?= $this->session->flashdata('message_check'); ?></p>
            <?php endif; ?>
        </div>
        </form>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" data-page-length="10" id="table-unit">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>Branch</th>	
                    <th>Put Away Number</th> 
					<th>Put Away Date</th>
                    <th>Put Away Shift</th>
                    <th>Put Away Status</th>	
                    <th>Put Away Description</th>    
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;?>
				<?php foreach($putAway as $audit): ?>
                <?php 
                    $dateTimeNow = strtotime(date('Y-m-d')); 
                    $putAwayDate = strtotime(format_date($audit['put_away_date'], 'Y-m-d'));
                ?>
				<tr> 
					<td><?= $no++ ?></td>
					<td> 
						<a href="<?= site_url('branch/view/' . $audit['id_branch']) ?>">
                            <?= $audit['branch'] ?>
                        </a>
					</td>
                    <td><?= ($audit['no_put_away']) ?></td>
					<td><?= format_date($audit['put_away_date'], 'd F Y') ?></td>
                    <td><?= ($audit['shift']) ?> (<?=format_date($audit['start'],'H:i')?> - <?=format_date($audit['end'],'H:i')?>)</td>
                    <td>
                        <?php
                            $labelStatus = [
                                'PENDING' => 'default',
                                'PROCESSED' => 'primary',
                                'REOPENED' => 'primary',
                                'APPROVED' => 'success',
                                'REJECTED' => 'danger',
                                'NOT PROCESSED' => 'danger',
                                'NOT VALIDATED' => 'warning',
                                'VALIDATED' => 'success',
                            ];
                        ?>
                        <span class="label label-<?= $labelStatus[$audit['status']]?>">
                            <?= $audit['status']  ?>
                        </span>
                    </td>
                    <td><?= if_empty($audit['description'], 'No Description') ?></td>
					<td>
						 <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/view/' . $audit['id']) ?>">
                                            <i class="fa ion-search"></i>View Details
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_PRINT)): ?>
                                    <!-- <li>
                                        <a href="<?= site_url('put-away/print/' . $audit['id']) ?>">
                                            <i class="fa fa-print"></i>Print Job Put Away
                                        </a>
                                    </li> -->
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_PRINT_RESULT)): ?>
                                    <?php if(($audit['status'] == PutAwayModel::STATUS_PROCESSED) || ($audit['status'] == PutAwayModel::STATUS_APPROVED)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/print_result/' . $audit['id']) ?>">
                                            <i class="fa fa-print"></i>Print Result Put Away
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_PROCESS)): ?>
                                    <?php if( ($audit['status'] == PutAwayModel::STATUS_PENDING) || ($audit['status'] == PutAwayModel::STATUS_REOPENED)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/process/' . $audit['id']) ?>">
                                            <i class="fa fa-pencil"></i>Process
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_RESULT)): ?>
                                    <?php if(($audit['status'] == PutAwayModel::STATUS_PROCESSED) || ($audit['status'] == PutAwayModel::STATUS_APPROVED) || ($audit['status'] == PutAwayModel::STATUS_NOT_VALIDATED) || ($audit['status'] == PutAwayModel::STATUS_VALIDATED)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/result/' . $audit['id']) ?>">
                                            <i class="fa fa-search-plus"></i>View Result Put Away
                                        </a>
                                    </li>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_VALIDATE) && ($audit['status'] == PutAwayModel::STATUS_PROCESSED)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/result/' . $audit['id']) ?>">
                                            <i class="fa ion-checkmark"></i>Validate
                                        </a>
                                    </li>
                                    <?php endif; ?>                                    
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_ACCESS)): ?>
                                    <?PHP if(($audit['status'] == PutAwayModel::STATUS_REJECTED) || ($audit['status'] == PutAwayModel::STATUS_PROCESSED) ||  ($audit['status'] == PutAwayModel::STATUS_NOT_PROCESSED)): ?>
                                    <li>
                                        <a href="<?= site_url('put-away/reopen/'. $audit['id']) ?>"
                                           class="btn-validate"
                                           data-validate="reopen"
                                           data-label="{{date}}">
                                            <i class="fa ion-checkmark"></i> Re Open Process
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('put-away/delete/' . $audit['id']) ?>" class="btn-delete"
                                           data-id="<?= $audit['id'] ?>"
                                           data-title="put_away"
                                           data-label="<?= $audit['id'] ?>">
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
<?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
   <?php $this->load->view('put_away/_modal_put_away') ?>
<?php endif; ?>

<script type="text/javascript">
    $(document).on('click', '.btn-generate', function (e) {
        e.preventDefault();

        var urlCheck = $(this).attr('href');

        var modalCheckOut = $('#modal-put-away');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
</script>
