
<div class="box box-primary">
    <div class="box-header with-border row-workorder" data-no="<?= $workOrder['no_work_order'] ?>">
        <h3 class="box-title">View Job <?= $this->uri->segment('4') == 'history' ? 'History' : 'Result' ?></h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)) : ?>
            <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_REJECT) : ?>
                <a href="<?= site_url('work-order/fix-work-order/' . $workOrder['id']) ?>" class="btn btn-primary btn-validate pull-right" data-validate="fix job" data-label="<?= $workOrder['no_work_order'] ?>">
                    Fix Job
                </a>
            <?php endif ?>
        <?php endif ?>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATE)) : ?>
            <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_ON_REVIEW) : ?>
                <a href="<?= site_url('work-order/validate-work-order/' . $workOrder['id']) ?>" class="btn btn-primary btn-validate pull-right" data-validate="validate job" data-label="<?= $workOrder['no_work_order'] ?>">
                    Validate Job
                </a>
            <?php endif ?>
            <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_FIXED) : ?>
                <div class="pull-right">
                    <a href="<?= site_url('work-order/reject-work-order/' . $workOrder['id']) ?>" class="btn btn-warning btn-validate" data-validate="reject job" data-label="<?= $workOrder['no_work_order'] ?>">
                        Reject Job
                    </a>
                    <a href="<?= site_url('work-order/validate-work-order/' . $workOrder['id']) ?>" class="btn btn-primary btn-validate" data-validate="validate job" data-label="<?= $workOrder['no_work_order'] ?>">
                        Validate Job
                    </a>
                </div>
            <?php endif ?>
            <?php if ($workOrder['multiplier_goods'] !== '0') : ?>
                <?php if (!in_array($workOrder['status_validation'], ['PENDING', 'CHECKED', 'REJECTED', 'FIXED', 'VALIDATED'])) : ?>
                    <div class="pull-right">
                        <a href="<?= site_url('work-order/reject-work-order/' . $workOrder['id']) ?>" class="btn btn-warning btn-validate" data-validate="reject job" data-label="<?= $workOrder['no_work_order'] ?>">
                            Reject Job
                        </a>
                        <a href="<?= site_url('work-order/validate-work-order/' . $workOrder['id']) ?>" class="btn btn-primary btn-validate" data-validate="validate job" data-label="<?= $workOrder['no_work_order'] ?>">
                            Validate Job
                        </a>
                    </div>
                <?php endif ?>
            <?php else : ?>
                <?php if (!in_array($workOrder['status_validation'], ['REJECTED', 'FIXED', 'VALIDATED'])) : ?>
                    <div class="pull-right">
                        <a href="<?= site_url('work-order/reject-work-order/' . $workOrder['id']) ?>" class="btn btn-warning btn-validate" data-validate="reject job" data-label="<?= $workOrder['no_work_order'] ?>">
                            Reject Job
                        </a>
                        <a href="<?= site_url('work-order/validate-work-order/' . $workOrder['id']) ?>" class="btn btn-primary btn-validate" data-validate="validate job" data-label="<?= $workOrder['no_work_order'] ?>">
                            Validate Job
                        </a>
                    </div>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    </div>
    
    <div class="box-body">
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
                                    WorkOrderModel::STATUS_VALIDATION_FIXED => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_REJECT => 'danger',
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
                                    <?php if (empty($workOrder['gate_in_date'])) : ?>
                                        NEED GATE IN
                                    <?php else : ?>
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
                                <?php if (empty($workOrder['attachment'])) : ?>
                                    No attachment
                                <?php else : ?>
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
                                <?php if (!empty($workOrder['no_safe_conduct'])) : ?>
                                    <a href="<?= site_url('safe-conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                        <?= $workOrder['no_safe_conduct'] ?>
                                    </a>
                                <?php else : ?>
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
                                <?php } else if (!empty($workOrder['id_transporter_entry_permit']) || !empty($chassis)) { ?>
                                    <?= 'EXTERNAL' ?>
                                <?php } else { ?>
                                    <?= '-' ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Plate Number</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['no_police'],values(if_empty($workOrder['no_plate_take'], '', '', ' (TEP)'),'-')) ?>
                                <?php if (!empty($workOrder['vehicle_type'])) { ?>
                                    (<?= values($workOrder['vehicle_type'], '-') ?>)
                                <?php } ?>

                                <?php if (!empty($tep)): ?>
                                    <?= $tep['tep_code'] ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Chassis</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (!empty($chassis)): ?>
                                    <?= $chassis['no_chassis'] ?> (Delivered By <?= $chassis['tep_code'] ?>)
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shipping Route</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['shipping_route'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Armada</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['jenis_armada'], '-') ?>
                                <?php if (!empty($workOrder['armada_type'])) { ?>
                                    (<?= values($workOrder['armada_type'], '-') ?>)
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Armada Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['armada_description'], '-') ?>
                            </p>
                        </div>
                    </div>
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
                        <label class="col-sm-3">Completed By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['completed_name'], '-') ?>
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
                    <div class="form-group">
                        <label class="col-sm-3">Space</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(number_format($workOrder['space'], 2), '-') ?> m <sup>2</sup>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php if (!empty($lockHistories)) : ?>
            <div class="table-responsive">
                <?php $this->load->view('workorder/_history_lock') ?>
            </div>
        <?php endif; ?>
        <?php if ($workOrder['status'] != 'COMPLETED') : ?>
            <div class="panel panel-danger">
                <div class="panel-body">
                    <p class="lead mb0">Please complete tally check first.</p>
                </div>
            </div>
        <?php else : ?>
            <?php if (!empty($workOrderPhotos)) : ?>
                <div class="table-responsive">
                    <?php $this->load->view('workorder/_data_photos') ?>
                </div>
            <?php endif ?>
            <?php $this->load->view('workorder/_data_detail') ?>
        <?php endif ?>

        <?php $this->load->view('tally/_tally_component_view') ?>

        <?php if (isset($workOrderStatuses)) : ?>
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
                            <?php foreach ($workOrderStatuses as $status) : ?>
                                <tr>
                                    <td class="responsive-hide"><?= $no++ ?></td>
                                    <td class="responsive-title">
                                        <span class="label label-<?= $dataLabel[$status['status']] ?>">
                                            <?= $status['status'] ?>
                                        </span>
                                    </td>
                                    <td><?php foreach ($status['goods'] as $goods) {
                                            if ($status['status'] == 'CHECKED' && trim($goods['stock_remaining_tally']) != '') {
                                                echo $goods['goods_name'] . " : " . $goods['stock_remaining_tally'] . "</br>";
                                            }
                                            if ($status['status'] == 'APPROVED' && trim($goods['stock_remaining_spv']) != '') {
                                                echo $goods['goods_name'] . " : " . $goods['stock_remaining_spv'] . "</br>";
                                            }
                                        } ?></td>
                                    <td><?= values($status['description'], '-') ?></td>
                                    <td><?= readable_date($status['created_at']) ?></td>
                                    <td><?= $status['creator_name'] ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (empty($status)) : ?>
                                <tr>
                                    <td colspan="5">No statuses available</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif ?>

        <?php if (isset($workOrderHistories)) : ?>
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
                            <?php foreach ($workOrderHistories as $workOrderHistory) : ?>
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
                            <?php if (empty($workOrderHistories)) : ?>
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
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PRINT)) : ?>
            <a href="<?= site_url('work-order/print-tally-sheet2/' . $workOrder['id']) ?>" class="btn btn-primary btn-print-tally-sheet pull-right">
                Print Tally Sheet
            </a>
        <?php endif ?>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_WORKORDER_VALIDATE, PERMISSION_WORKORDER_APPROVED])) : ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif ?>

<script src="<?= base_url('assets/app/js/work-order.js?v=3') ?>" defer></script>
