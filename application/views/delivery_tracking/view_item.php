<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View <?= $deliveryTracking['no_delivery_tracking'] ?></h3>
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
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryTracking['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
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
                    <a href="<?= site_url('delivery-tracking/view/' . $deliveryTracking['id']) ?>" class="btn btn-primary mr10">
                        Show By State
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px" class="text-center">No</th>
                        <th>Goods Name</th>
                        <th>No Safe Conduct</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($safeConductGoods as $safeConductItem): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $safeConductItem['goods_name'] ?></td>
                            <td><?= $safeConductItem['no_safe_conduct'] ?></td>
                        </tr>
                        <?php if (!empty($safeConductItem['states'])): ?>
                            <tr>
                                <td></td>
                                <td colspan="2">
                                    <table class="table table-sm no-datatable">
                                        <thead>
                                        <tr>
                                            <th style="width: 50px">No</th>
                                            <th>Tracking Message</th>
                                            <th>Arrival</th>
                                            <th>Unload</th>
                                            <th>Location</th>
                                            <th>Attachment</th>
                                            <th>Quantity</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $innerIndex = 1 ?>
                                        <?php foreach ($safeConductItem['states'] as $deliveryTrackingDetail): ?>
                                            <?php foreach ($deliveryTrackingDetail['goods'] as $goods): ?>
                                                <?php if($goods['id_safe_conduct_goods'] == $safeConductItem['id']): ?>
                                                    <tr>
                                                        <td><?= $innerIndex++ ?></td>
                                                        <td><?= nl2br($deliveryTrackingDetail['tracking_message']) ?></td>
                                                        <td><?= if_empty(format_date($deliveryTrackingDetail['arrival_date'], 'd M Y H:i'), '-') ?></td>
                                                        <td><?= if_empty(format_date($deliveryTrackingDetail['unload_date'], 'd M Y H:i'), '-') ?></td>
                                                        <td><?= if_empty($deliveryTrackingDetail['unload_location'], '-') ?></td>
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
                                                        <td><?= numerical($goods['quantity'], 2, true) ?></td>
                                                        <td><?= if_empty($goods['description'], '-') ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($safeConductGoods)): ?>
                        <tr>
                            <td colspan="3">No delivery tracking item</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>