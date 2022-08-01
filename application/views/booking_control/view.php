<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Booking Control</h3>
    </div>

    <div class="box-body">

        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['category'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['booking_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['no_reference'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($booking['booking_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <?php
                            $statuses = [
                                BookingModel::STATUS_BOOKED => 'default',
                                BookingModel::STATUS_REJECTED => 'danger',
                                BookingModel::STATUS_APPROVED => 'success',
                                BookingModel::STATUS_COMPLETED => 'success',
                                BookingControlStatusModel::STATUS_CANCELED => 'danger',
                                BookingControlStatusModel::STATUS_PENDING => 'default',
                                BookingControlStatusModel::STATUS_DRAFT => 'warning',
                                BookingControlStatusModel::STATUS_DONE => 'primary',
                                BookingControlStatusModel::STATUS_CLEAR => 'success',
                            ];
                            ?>
                            <p class="form-control-static">
                                <span class="label label-<?= get_if_exist($statuses, $booking['status'], 'default') ?>">
                                    <?= $booking['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status Control</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <span class="label label-<?= get_if_exist($statuses, $booking['status_control'], 'default') ?>">
                                    <?= $booking['status_control'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Control Status</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed no-datatable responsive">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Created By</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bookingControlStatuses as $index => $status): ?>
                            <tr>
                                <td><?= ($index + 1) ?></td>
                                <td>
                                    <span class="label label-<?= get_if_exist($statuses, $status['status_control'], 'default') ?>">
                                        <?= $status['status_control'] ?>
                                    </span>
                                </td>
                                <td><?= $status['description'] ?></td>
                                <td><?= readable_date($status['created_at']) ?></td>
                                <td><?= $status['creator_name'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($bookingControlStatuses)): ?>
                            <tr>
                                <td colspan="4">No status available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!empty($workOrderInbounds)): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Inbound</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed no-datatable responsive">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>No Booking</th>
                                <th>No Job</th>
                                <th>Job Taken</th>
                                <th>Job Completed</th>
                                <th>Tally</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($workOrderInbounds as $index => $workOrder): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('booking/view/' . $workOrder['id_booking']) ?>">
                                            <?= $workOrder['no_booking'] ?>
                                        </a><br>
                                        <small class="text-muted"><?= $workOrder['no_reference'] ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                            <?= $workOrder['no_work_order'] ?>
                                        </a>
                                    </td>
                                    <td><?= readable_date($workOrder['taken_at']) ?></td>
                                    <td><?= readable_date($workOrder['completed_at']) ?></td>
                                    <td><?= $workOrder['tally_name'] ?></td>
                                    <td><?= $workOrder['status'] ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <?php if(!empty($workOrder['containers'])): ?>
                                            <?php $this->load->view('booking_control/_data_container', [
                                                'containers' => $workOrder['containers']
                                            ]) ?>
                                        <?php endif; ?>

                                        <?php if(!empty($workOrder['goods'])): ?>
                                            <?php $this->load->view('booking_control/_data_goods', [
                                                'goods' => $workOrder['goods']
                                            ]) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($workOrderOutbounds)): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Outbound</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed no-datatable responsive">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>No Booking</th>
                                <th>No Job</th>
                                <th>Job Taken</th>
                                <th>Job Completed</th>
                                <th>Tally</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($workOrderOutbounds as $index => $workOrder): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('booking/view/' . $workOrder['id_booking']) ?>">
                                            <?= $workOrder['no_booking'] ?>
                                        </a><br>
                                        <small class="text-muted"><?= $workOrder['no_reference'] ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                            <?= $workOrder['no_work_order'] ?>
                                        </a>
                                    </td>
                                    <td><?= readable_date($workOrder['taken_at']) ?></td>
                                    <td><?= readable_date($workOrder['completed_at']) ?></td>
                                    <td><?= $workOrder['tally_name'] ?></td>
                                    <td><?= $workOrder['status'] ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <?php if(!empty($workOrder['containers'])): ?>
                                            <?php $this->load->view('booking_control/_data_container', [
                                                'containers' => $workOrder['containers']
                                            ]) ?>
                                        <?php endif; ?>

                                        <?php if(!empty($workOrder['goods'])): ?>
                                            <?php $this->load->view('booking_control/_data_goods', [
                                                'goods' => $workOrder['goods']
                                            ]) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($booking['category'] == 'INBOUND'): ?>
            <?php $this->load->view('booking_control/_data_comparator') ?>
        <?php endif; ?>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>