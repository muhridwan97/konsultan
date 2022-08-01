<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Complains</h3>

        <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_CREATE)): ?>
            <a href="<?= site_url('complain/create') ?>" class="btn btn-primary pull-right">
                Create Complain
            </a>
        <?php endif; ?>

	</div>
	<div class="box-body">

        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" id="table-complain">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>Complain Code</th>
                    <th>Complain Category</th>
                    <th>Customer Name</th>
                    <th>Via</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Complain Date</th>
                    <th>Close Date</th>
					<th style="width: 60px">Action</th>
				</tr>


			</thead>
			<tbody>
				<?php $no = 1;
                $dataLabel = [
                    ComplainModel::STATUS_CLOSED => 'info',
                    ComplainModel::STATUS_ON_REVIEW => 'warning',
                    ComplainModel::STATUS_APPROVE => 'info',
                    ComplainModel::STATUS_SUBMITTED => 'default',
                    ComplainModel::STATUS_DISPROVE => 'warning',
                    ComplainModel::STATUS_FINAL => 'danger',
                    ComplainModel::STATUS_FINAL_CONCLUSION => 'warning',
                    ComplainModel::STATUS_PROCESSED => 'primary',
                    ComplainModel::STATUS_CONCLUSION => 'success',
                    ComplainModel::STATUS_PENDING => 'default',
                    ComplainModel::STATUS_REJECT => 'danger',
                    ComplainModel::STATUS_ACCEPTED => 'success',
                ];
				foreach ($complains as $complain): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $complain['no_complain'] ?></td>
                    <td class="responsive-title"><?= if_empty($complain['category'], 'Not set yet') ?></td>
                    <td class="responsive-title"><?= $complain['customer_name'] ?></td>
                    <td class="responsive-title"><?= $complain['via'] ?></td>
                    <td class="responsive-title"><?= if_empty($complain['department'], '-') ?></td>
                    <td class="responsive-title">
                        <!-- <?php if(empty($complain['department']) && empty($complain['investigation_result'])): ?>
                            <span class="label label-default"> Submitted </span>
                        <?php else: ?>
                            <?php if(!empty($complain['department']) && empty($complain['investigation_result'])): ?>
                                <span class="label label-warning"> On Review </span>
                            <?php else: ?>
                                <?php if(!empty($complain['department']) && !empty($complain['investigation_result']) && empty($complain['close_date'])): ?>
                                    <span class="label label-primary"> Processed </span>
                                <?php else: ?>
                                    <?php if(!empty($complain['close_date'])): ?>
                                        <?php if(empty($complain['attachment'])): ?>
                                            <span class="label label-info"> Closed </span>
                                        <?php else: ?>
                                            <span class="label label-success"> Uploaded </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="label label-danger"> Pending </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>    
                        <?php endif; ?>       -->
                        <span class="label label-<?= $dataLabel[$complain['status']] ?>"> 
                            <?=$complain['status']?> </span>  
                        <?php if(!empty($complain['status_investigation']) && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                        </br>
                        <span class="label label-<?= $dataLabel[$complain['status_investigation']] ?>"> 
                            <?=$complain['status_investigation']?> </span>  
                        <?php endif; ?>
                    </td>
                    <td><?= readable_date($complain['complain_date']) ?></td>
                    <td><?= readable_date($complain['close_date']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if(empty($complain['department']) && empty($complain['investigation_result'])): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                                        <li>
                                            <a href="<?= site_url('complain/edit/' . $complain['id']) ?>">
                                                <i class="fa ion-compose"></i>Edit
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if(!empty($complain['department']) && empty($complain['investigation_result']) && ($complain['department'] == $profil['department'] || AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_ADMIN) || UserModel::authenticatedUserData('position_level') == 'DIRECTOR')): ?>
                                    <?php if (AuthorizationModel::isAuthorizedByBranch(PERMISSION_COMPLAIN_INVESTIGATION_CREATE,$complain['id_branch'])): ?>
                                        <li>
                                            <a href="<?= site_url('complain/investigation/' . $complain['id']) ?>">
                                                <i class="fa ion-compose"></i> Investigation
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(!empty($complain['department']) && !empty($complain['investigation_result'])): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_VIEW)): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view_investigation/' . $complain['id']) ?>">
                                                <i class="fa ion-search"></i> Investigation View
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($complain['status'] == ComplainModel::STATUS_DISPROVE || ($complain['status'] == ComplainModel::STATUS_PROCESSED && $complain['status_investigation'] == ComplainModel::STATUS_REJECT && !empty($complain['conclusion']))): ?>
                                    <?php if (AuthorizationModel::isAuthorizedByBranch(PERMISSION_COMPLAIN_INVESTIGATION_CREATE,$complain['id_branch']) && ($complain['department'] == $profil['department'] || AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_ADMIN) || UserModel::authenticatedUserData('position_level') == 'DIRECTOR')): ?>
                                        <li>
                                            <a href="<?= site_url('complain/response/' . $complain['id']) ?>">
                                                <i class="fa ion-reply"></i>Response
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($complain['status_investigation'] == ComplainModel::STATUS_PENDING && empty($complain['conclusion'])): ?>
                                    <?php if (($complain['department'] == $profil['department'] || AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_ADMIN) || UserModel::authenticatedUserData('position_level') == 'DIRECTOR') && (UserModel::authenticatedUserData('position_level') == 'MANAGER' || UserModel::authenticatedUserData('position_level') == 'DIRECTOR')): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view_investigation/' . $complain['id']) ?>">
                                                <i class="fa ion-checkmark"></i>Approval
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($complain['status_investigation'] == ComplainModel::STATUS_PENDING && !empty($complain['conclusion'])): ?>
                                    <?php if (($complain['department'] == $profil['department'] || AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_ADMIN) || UserModel::authenticatedUserData('position_level') == 'DIRECTOR') && (UserModel::authenticatedUserData('position_level') == 'MANAGER') || UserModel::authenticatedUserData('position_level') == 'DIRECTOR'): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view_response/' . $complain['id']) ?>">
                                                <i class="fa ion-checkmark"></i>Approval
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($complain['status_investigation'] == ComplainModel::STATUS_REJECT  && empty($complain['conclusion']) && ($complain['department'] == $profil['department'] || AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_ADMIN) || UserModel::authenticatedUserData('position_level') == 'DIRECTOR')): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_EDIT)): ?>
                                        <li>
                                            <a href="<?= site_url('complain/edit_investigation/' . $complain['id']) ?>">
                                                <i class="fa ion-compose"></i>Edit Investigation
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            

                                <?php if($complain['status_investigation'] == ComplainModel::STATUS_APPROVE && $complain['status'] == ComplainModel::STATUS_PROCESSED): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                                <i class="fa fa-legal"></i>Conclusion
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($complain['status'] == ComplainModel::STATUS_CONCLUSION): ?>
                                    <?php if (UserModel::authenticatedUserData('id_person') == $complain['id_customer']): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                                <i class="fa fa-volume-control-phone"></i>Disprove
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                                <i class="fa ion-checkmark"></i>Accept
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                                    <?php if(in_array($complain['status'], [ComplainModel::STATUS_CONCLUSION, ComplainModel::STATUS_DISPROVE])): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                                <i class="fa ion-checkmark-circled"></i>Set Final
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(in_array($complain['status'], [ComplainModel::STATUS_ACCEPTED, ComplainModel::STATUS_FINAL])): ?>
                                        <?php if($complain['allow_set_final_conclusion'] || $complain['status'] == ComplainModel::STATUS_ACCEPTED): ?>
                                            <li>
                                                <a href="<?= site_url('complain/edit-conclusion-category/' . $complain['id']) ?>">
                                                    <i class="fa ion-compose"></i>Edit Conclusion Category
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="disabled">
                                                <a href="<?= site_url('complain/view/' . $complain['id']) ?>" onclick="event.preventDefault()" class="disabled" data-toggle="tooltip" data-title="Waiting response until <?= $complain['max_date_waiting'] ?? 'indefinitely' ?>">
                                                    <i class="fa ion-compose"></i>Edit Conclusion Category
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(UserModel::authenticatedUserData('id_person') == $complain['id_customer']): ?>
                                    <?php if($complain['status'] == ComplainModel::STATUS_FINAL): ?>
                                        <li>
                                            <a href="<?= site_url('complain/view/' . $complain['id']) ?>">
                                                <i class="fa ion-reply"></i>Add Response
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($complain['status'] == ComplainModel::STATUS_FINAL_CONCLUSION || ($complain['status'] == ComplainModel::STATUS_CLOSED && empty($complain['rating']))): ?>
                                        <li>
                                            <a href="<?= site_url('complain/rating/' . $complain['id']) ?>"  class="btn-rating"
                                               data-id="<?= $complain['id'] ?>"
                                               data-title="Complain"
                                               data-rating="<?= $complain['rating'] ?>"
                                               data-rating-reason="<?= $complain['rating_reason'] ?>"
                                               data-label="<?= $complain['no_complain'] ?>">
                                                <i class="fa fa-star"></i> <?= empty($complain['rating']) ? 'Give' : 'Update' ?> rating
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(in_array($complain['status'], [ComplainModel::STATUS_FINAL_CONCLUSION])): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_VALIDATE)): ?>
                                        <li>
                                            <a href="<?= site_url('complain/close/' . $complain['id']) ?>"  class="btn-validate"
                                               data-id="<?= $complain['id'] ?>"
                                               data-title="Complain"
                                               data-label="<?= $complain['no_complain'] ?>">
                                                <i class="fa fa-check"></i> Close
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(!empty($complain['close_date'])): ?>

                                    <?php if(empty($complain['attachment'])): ?>
                                         <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_PRINT)): ?>
                                            <li>
                                                <a href="<?= site_url('complain/print/' . $complain['id']) ?>">
                                                    <i class="fa fa-print"></i> Print
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_UPLOAD)): ?>
                                            <li>
                                                 <a href="<?= site_url('complain/upload/' . $complain['id']) ?>" class="btn-upload" data-label="<?= $complain['no_complain'] ?>">
                                                    <i class="fa fa-print"></i> Upload Bukti Keluhan
                                                 </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_RESULT)): ?>
                                            <li>
                                                <a href="<?= site_url('complain/result/' . $complain['id']) ?>">
                                                    <i class="fa ion-search"></i> Result
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('complain/delete/' . $complain['id']) ?>" class="btn-delete"
                                           data-id="<?= $complain['id'] ?>"
                                           data-title="Complain"
                                           data-label="<?= $complain['no_complain'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validate <span class="validate-title"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="effective"> Effective : &nbsp;</label>
                            <input type="radio" class="form-control" name="effective" value="YA" checked> <span>&nbsp; YES &nbsp;</span>
                            <input type="radio" class="form-control" name="effective" value="TIDAK"> <span>&nbsp; NO </span>
                        </div>
                        <div class="form-group">
                            <label for="ftkp">FTKP</label>
                            <textarea class="form-control" disabled placeholder="Enter FTKP" maxlength="500"><?= set_value('ftkp', $ftkp_number) ?></textarea>
                            <input type="hidden" name="ftkp" value="<?= $ftkp_number; ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_PRINT)): ?>
    <?php $this->load->view('complain/_modal_complain'); ?>
<?php endif; ?>
<?php $this->load->view('complain/_modal_rating'); ?>
<script src="<?= base_url('assets/app/js/complain.js?v=1') ?>" defer></script>