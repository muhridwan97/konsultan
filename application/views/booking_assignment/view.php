<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View assignment <?= $bookingAssignment['no_booking'] ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $bookingAssignment['id']) ?>">
                                    <?= $bookingAssignment['no_booking'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Doc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingAssignment['no_reference'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Assigned To</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $bookingAssignment['name'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($bookingAssignment['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Assigned By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $bookingAssignment['assigner_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Assigned At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($bookingAssignment['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>