<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Readdress</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Booking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $readdress['id_booking']) ?>">
                                    <?= $readdress['no_booking'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $readdress['no_reference'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer From</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($readdress['customer_from'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer To</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($readdress['customer_to'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    'PENDING' => 'default',
                                    'APPROVED' => 'success',
                                    'REJECTED' => 'danger',
                                    'EXPIRED' => 'warning',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$readdress['status']] ?>">
                                    <?= $readdress['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($readdress['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= readable_date($readdress['validated_at']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($readdress['validator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($readdress['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>
<!-- /.box -->