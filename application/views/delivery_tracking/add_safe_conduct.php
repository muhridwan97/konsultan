<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Safe Conduct Delivery</h3>
    </div>

    <form action="<?= site_url('delivery-tracking/save-safe-conduct/' . $deliveryTracking['id']) ?>" role="form" method="post" id="form-delivery-tracking">
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

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Add Tracking Safe Conduct</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('safe_conduct') == '' ?: 'has-error'; ?>">
                        <label for="safe_conduct">Safe Conduct Outbound</label>
                        <div class="input-group">
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('safe-conduct/ajax_get_by_keyword?type=OUTBOUND&customer=' . $deliveryTracking['id_customer']) ?>"
                                    data-key-id="id" data-key-label="no_safe_conduct" data-key-sublabel="no_reference"
                                    name="safe_conduct" id="safe_conduct" data-placeholder="Select safe conduct">
                                <option value=""></option>
                                <?php if(!empty($safeConduct)): ?>
                                    <option value="<?= $safeConduct['id'] ?>" selected>
                                        <?= $safeConduct['no_safe_conduct'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" id="btn-add-safe-conduct" type="button">
                                    ADD SAFE CONDUCT
                                </button>
                            </span>
                        </div>
                        <?= form_error('safe_conduct', '<span class="help-block">', '</span>'); ?>
                    </div>

                    <table class="table table-sm responsive no-datatable" id="table-safe-conduct-list">
                        <thead>
                        <tr>
                            <th style="width: 50px" class="text-center">No</th>
                            <th>Safe Conduct</th>
                            <th style="width: 100px" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="row-placeholder">
                            <td colspan="3">No safe conduct available, please pick from list</td>
                        </tr>
                        <?php foreach ($deliveryTrackingSafeConducts as $index => $deliveryTrackingSafeConduct): ?>
                            <tr>
                                <td class="text-center column-no"><?= $index + 1 ?></td>
                                <td><?= $deliveryTrackingSafeConduct['no_safe_conduct'] ?> - <?= $deliveryTrackingSafeConduct['no_reference'] ?></td>
                                <td class="text-center">
                                    <input type="hidden" name="safe_conducts[]" id="safe_conduct_<?= $deliveryTrackingSafeConduct['id_safe_conduct'] ?>" value="<?= $deliveryTrackingSafeConduct['id_safe_conduct'] ?>">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-safe-conduct">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
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
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Set Safe Conduct
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/delivery-tracking.js') ?>" defer></script>