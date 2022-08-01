<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Control</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('booking_control/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-booking-control">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th class="type-category">Type</th>
                <th class="type-booking">No Booking</th>
                <th class="type-reference">No Ref In/Out</th>
                <th class="type-date">Date</th>
                <th class="type-status">Status</th>
                <th class="type-status-control">Control</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
    <!-- /.box-body -->
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_VALIDATE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-change-status-booking">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Change Status Booking</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 10px">Change status booking
                            <strong id="booking-title"></strong>?
                        </p>
                        <div class="form-group">
                            <label for="status_control" class="control-label">Status Control</label>
                            <select class="select2" name="status_control" id="status_control"
                                    data-placeholder="Select status" style="width: 100%" required>
                                <option value=""></option>
                                <option value="<?= BookingControlModel::STATUS_PENDING ?>">
                                    <?= BookingControlModel::STATUS_PENDING ?>
                                </option>
                                <option value="<?= BookingControlModel::STATUS_CANCELED ?>">
                                    <?= BookingControlModel::STATUS_CANCELED ?>
                                </option>
                                <option value="<?= BookingControlModel::STATUS_DRAFT ?>">
                                    <?= BookingControlModel::STATUS_DRAFT ?>
                                </option>
                                <option value="<?= BookingControlModel::STATUS_DONE ?>">
                                    <?= BookingControlModel::STATUS_DONE ?>
                                </option>
                                <option value="<?= BookingControlModel::STATUS_CLEAR ?>">
                                    <?= BookingControlModel::STATUS_CLEAR ?>
                                </option>
                            </select>
                            <span class="help-block">If you set INBOUND to clear then related outbounds will clear as well</span>
                        </div>
                        <div class="form-group">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3"
                                      required maxlength="300" placeholder="Status remark"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" data-toggle=one-touch>Change Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<script id="control-booking-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CONTROL_VIEW)): ?>
                <li>
                    <a href="<?= site_url('booking-control/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Detail
                    </a>
                </li>
            <?php endif ?>

            <?php
            $authorizeManage = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CONTROL_MANAGE);
            $authorizeRevert = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CONTROL_REVERT);
            ?>
            <?php if ($authorizeManage || $authorizeRevert): ?>
                <li class="action-validate"
                    data-authorize-manage="<?= $authorizeManage ?>"
                    data-authorize-revert="<?= $authorizeRevert ?>">
                    <a href="<?= site_url('booking-control/change-status/{{id}}') ?>"
                       class="btn-change-status"
                       data-id="{{id}}"
                       data-status-control="{{status_control}}"
                       data-label="{{no_booking}}"
                       data-category="{{category}}">
                        <i class="fa ion-checkmark"></i>Change Status
                    </a>
                </li>
            <?php endif ?>

        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/booking-control.js?v=2') ?>" defer></script>