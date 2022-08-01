<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Add Assignment Message</h3>
    </div>

    <form action="<?= site_url('delivery-tracking/save-assignment-message/' . $deliveryTracking['id']) ?>" role="form" method="post" enctype="multipart/form-data" id="form-delivery-tracking">
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
                    <h3 class="box-title">Add Assignment Message</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('assignment_message') == '' ?: 'has-error'; ?>">
                        <label for="assignment_message">Assignment Message</label>
                        <textarea class="form-control" id="assignment_message" name="assignment_message" placeholder="Assignment message"
                                  maxlength="500" rows="4"><?= set_value('assignment_message') ?></textarea>
                        <span class="help-block">This message will be sent over Whatsapp on specific schedule <strong>to assignment user</strong>.</span>
                        <?= form_error('assignment_message', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" id="attachment" placeholder="Attachment">
                        <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Add Delivery State
            </button>
        </div>
    </form>
</div>