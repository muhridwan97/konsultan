<div class="mb20">
    <?php $this->load->view('client_area/_form_container') ?>
</div>

<h4 class="mb20">Tracking Result</h4>
<?php if(!empty(get_url_param('no_container'))): ?>
    <?php if(!empty($container)): ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">CONTAINER INFO</h3>
            </div>
            <div class="box-body">
                <form class="form-horizontal form-view mb20">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Shipping Line</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $container['shipping_line'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Container Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $container['no_container'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Type</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $container['type'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Size</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $container['size'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Description</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= if_empty($container['description'], 'No description') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Last Update</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= readable_date($container['updated_at']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($bookings as $booking): ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)
                </h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-hover no-datatable mb20">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>No Reference</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>BOOKING IN</td>
                        <td><?= readable_date($booking['booking_date']) ?></td>
                        <td><?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)</td>
                        <td><?= if_empty($booking['description'], '-') ?></td>
                    </tr>
                    <?php $no = 2 ?>
                    <?php foreach ($booking['jobs'] as $job): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $job['handling_type'] ?></td>
                            <td><?= readable_date($job['completed_at']) ?></td>
                            <td><?= $job['no_work_order'] ?></td>
                            <td><?= if_empty($job['description'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($booking['outbounds'] as $outbound): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>BOOKING OUT</td>
                            <td><?= readable_date($outbound['booking_date']) ?></td>
                            <td><?= $outbound['no_booking'] ?> (<?= $outbound['no_reference'] ?>)</td>
                            <td><?= if_empty($outbound['description'], '-') ?></td>
                        </tr>
                        <?php foreach ($outbound['jobs'] as $job): ?>
                            <tr class="ml20">
                                <td><?= $no++ ?></td>
                                <td><?= $job['handling_type'] ?></td>
                                <td><?= readable_date($job['completed_at']) ?></td>
                                <td><?= $job['no_work_order'] ?></td>
                                <td><?= if_empty($job['description'], '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if(empty($bookings)): ?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>Container or activity transaction data not found.</p>
        </div>
    <?php endif; ?>
<?php else: ?>
    Search container activity.
<?php endif; ?>
