<div class="box box-primary">
	<div class="box-header">
        <form action="<?= site_url('cycle-count/create/') ?>" class="form" method="post">
		<h3 class="box-title">Cycle Count</h3>
        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_CREATE)): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
                <button type="submit" class="btn btn-primary">Generate Cycle Count</button>
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
                    <th>Cycle Count Number</th> 
					<th>Cycle Count Date</th>
                    <th>Cycle Count Type</th>
                    <th>Cycle Count Status</th>	
                    <th>Cycle Count Description</th>    
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;?>
				<?php foreach($cycleCounts as $cycleCount): ?>
                <?php 
                    $dateTimeNow = strtotime(date('Y-m-d')); 
                    $cycleCountDate = strtotime(format_date($cycleCount['cycle_count_date'], 'Y-m-d'));
                ?>
				<tr> 
					<td><?= $no++ ?></td>
					<td> 
						<a href="<?= site_url('branch/view/' . $cycleCount['id_branch']) ?>">
                            <?= $cycleCount['branch'] ?>
                        </a>
					</td>
                    <td><?= ($cycleCount['no_cycle_count']) ?></td>
					<td><?= format_date($cycleCount['cycle_count_date'], 'd F Y') ?></td>
                    <td><?= ($cycleCount['type']) ?></td>
                    <td>
                        <?php
                            $labelStatus = [
                                'PENDING' => 'default',
                                'PROCESSED' => 'primary',
                                'REOPENED' => 'primary',
                                'APPROVED' => 'success',
                                'REJECTED' => 'danger',
                                'NOT PROCESSED' => 'warning',
                            ];
                        ?>
                        <span class="label label-<?= $dateTimeNow != $cycleCountDate && $cycleCount['status'] == ($cycleCount['status'] == CycleCountModel::STATUS_PENDING || $cycleCount['status'] == CycleCountModel::STATUS_REOPENED) ? $labelStatus['NOT PROCESSED'] : $labelStatus[$cycleCount['status']]?>">
                            <?= $dateTimeNow != $cycleCountDate && ($cycleCount['status'] == CycleCountModel::STATUS_PENDING || $cycleCount['status'] == CycleCountModel::STATUS_REOPENED) ? 'NOT PROCESSED' : $cycleCount['status']  ?>
                        </span>
                    </td>
                    <td><?= if_empty($cycleCount['description'], 'No Description') ?></td>
					<td>
						 <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/view/' . $cycleCount['id']) ?>">
                                            <i class="fa ion-search"></i>View Details
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_PRINT)): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/print/' . $cycleCount['id']) ?>">
                                            <i class="fa fa-print"></i>Print Job Cycle Count
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_PRINT_RESULT)): ?>
                                    <?php if(($cycleCount['status'] == CycleCountModel::STATUS_PROCESSED) || ($cycleCount['status'] == CycleCountModel::STATUS_APPROVED)): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/print_result/' . $cycleCount['id']) ?>">
                                            <i class="fa fa-print"></i>Print Result Cycle Count
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_PROCESS)): ?>
                                    <?php if( (($cycleCount['status'] == CycleCountModel::STATUS_PENDING) || ($cycleCount['status'] == CycleCountModel::STATUS_REOPENED)) && $cycleCountDate == $dateTimeNow): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/process/' . $cycleCount['id']) ?>">
                                            <i class="fa fa-pencil"></i>Process
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_RESULT)): ?>
                                    <?php if(($cycleCount['status'] == CycleCountModel::STATUS_PROCESSED) || ($cycleCount['status'] == CycleCountModel::STATUS_APPROVED)): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/result/' . $cycleCount['id']) ?>">
                                            <i class="fa fa-search-plus"></i>View Result Cycle Count
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_ACCESS)): ?>
                                    <?PHP if( (($cycleCount['status'] == CycleCountModel::STATUS_REJECTED) || ($cycleCount['status'] == CycleCountModel::STATUS_PROCESSED)) && $cycleCountDate == $dateTimeNow ): ?>
                                    <li>
                                        <a href="<?= site_url('cycle-count/validate/reopen/'. $cycleCount['id']) ?>"
                                           class="btn-validate"
                                           data-validate="reopen"
                                           data-label="{{date}}">
                                            <i class="fa ion-checkmark"></i> Re Open Process
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CYCLE_COUNT_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('cycle-count/delete/' . $cycleCount['id']) ?>" class="btn-delete"
                                           data-id="<?= $cycleCount['id'] ?>"
                                           data-title="cycle_count"
                                           data-label="<?= $cycleCount['id'] ?>">
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
   <?php $this->load->view('cycle_count/_modal_cycle_count') ?>
<?php endif; ?>

<script type="text/javascript">
    $(document).on('click', '.btn-generate', function (e) {
        e.preventDefault();

        var urlCheck = $(this).attr('href');

        var modalCheckOut = $('#modal-cycle-count');
        modalCheckOut.find('form').attr('action', urlCheck);
        modalCheckOut.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
</script>
