<div class="box box-primary">
    <div class="box-header with-border row-workorder" data-no="<?= $workOrder['no_work_order'] ?>">
        <h3 class="box-title">View Job <?= $this->uri->segment('4') == 'history' ? 'History' : 'Result' ?></h3>
        <?php if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_ON_REVIEW): ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATE)): ?>
                <a href="<?= site_url('work-order/validate-work-order/' . $workOrder['id']) ?>"
                   class="btn btn-primary btn-validate pull-right" data-validate="validate job" data-label="<?= $workOrder['no_work_order'] ?>">
                    Validate Job
                </a>
            <?php endif ?>
        <?php endif ?>
    </div>
    <div class="box-body" id="tally-check-body">
    <?php $this->load->view('template/_alert') ?>
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Job</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['no_work_order'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Handling</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('handling/view/' . $workOrder['id_handling']) ?>">
                                    <?= $workOrder['no_handling'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Handling Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['handling_type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $workOrder['id_booking']) ?>">
                                    <?= $workOrder['no_booking'] ?>
                                </a> (<?= $workOrder['no_reference'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Queue</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['queue'] == 0 ? 'Auto Job (No queue)' : $workOrder['queue'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $dataLabel = [
                                    WorkOrderModel::STATUS_QUEUED => 'danger',
                                    WorkOrderModel::STATUS_TAKEN => 'warning',
                                    WorkOrderModel::STATUS_COMPLETED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_PENDING => 'default',
                                    WorkOrderModel::STATUS_VALIDATION_ON_REVIEW => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_VALIDATED => 'primary',
                                    WorkOrderModel::STATUS_VALIDATION_APPROVED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_CHECKED => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED => 'primary',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED => 'info',
                                ];
                                ?>
                                <span class="label label-<?= $dataLabel[$workOrder['status']] ?> mr10">
                                    <?php if(empty($workOrder['gate_in_date'])): ?>
                                        NEED GATE IN
                                    <?php else: ?>
                                        <?= $workOrder['status'] ?>
                                    <?php endif ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Attachment</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($workOrder['attachment'])): ?>
                                    No attachment
                                <?php else: ?>
                                    <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                        Download Attachment
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Safe Conduct</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(!empty($workOrder['no_safe_conduct'])): ?>
                                    <a href="<?= site_url('safe-conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                        <?= $workOrder['no_safe_conduct'] ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Transporter</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (!empty($workOrder['id_vehicle'])) { ?>
                                    <?= 'INTERNAL TCI' ?>
                                <?php } else if (!empty($workOrder['id_transporter_entry_permit'])) { ?>
                                    <?= 'EXTERNAL' ?>
                                <?php } else  { ?>
                                    <?= '-' ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Plate Number</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                            <?= values($workOrder['no_police'],values($workOrder['no_plate_take'].' (TAKE)','-')) ?>
                                <?php if (!empty($workOrder['vehicle_type'])) { ?>
                                    (<?= values($workOrder['vehicle_type'],'-') ?>)
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shipping Route</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['shipping_route'],'-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Armada</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['jenis_armada'],'-') ?>
                                <?php if (!empty($workOrder['armada_type'])) { ?>
                                    (<?= values($workOrder['armada_type'],'-') ?>)
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Armada Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['armada_description'],'-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Gate In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['gate_in_date']) ? '-' : readable_date($workOrder['gate_in_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Gate Out</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['gate_out_date']) ? '-' : readable_date($workOrder['gate_out_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ST Gate</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['service_time'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Taken At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['taken_at']) ? '-' : readable_date($workOrder['taken_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Completed At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['completed_at']) ? '-' : readable_date($workOrder['completed_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ST Tally</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['service_time_tally'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Taken By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['tally_name'], 'Not taken yet') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($workOrder['created_at']) ?>
                                <?= if_empty($workOrder['creator_name'], '', ' by (', ')') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($workOrder['updated_at']) ?>
                                <?= if_empty($workOrder['updater_name'], '', ' by (', ')') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php if(!empty($lockHistories)): ?>
        <div class="table-responsive">
            <?php $this->load->view('workorder/_history_lock') ?>
        </div>               
        <?php endif; ?>        
        <?php if(empty($containers) && empty($goods)): ?>
            <div class="panel panel-danger">
                <div class="panel-body">
                    <p class="lead mb0">Please complete tally check first.</p>
                </div>
            </div>
        <?php else: ?>
            <?php if(!empty($workOrderPhotos)): ?>
                <div class="table-responsive">
                <?php $this->load->view('workorder/_data_photos') ?>
                </div>
            <?php endif ?>
            <form id="form-stock-remain" action="<?= site_url('tally/checked-job/') ?>" method="post">
                <input type="hidden" name="id" id="id" value="<?= $workOrder['id'] ?>">
                <input type="hidden" name="no" id="no" value="<?= $workOrder['no_work_order'] ?>">
                <input type="hidden" name="handheld_status" id="handheld_status" value="<?= $workOrder['status_unlock_handheld'] ?>">
                <input type="hidden" name="id_customer" id="id_customer" value="<?= $workOrder['id_customer'] ?>">
                <!-- <input type="hidden" name="photo_name[]" id="photo_name" value=""> -->
                <input type="hidden" name="message" id="message" value="">
                <?php if($workOrder['multiplier_goods'] == '-1' && !empty($goods)): //if(($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'STUFFING') && !empty($goods)): ?>
                    <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_PENDING||$workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_CHECKED || $workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED): ?>
                        <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_PENDING || $workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED): ?>
                            <?php if (isset($check) && $workOrder['tally_name']!=UserModel::authenticatedUserData('name')||isset($check) && AuthorizationModel::hasRole(ROLE_ADMINISTRATOR)): ?>
                                <?php $this->load->view('workorder/_data_detail_check') ?>
                            <?php endif ?>
                        <?php else: ?>
                            <?php $this->load->view('workorder/_data_detail_check_spv') ?>
                        <?php endif ?>
                    <?php endif ?>
                <?php else: ?>
                    <?php $this->load->view('workorder/_data_detail') ?>
                <?php endif ?>
        <?php endif ?>

        <?php if(isset($workOrderStatuses)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Validation Status</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable responsive">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Status</th>
                            <th>Stock</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Created By</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($workOrderStatuses as $status): ?>
                            <tr>
                                <td class="responsive-hide"><?= $no++ ?></td>
                                <td class="responsive-title">
                                    <span class="label label-<?= $dataLabel[$status['status']] ?>">
                                        <?= $status['status'] ?>
                                    </span>
                                </td>
                                <td><?php foreach ($status['goods'] as $goods) {
                                    if ($status['status']=='CHECKED' && trim($goods['stock_remaining_tally'])!='') {
                                        echo $goods['goods_name']." : ".$goods['stock_remaining_tally']."</br>";
                                    }
                                    if ($status['status']=='APPROVED' && trim($goods['stock_remaining_spv'])!='') {
                                        echo $goods['goods_name']." : ".$goods['stock_remaining_spv']."</br>";
                                    }
                                } ?></td>
                                <td><?= values($status['description'], '-') ?></td>
                                <td><?= readable_date($status['created_at']) ?></td>
                                <td><?= $status['creator_name'] ?></td>
                            </tr>
                        <?php endforeach ?>
                        <?php if(empty($status)): ?>
                            <tr>
                                <td colspan="5">No statuses available</td>
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif ?>

        <?php if(isset($workOrderHistories)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Work Order Histories</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable responsive">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>No Work Order</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Updated At</th>
                            <th>Updated By</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($workOrderHistories as $workOrderHistory): ?>
                            <tr>
                                <td class="responsive-hide"><?= $no++ ?></td>
                                <td class="responsive-title">
                                    <a href="<?= site_url('work-order/history/' . $workOrderHistory['id']) ?>">
                                        <?= $workOrderHistory['no_work_order'] ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="label label-<?= $dataLabel[$workOrderHistory['status']] ?>">
                                        <?= $workOrderHistory['status'] ?>
                                    </span>
                                </td>
                                <td><?= if_empty($workOrderHistory['description'], '-') ?></td>
                                <td><?= format_date($workOrderHistory['created_at'], 'd M Y H:i') ?></td>
                                <td><?= $workOrderHistory['creator_name'] ?></td>
                            </tr>
                        <?php endforeach ?>
                        <?php if(empty($workOrderHistories)): ?>
                            <tr>
                                <td colspan="5">No history available</td>
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif ?>
    </div>
    </form>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PRINT)): ?>
            <?php if (!isset($check)): ?>
                <a href="<?= site_url('work-order/print-tally-sheet2/' . $workOrder['id']) ?>"
                class="btn btn-primary btn-print-tally-sheet pull-right">
                    Print Tally Sheet
                </a>
            <?php endif ?>
        <?php endif ?>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VIEW)): ?>
            <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_CHECKED): ?>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                    <button class="btn btn-primary btn-approved pull-right" data-url="<?= site_url('tally/approve-job') ?>" 
                    data-id="<?= $workOrder['id'] ?>" 
                    data-no="<?= $workOrder['no_work_order'] ?>" style="margin-bottom: 5px">
                        APPROVE &nbsp; <i class="fa fa-check"></i>
                    </button>
                <?php endif ?>
            <?php endif ?>
            <?php if(($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'STUFFING') && !empty($goods)): ?>

                <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_PENDING || $workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED): ?>
                    <?php if (isset($check) && $workOrder['tally_name']!=UserModel::authenticatedUserData('name')||isset($check) && AuthorizationModel::hasRole(ROLE_ADMINISTRATOR)): ?>
                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_TAKE_JOB)): ?>
                        <a data-url="<?= site_url('tally/checked-job/') ?>" data-id="<?= $workOrder['id'] ?>" data-no="<?= $workOrder['no_work_order'] ?>"
                        data-id-handling-type="<?= $workOrder['id_handling_type'] ?>"
                            class="btn btn-primary btn-checked pull-right">
                            Checked
                        </a>
                        <?php endif ?>
                    <?php endif ?>
                <?php endif ?>
            <?php else: ?>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)): ?>
                    <button class="btn btn-primary btn-approved pull-right" data-url="<?= site_url('tally/approve-job') ?>" 
                    data-id="<?= $workOrder['id'] ?>" 
                    data-no="<?= $workOrder['no_work_order'] ?>" style="margin-bottom: 5px">
                        APPROVE &nbsp; <i class="fa fa-check"></i>
                    </button>
                <?php endif ?>
            <?php endif ?>        
        <?php endif ?>
    </div>
</div>
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
                    <input type="text" name="photo_name[]" id="photo_name_{{index}}" class="form-control" placeholder="Photo Name" required>
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif ?>
<?php $this->load->view('workorder/_modal_checked') ?>
<?php $this->load->view('workorder/_modal_approved') ?>
<?php $this->load->view('tally/_modal_take_photo') ?>
<?php $this->load->view('tally/_attachment_photo') ?>
<?php $this->load->view('tally/_attachment_default') ?>
<script src="<?= base_url('assets/app/js/work-order.js?v=4') ?>" defer></script>
<script src="<?= base_url('assets/app/js/tally.js?v=14') ?>" defer></script>
<script src="<?= base_url('assets/app/js/photo-scanner.js?v=3') ?>" defer></script>