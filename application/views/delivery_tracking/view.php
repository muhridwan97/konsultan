<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View <?= $deliveryTracking['no_delivery_tracking'] ?></h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_EDIT) && $deliveryTracking['status'] == DeliveryTrackingModel::STATUS_ACTIVE): ?>
            <a href="<?= site_url('delivery-tracking/edit/' . $deliveryTracking['id']) ?>" class="btn btn-primary pull-right">
                Edit Delivery
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Tracking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $deliveryTracking['no_delivery_tracking'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryTracking['customer_name'], 'No customer') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Reminder Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryTracking['reminder_type'], 'No reminder') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Remind To</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $deliveryTracking['reminder_type'] == 'EMPLOYEE' ? $deliveryTracking['contact_mobile'] : $deliveryTracking['contact_group'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryTracking['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    DeliveryTrackingModel::STATUS_ACTIVE => 'default',
                                    DeliveryTrackingModel::STATUS_DELIVERED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $deliveryTracking['status'], 'default') ?>">
                                    <?= $deliveryTracking['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($deliveryTracking['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($deliveryTracking['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Delivery Details</h3>
                <div class="pull-right">
                    <a href="<?= site_url('delivery-tracking/view-item/' . $deliveryTracking['id']) ?>" class="btn btn-primary">
                        Show By Item
                    </a>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_ADD_STATE) && $deliveryTracking['status'] == DeliveryTrackingModel::STATUS_ACTIVE): ?>
                        <a href="<?= site_url('delivery-tracking/add-delivery-state/' . $deliveryTracking['id']) ?>" class="btn btn-success">
                            Add Delivery State
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Tracking Message</th>
                        <th>Arrival</th>
                        <th>Unload</th>
                        <th>Location</th>
                        <th>Attachment</th>
                        <th>Is Sent</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($deliveryTrackingDetails as $deliveryTrackingDetail): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= nl2br($deliveryTrackingDetail['tracking_message']) ?></td>
                            <td>
                                <?= if_empty(format_date($deliveryTrackingDetail['arrival_date'], 'd M Y H:i'), '-') ?>
                                <?= if_empty($deliveryTrackingDetail['arrival_date_type'], '', '<br><small class="text-muted">', '</small>') ?>
                            </td>
                            <td>
                                <?= if_empty(format_date($deliveryTrackingDetail['unload_date'], 'd M Y H:i'), '-') ?>
                                <?= if_empty($deliveryTrackingDetail['unload_date_type'], '', '<br><small class="text-muted">', '</small>') ?>
                            </td>
                            <td>
                                <a href="<?= site_url("delivery-tracking/print-delivery-tracking-state/{$deliveryTracking['id']}/{$deliveryTrackingDetail['id']}") ?>">
                                    <?= if_empty($deliveryTrackingDetail['unload_location'], 'No Location') ?>
                                </a>
                            </td>
                            <td>
                                <?php if (empty($deliveryTrackingDetail['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/' . $deliveryTrackingDetail['attachment']) ?>"
                                       target="_blank">
                                        Download
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label label-<?= $deliveryTrackingDetail['is_sent'] ? 'success' : 'default' ?>">
                                    <?= $deliveryTrackingDetail['is_sent'] ? 'YES' : 'WAITING' ?>
                                </span>
                            </td>
                            <td><?= format_date($deliveryTrackingDetail['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $deliveryTrackingDetail['creator_name'] ?></td>
                        </tr>
                        <?php if (!empty($deliveryTrackingDetail['goods'])): ?>
                            <tr>
                                <td></td>
                                <td colspan="10">
                                    <table class="table table-sm no-datatable">
                                        <thead>
                                        <tr>
                                            <th style="width: 50px">No</th>
                                            <th>No Safe Conduct</th>
                                            <th>No Plate</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($deliveryTrackingDetail['goods'] as $innerIndex => $goods): ?>
                                            <tr>
                                                <td><?= $innerIndex + 1 ?></td>
                                                <td><?= $goods['no_safe_conduct'] ?></td>
                                                <td><?= $goods['no_police'] ?></td>
                                                <td><?= $goods['goods_name'] ?></td>
                                                <td><?= numerical($goods['quantity'], 2, true) ?></td>
                                                <td><?= if_empty($goods['description'], '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($deliveryTrackingDetails)): ?>
                        <tr>
                            <td colspan="8">No delivery tracking state</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Assignment Messages</h3>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE) && $deliveryTracking['status'] == DeliveryTrackingModel::STATUS_ACTIVE): ?>
                    <a href="<?= site_url('delivery-tracking/add-assignment-message/' . $deliveryTracking['id']) ?>" class="btn btn-success pull-right">
                        Add Assignment Message
                    </a>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Assignment Message</th>
                        <th>Attachment</th>
                        <th>Is Sent</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($deliveryTrackingAssignments as $deliveryTrackingAssignment): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= nl2br($deliveryTrackingAssignment['assignment_message']) ?></td>
                            <td>
                                <?php if (empty($deliveryTrackingAssignment['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/' . $deliveryTrackingAssignment['attachment']) ?>"
                                       target="_blank">
                                        Download
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label label-<?= $deliveryTrackingAssignment['is_sent'] ? 'success' : 'default' ?>">
                                    <?= $deliveryTrackingAssignment['is_sent'] ? 'YES' : 'WAITING' ?>
                                </span>
                            </td>
                            <td><?= format_date($deliveryTrackingAssignment['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $deliveryTrackingAssignment['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($deliveryTrackingAssignments)): ?>
                        <tr>
                            <td colspan="7">No assignment message</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status History</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($statusHistories as $status): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $status['status'], 'default') ?>">
                                    <?= $status['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td><?= format_date($status['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('delivery-tracking/print-delivery-tracking/' . $deliveryTracking['id']) ?>" class="btn btn-primary pull-right">
            Print
        </a>
    </div>
</div>