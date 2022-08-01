<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Safe Conduct <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Safe Conduct</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-safe-conduct">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Safe Conduct</th>
                        <th>No Police</th>
                        <th>Driver</th>
                        <th>Expedition</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($safeConducts as $safeConduct): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?php $type = $safeConduct['type'] == SafeConductModel::TYPE_INBOUND ? 'in' : 'out' ?>
                                <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>">
                                    <?= $safeConduct['no_safe_conduct'] ?>
                                </a>
                            </td>
                            <td><?= $safeConduct['no_police'] ?></td>
                            <td><?= $safeConduct['driver'] ?></td>
                            <td><?= $safeConduct['expedition'] ?></td>
                            <td><?= empty($safeConduct['security_in_date']) ? '-' : (new DateTime($safeConduct['security_in_date']))->format('d M Y H:i') ?></td>
                            <td><?= empty($safeConduct['security_out_date']) ? '-' : (new DateTime($safeConduct['security_out_date']))->format('d M Y H:i') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($safeConducts)): ?>
                        <tr>
                            <td colspan="7">No any safe conducts available</td>
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
        <?php if($booking['status'] == BookingModel::STATUS_APPROVED): ?>
            <a href="<?= site_url('safe-conduct/create') ?>" class="btn btn-success pull-right">
                Create Safe Conduct
            </a>
        <?php endif; ?>
    </div>
</div>