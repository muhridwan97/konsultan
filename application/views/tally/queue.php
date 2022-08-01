<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Tally</h3>
    </div>

    <div class="box-body" id="tally-queue-list">
        <input type="hidden" id="tally-validated-edit" value="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">

        <?php $this->load->view('template/_alert') ?>

        <?php if (!$isOvertimeValidationCompleted || !$isOverSpaceValidationCompleted): ?>
            <div class="alert alert-danger">
                There are work order overtimes or storage usage validation are outstanding, please validate first before take new tally!
            </div>
            <script defer>
                $(function () {
                    $('#tally-queue-list')
                        .addClass('text-muted')
                        .find('button, a')
                        .prop('disabled', true)
                        .addClass('disabled');
                });
            </script>
        <?php endif; ?>

        <?php if ($lockComplain): ?>
            <div class="alert alert-danger">
                There are complain investigation are outstanding, please approve first before take new tally!
            </div>
            <script defer>
                $(function () {
                    $('#tally-queue-list')
                        .addClass('text-muted')
                        .find('button, a')
                        .prop('disabled', true)
                        .addClass('disabled');
                });
            </script>
        <?php endif; ?>
        
        <?php if ($isPendingPutAway): ?>
            <div class="alert alert-danger">
                There are put away audit are outstanding, please processed first before take new tally!
            </div>
            <script defer>
                $(function () {
                    $('#tally-queue-list')
                        .addClass('text-muted')
                        .find('button, a')
                        .prop('disabled', true)
                        .addClass('disabled');
                });
            </script>
        <?php endif; ?>

        <div class="list-group" id="tally-queue-list">
            <a href="#" class="list-group-item disabled hidden-xs">
                <div class="row">
                    <div class="col-xs-1 text-center">
                        Queue
                    </div>
                    <div class="col-xs-3">
                        Job Order
                    </div>
                    <div class="col-xs-5">
                        <div class="row">
                            <div class="col-sm-6">Description</div>
                            <div class="col-sm-6">Gate</div>
                        </div>
                    </div>
                    <div class="col-xs-3 text-right">
                        Action
                    </div>
                </div>
            </a>
            <?php if (count($workOrders) > 0): ?>
                <?php foreach ($workOrders as $workOrder): ?>
                    <div data-id="<?= $workOrder['id'] ?>"
                         data-no="<?= $workOrder['no_work_order'] ?>"
                         data-customer="<?= $workOrder['id_customer'] ?>"
                         data-photo="<?php if ($workOrder['id_handling_type'] == $settings['default_inbound_handling']) {
                             if ($workOrder['container']==null) {
                                echo "noContainer";
                             } else {
                                echo "withContainer";
                             }                           
                         } else {
                            echo $workOrder['photo'] ;
                         }?>"
                         data-category="<?= $workOrder['category_booking'] ?>"
                         data-handling-type="<?= $workOrder['handling_type'] ?>"
                         data-container="<?= $workOrder['container'] ?>"
                         data-id-upload="<?= $workOrder['id_upload'] ?>"
                         data-id-handling-type="<?= $workOrder['id_handling_type'] ?>"
                         class="list-group-item queue-list">
                        <div class="row">
                            <div class="col-xs-3 col-sm-1 text-center">
                                <h2 class="list-group-item-heading">
                                    <?= $workOrder['queue'] ?>
                                    <small class="text-muted mt10" style="font-size: 12px; display: block" title="Queue on <?= format_date($workOrder['gate_in_date'], 'd F Y') ?>">
                                        <?= format_date($workOrder['gate_in_date'], 'd/m') ?>
                                    </small>
                                </h2>
                            </div>
                            <div class="col-xs-9 col-sm-3">
                                <h4 class="list-group-item-heading">
                                    <strong><?= $workOrder['no_work_order'] ?></strong>
                                </h4>
                                <p class="list-group-item-text">
                                    <?= ucwords($workOrder['handling_type']) ?> Handling
                                </p>
                                <p class="list-group-item-text text-muted">
                                    <?= if_empty($workOrder['no_reference_inbound'], $workOrder['no_reference']) ?>
                                </p>
                            </div>
                            <div class="col-xs-9 col-xs-push-3 col-sm-5 col-sm-push-0">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Description:</strong><br>
                                        <?= if_empty($workOrder['description'], 'No description') ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Gate In At:</strong><br>
                                        <?= readable_date($workOrder['gate_in_date']) ?>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                $getStatusHistory = $this->statusHistory->getBy(['id_reference' => $workOrder['id'], 'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED]);
                                $getStatusHistory = end($getStatusHistory);
                                $handoverData = json_decode($getStatusHistory['data'], true);
                                $getHandoverBy = $this->userModel->getById($handoverData['handover_user_id']);
                            ?>
                            <div class="col-xs-4 col-xs-push-3 col-sm-3 col-sm-push-0 text-md-right" style="padding-top: 5px">
                                <?php if($workOrder['status'] == WorkOrderModel::STATUS_TAKEN): ?>
                                    <?php if($workOrder['taken_by'] == UserModel::authenticatedUserData('id') || UserModel::authenticatedUserData('username') == 'admin'): ?>
                                        <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED): ?>
                                            <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                                                <button class="btn btn-success btn-handover-approved" data-url="<?= site_url('tally/approve-handover') ?>" style="margin-bottom: 5px" data-user="<?= $getHandoverBy['name'] ?>">
                                                APPROVE HANDOVER &nbsp; <i class="fa fa-check"></i>
                                                </button>
                                            <?php else: ?>
                                                <strong>Handover Pending Approval By Supervisor</strong><br>
                                                at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                            <?php endif; ?>  
                                        <?php else: ?>
                                            <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED): ?>
                                                <?php if($getHandoverBy['id'] !=  UserModel::authenticatedUserData('id')): ?>
                                                    <strong>The Request Handover Has Been Approved By Supervisor</strong><br>
                                                        at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-warning btn-take-handover" style="margin-bottom: 5px" data-url="<?= site_url('tally/take_handover/' . $workOrder['id']) ?>">
                                                        TAKE HANDOVER &nbsp; <i class="fa fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN && $getHandoverBy['id'] !=  UserModel::authenticatedUserData('id')): ?>
                                                    <strong>Handover By <?= $getHandoverBy['name'] ?></strong><br>
                                                    at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-warning btn-req-handover" style="margin-bottom: 5px" data-url="<?= site_url('tally/request_handover/' . $workOrder['id']) ?>">
                                                        HANDOVER &nbsp; <i class="fa fa-share-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-complete-tally" style="margin-bottom: 5px" data-handheld-status="<?= $workOrder['status_unlock_handheld'] ?>" data-url="<?= site_url('tally/complete_job/' . $workOrder['id']) ?>">
                                                        COMPLETE &nbsp; <i class="fa fa-check"></i>
                                                    </button>
                                                    <a href="<?= site_url('tally/create/' . $workOrder['id']) ?>" style="margin-bottom: 5px" class="btn btn-danger btn-continue-tally-check">
                                                        CONTINUE &nbsp; <i class="fa fa-arrow-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED): ?>
                                             <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                                                <button class="btn btn-success btn-handover-approved" data-url="<?= site_url('tally/approve-handover') ?>" style="margin-bottom: 5px" data-user="<?= $getHandoverBy['name'] ?>">
                                                APPROVE HANDOVER &nbsp; <i class="fa fa-check"></i>
                                                </button>
                                            <?php else: ?>
                                                <strong>Handover Pending Approval By Supervisor</strong><br>
                                                at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                            <?php endif; ?>  
                                        <?php else: ?>
                                            <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED): ?>
                                                <?php if($getHandoverBy['id'] !=  UserModel::authenticatedUserData('id')): ?>
                                                    <strong>The Request Handover Has Been Approved By Supervisor</strong><br>
                                                        at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                                <?php else: ?>
                                                    <button class="btn btn-warning btn-take-handover" style="margin-bottom: 5px" data-url="<?= site_url('tally/take_handover/' . $workOrder['id']) ?>">
                                                            TAKE HANDOVER &nbsp; <i class="fa fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN): ?>
                                                    <?php if($getHandoverBy['id'] == UserModel::authenticatedUserData('id')): ?>
                                                        <button class="btn btn-warning btn-req-handover" style="margin-bottom: 5px" data-url="<?= site_url('tally/request_handover/' . $workOrder['id']) ?>">
                                                            HANDOVER &nbsp; <i class="fa fa-share-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-success btn-complete-tally" style="margin-bottom: 5px" data-handheld-status="<?= $workOrder['status_unlock_handheld'] ?>" data-url="<?= site_url('tally/complete_job/' . $workOrder['id']) ?>">
                                                            COMPLETE &nbsp; <i class="fa fa-check"></i>
                                                        </button>
                                                        <a href="<?= site_url('tally/create/' . $workOrder['id']) ?>" style="margin-bottom: 5px" class="btn btn-danger btn-continue-tally-check">
                                                            CONTINUE &nbsp; <i class="fa fa-arrow-right"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <strong>Handover By <?= $getHandoverBy['name'] ?></strong><br>
                                                        at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <strong>Taken By <?= $workOrder['tally_name'] ?></strong><br>
                                                    at <?= format_date($workOrder['updated_at'], 'd F Y H:i') ?>
                                                <?php endif; ?> 
                                            <?php endif; ?>     
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php elseif($workOrder['status'] == WorkOrderModel::STATUS_COMPLETED): ?>
                                    <?php if($workOrder['completed_by'] == UserModel::authenticatedUserData('id') || AuthorizationModel::hasRole(ROLE_ADMINISTRATOR)): ?>
                                        <a class="btn btn-secondary" href="<?= site_url('work-order/view-upload/' . $workOrder['id']) ?>" style="margin-bottom: 5px ;background-color: #00c0ef;border-color: #00c0ef;color: #fff;">
                                            <i class="fa ion-upload"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($workOrder['multiplier_goods'] == '-1' && !empty($workOrder['goods']))://if(($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'STUFFING') && !empty($workOrder['goods'])): ?>
                                        <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_CHECKED): ?>
                                            <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                                                <a class="btn btn-secondary" href="<?= site_url('work-order/view-check/' . $workOrder['id']) ?>" style="margin-bottom: 5px ;background-color: #f39c12;border-color: #f39c12;color: #fff;">
                                                    PREVIEW &nbsp; <i class="fa fa-check"></i>
                                                </a>
                                                <button class="btn btn-primary btn-approved" data-url="<?= site_url('tally/approve-job') ?>" style="margin-bottom: 5px">
                                                    APPROVE &nbsp; <i class="fa fa-check"></i>
                                                </button>
                                            <?php else: ?>
                                                <strong>Checked By <?= $workOrder['checked_name'] ?></strong><br>
                                                at <?= format_date($workOrder['created_at_status'], 'd F Y H:i') ?>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <a class="btn btn-secondary" href="<?= site_url('work-order/view-check/' . $workOrder['id']) ?>" style="margin-bottom: 5px ;background-color: #f39c12;border-color: #f39c12;color: #fff;">
                                                CHECK &nbsp; <i class="fa fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a class="btn btn-secondary" href="<?= site_url('work-order/view-check/' . $workOrder['id']) ?>" style="margin-bottom: 5px ;background-color: #f39c12;border-color: #f39c12;color: #fff;">
                                            PREVIEW &nbsp; <i class="fa fa-check"></i>
                                        </a>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                                            <button class="btn btn-primary btn-approved" data-url="<?= site_url('tally/approve-job') ?>" style="margin-bottom: 5px">
                                                APPROVE &nbsp; <i class="fa fa-check"></i>
                                            </button>
                                        <?php endif; ?> 
                                    <?php endif; ?>            
                                <?php else: ?>
                                    <?php if($workOrder['payout_passed']): ?>
                                        <p class="mb0">Cash & Carry Payout Date Passed</p>
                                        <p class="text-sm text-muted mb0">Contact Finance to Proceed</p>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-take-tally-check" data-handheld-status="<?= $workOrder['status_unlock_handheld'] ?>" data-url="<?= site_url('tally/take-job/' . $workOrder['id']) ?>">
                                            TAKE &nbsp; <i class="fa fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <a href="#" class="list-group-item disabled">
                    No queue job tally check
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-tally-check">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" class="need-validation">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_customer" id="id_customer">
                <input type="hidden" name="category" id="category">
                <input type="hidden" name="handling_type" id="handling_type">
                <input type="hidden" name="id_upload" id="id_upload">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Tally Check</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to take job no
                        <strong id="tally-check-title"></strong>?</p>
                    <p class="text-danger">
                        Another user would never can take this job handling until you release it.
                    </p>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-3" id="photo-add">
                            <button class="btn btn-primary btn-sm" type="button" id="btn-add-photo">Add Photo</button>
                        </div>
                    </div>
                    <div id="photo-wrapper">
                        <div class="row" style="margin-bottom: 2px">
                            <div class="form-group">
                                <div class="col-sm-3">
                                        <label for="attachment_button_0">Attachment 1</label>
                                        <input type="file" id="attachment_0" name="attachments_0" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                                        <div class="input-group col-xs-12">
                                            <input type="text" name="candidates[0][attachment]" id="attachment_info_0" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
    background:#F5F5F5;webkit-touch-callout: none;" placeholder="Upload attachment" >
                                            <span class="input-group-btn">
                                                <button class="file-upload-browse btn btn-default btn-photo-picker button-file" id="attachment_button_0" type="button">Upload</button>
                                            </span>
                                        </div>
                                    <div class="upload-input-wrapper"></div>
                                </div>
                                <div class="col-sm-3">
                                    <div id="progress" class="progress progress-upload">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>
                                    <div class="uploaded-file"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="photo_name_0">Photo Name</label>
                                    <div class="input-group col-xs-12">
                                        <input type="text" name="photo_name[0]" id="photo_name_0" class="form-control" placeholder="Photo Name">
                                    </div>
                                </div>
                            </div>
                            </div>
                    </div>
                    <div id="transporter-wrapper">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transporter">Transporter</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="transporter" id="transporter" data-placeholder="Select transporter" style="width: 100%;">
                                            <option value=""></option>   
                                            <option value="internal">INTERNAL</option>   
                                            <option value="external">EXTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7" id="internal-expedition-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="plat" id="plat_label">Plat</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="plat" id="plat" data-placeholder="Select plat" style="width: 100%;">
                                            <option value=""></option>   
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="external-expedition-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="plat" id="plat_label">Plat</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="plat_external" id="plat_external" data-placeholder="Select plat" style="width: 100%;">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="chassis-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="chassis">Chassis</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2" name="chassis" id="chassis" data-placeholder="Select chassis" style="width: 100%;" required>
                                            <option value=""></option>
                                            <?php foreach ($outstandingChassis as $chassis): ?>
                                                <option value="<?= $chassis['id'] ?>">
                                                    <?= $chassis['no_chassis'] ?> (<?= $chassis['tep_code'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="expedition-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="expedition" id="expedition">Expedition</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2"  name="expedition" id="expedition" data-placeholder="Select expedition" style="width: 100%;">
                                            <option value=""></option>
                                            <?php foreach ($expeditions as $expedition): ?>
                                            <option value="<?= $expedition['name'] ?>">
                                                <?= $expedition['name'] ?> (<?= $expedition['no_person'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label for="armada">Armada</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="armada" id="armada" data-placeholder="Select armada" style="width: 100%;">
                                            <option value=""></option>   
                                            <?php 
                                                foreach ($armadas as $armada) :
                                            ?>
                                            <option value="<?= $armada['id'] ?>"><?= $armada['jenis_armada'] ?></option>   
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" id="armada-type-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="armada_type">Container Owner</label>
                                    <div class="input-group col-xs-12">
                                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="armada_type" id="armada_type" data-placeholder="Select Owner" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="internal">INTERNAL</option> 
                                            <option value="external">EXTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" id="armada-description-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="armada_description">Armada Description</label>
                                    <div class="input-group col-xs-12">
                                        <input class="form-control" type="text" name="armada_description" id="armada_description" placeholder="Armada Description" style="width: 100%;" maxlength="100">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="rute-pengiriman-wrapper" style="display: none">
                                <div class="form-group">
                                    <label for="rute_pengiriman">Shipping Route</label>
                                    <div class="input-group col-xs-12">
                                        <input class="form-control" type="text" name="rute_pengiriman" id="rute_pengiriman" placeholder="Recipient's Address" style="width: 100%;" maxlength="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Take Tally Check</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php $this->load->view('tally/_modal_confirm_check') ?>
<?php $this->load->view('tally/_modal_request_handover', ['users' => $handover_users]) ?>
<?php $this->load->view('tally/_modal_approved') ?>
<?php $this->load->view('tally/_modal_handover_approved') ?>
<?php $this->load->view('tally/_modal_take_handover') ?>
<?php $this->load->view('tally/_modal_take_photo') ?>
<?php $this->load->view('tally/_attachment_photo') ?>
<?php $this->load->view('tally/_attachment_default') ?>
<script src="<?= base_url('assets/app/js/tally.js?v=13') ?>" defer></script>
<script src="<?= base_url('assets/app/js/photo-scanner.js?v=2') ?>" defer></script>
<script id="photo-template" type="text/x-custom-template">
    <div class="row card-photo" style="margin-bottom: 2px">
        <div class="form-group">
            <div class="col-sm-3">
                <label for="attachment_button_{{index}}">Attachment {{no}}</label>
                <input type="file" id="attachment_{{index}}" name="attachments_{{index}}" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera"  >
                <div class="input-group col-xs-12">
                <input type="text" name="candidates[{{index}}][attachment]" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment" required>
                    <span class="input-group-btn">
                        <button class="file-upload-browse btn btn-default btn-photo-picker button-file" id="attachment_button_{{index}}" type="button">Upload</button>
                    </span>
                </div>
                <div class="upload-input-wrapper"></div>
            </div>
            <div class="col-sm-3">
                <div id="progress" class="progress progress-upload">
                    <div class="progress-bar progress-bar-success"></div>
                </div>
                <div class="uploaded-file"></div>
            </div>
            <div class="col-sm-4">
                <label for="photo_name_{{index}}">Photo Name</label>
                <div class="input-group col-xs-12">
                    <input type="text" name="photo_name[{{index}}]" id="photo_name_{{index}}" class="form-control" placeholder="Photo Name" required>
                </div>
            </div>
            <div class="col-sm-2">
                <label for="photo_name_{{index}}">Delete</label>
                <div class="input-group col-xs-12">
                    <button class="btn btn-sm btn-danger btn-remove-photo" type="button">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>
