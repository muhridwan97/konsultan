<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Handling <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Handling</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-handling">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Handling</th>
                        <th>Handling Type</th>
                        <th>No Booking</th>
                        <th>Customer</th>
                        <th>Handling Date</th>
                        <th>Validated By</th>
                        <th>Status</th>
                        <th>Work Order</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($handlings as $handling): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('handling/view/' . $handling['id']) ?>">
                                    <?= $handling['no_handling'] ?>
                                </a>
                            </td>
                            <td><?= $handling['handling_type'] ?></td>
                            <td><?= $handling['no_booking'] ?></td>
                            <td><?= $handling['customer_name'] ?></td>
                            <td><?= format_date($handling['handling_date'], 'd F Y H:i') ?></td>
                            <td><?= if_empty($handling['validator_name'], 'Not yet validated') ?></td>
                            <td>
                                <?php
                                $dataLabel = [
                                    HandlingModel::STATUS_PENDING => 'default',
                                    HandlingModel::STATUS_APPROVED => 'success',
                                    HandlingModel::STATUS_REJECTED => 'danger',
                                ];
                                ?>
                                <span class="label label-<?= $dataLabel[$handling['status']] ?>">
                                    <?= $handling['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if (empty($handling['no_work_order'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('work-order/view/' . $handling['id_work_order']) ?>">
                                        <?= $handling['no_work_order'] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($handlings)): ?>
                        <tr>
                            <td colspan="9">No any handlings available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back
        </a>
        <?php if($booking['category'] == 'INBOUND' && $booking['status'] == BookingModel::STATUS_APPROVED): ?>
            <a href="<?= site_url('handling/create?customer_id='.$booking['id_customer'].'&booking_id=' . $booking['id']) ?>" class="btn btn-success pull-right">
                Create Handling
            </a>
        <?php endif; ?>
    </div>
</div>