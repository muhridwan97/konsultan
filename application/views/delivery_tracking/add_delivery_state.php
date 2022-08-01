<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Add Delivery State</h3>
    </div>

    <form action="<?= site_url('delivery-tracking/save-delivery-state/' . $deliveryTracking['id']) ?>" role="form" method="post" enctype="multipart/form-data" id="form-delivery-state">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-horizontal form-view">
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
            </div>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title">Add Tracking Location</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="arrival_date_type">Arrival Type</label>
                                <select class="form-control select2" id="arrival_date_type" name="arrival_date_type" data-placeholder="Select type" required>
                                    <option value=""></option>
                                    <option value="ESTIMATION"<?= set_select('arrival_date_type', 'ESTIMATION') ?>>ESTIMATION</option>
                                    <option value="ACTUAL"<?= set_select('arrival_date_type', 'ACTUAL') ?>>ACTUAL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="arrival_date">Arrival Date</label>
                                <input type="text" class="form-control daterangepicker2" id="arrival_date" name="arrival_date"
                                       placeholder="Arrival of item" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unload_date_type">Unload Type</label>
                                <select class="form-control select2" id="unload_date_type" name="unload_date_type" data-placeholder="Select type" required>
                                    <option value=""></option>
                                    <option value="ESTIMATION"<?= set_select('unload_date_type', 'ESTIMATION') ?>>ESTIMATION</option>
                                    <option value="ACTUAL"<?= set_select('unload_date_type', 'ACTUAL') ?>>ACTUAL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unload_date">Unload Date</label>
                                <input type="text" class="form-control daterangepicker2" id="unload_date" name="unload_date"
                                       placeholder="Unload of item" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unload_location">Unload Location</label>
                        <input type="text" class="form-control" id="unload_location" name="unload_location"
                               placeholder="Unload location of item" autocomplete="off" required>
                    </div>
                </div>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title">Safe Conduct Goods</h3>
                </div>
                <div class="box-body">
                    <table class="table table-sm responsive no-datatable" id="table-item-list">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Safe Conduct</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th style="width: 70px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1 ?>
                        <?php foreach ($deliveryTrackingSafeConducts as $deliveryTrackingSafeConduct): ?>
                            <?php foreach ($deliveryTrackingSafeConduct['goods'] as $goods): ?>
                                <tr data-safe-conduct-goods-id="<?= $goods['id'] ?>"
                                    data-no-safe-conduct="<?= $deliveryTrackingSafeConduct['no_safe_conduct'] ?>"
                                    data-goods-name="<?= $goods['goods_name'] ?>"
                                    data-quantity="<?= $goods['quantity'] ?>">
                                    <td class="text-center column-no"><?= $no++ ?></td>
                                    <td><?= $deliveryTrackingSafeConduct['no_safe_conduct'] ?></td>
                                    <td><?= $goods['goods_name'] ?></td>
                                    <td class="label-quantity"><?= numerical($goods['quantity'], 2, true) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary btn-take-item">TAKE</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php if (empty($deliveryTrackingSafeConducts)): ?>
                            <tr class="row-placeholder">
                                <td colspan="8">
                                    <div class="alert alert-danger">
                                        No safe conduct item available, please set
                                        <a href="<?= site_url('delivery-tracking/add-safe-conduct/' . $deliveryTracking['id']) ?>">
                                            safe conduct here
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title">Updated Goods Info</h3>
                </div>
                <div class="box-body">
                    <table class="table table-sm responsive no-datatable" id="table-item-taken">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Safe Conduct</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Description</th>
                            <th style="width: 70px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="row-placeholder">
                            <td colspan="8">No item available, please pick from list</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title">Add Tracking Info</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('status_date') == '' ?: 'has-error'; ?>">
                        <label for="status_date">Status Date</label>
                        <input type="text" class="form-control daterangepicker2" id="status_date" name="status_date"
                               placeholder="Status update date" required maxlength="30"
                               value="<?= set_value('status_date', date('d F Y H:i')) ?>">
                        <?= form_error('status_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('tracking_message') == '' ?: 'has-error'; ?>">
                        <label for="tracking_message">Tracking Message</label>
                        <textarea class="form-control" id="tracking_message" name="tracking_message" placeholder="Tracking message"
                                  maxlength="500" required rows="4"><?= set_value('tracking_message') ?></textarea>
                        <span class="help-block">This message will be sent over Whatsapp on specific schedule.</span>
                        <?= form_error('tracking_message', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment / Photo</label>
                        <input type="file" name="attachment" id="attachment" accept="image/*" placeholder="Photo">
                        <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('tracking_description') == '' ?: 'has-error'; ?>">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Additional info"
                                  maxlength="500"><?= set_value('description') ?></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <?php if (!empty($deliveryTrackingSafeConducts)): ?>
                <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                    Add Delivery State
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php $this->load->view('delivery_tracking/_modal_add_goods') ?>
<script src="<?= base_url('assets/app/js/delivery-state.js') ?>" defer></script>