<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($bookings)): ?>
            <table class="table no-datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Type</th>
                    <th>No Booking</th>
                    <th>No Reference</th>
                    <th>Booking Date</th>
                    <th>Status Doc</th>
                    <th style="width: 50px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $booking['booking_type'] ?></td>
                        <td>
                            <a href="<?= site_url('booking/view/' . $booking['id']) ?>" target="_blank">
                                <?= $booking['no_booking'] ?>
                            </a>
                        </td>
                        <td><?= $booking['no_reference'] ?></td>
                        <td><?= readable_date($booking['booking_date']) ?></td>
                        <td><?= if_empty($booking['document_status'], '-') ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#detail-booking-<?= $booking['id'] ?>">
                                Reveal
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="detail-booking-<?= $booking['id'] ?>">
                        <?php if(empty($booking['details']['containers']) && empty($booking['details']['goods'])): ?>
                            <td colspan="7" class="text-center">No any data available</td>
                        <?php else: ?>
                            <td></td>
                            <td colspan="6" style="max-width: 767px">
                                <?php $this->load->view('booking/_view_detail', [
                                    'bookingContainers' => $booking['details']['containers'],
                                    'bookingGoods' => $booking['details']['goods'],
                                ]) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            No booking available
        <?php endif; ?>
    </div>
</div>


<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Safe Conduct</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($safeConducts)): ?>
            <table class="table no-datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Type</th>
                    <th>No Booking</th>
                    <th>No Safe Conduct</th>
                    <th>Vehicle Type</th>
                    <th>Driver</th>
                    <th>Police Number</th>
                    <th>Start (Check In)</th>
                    <th>End (Check Out)</th>
                    <th>Created At</th>
                    <th style="width: 50px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($safeConducts as $safeConduct): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $safeConduct['type'] ?></td>
                        <td>
                            <a href="<?= site_url('booking/view/' . $safeConduct['id_booking']) ?>" target="_blank">
                                <?= $safeConduct['no_booking'] ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= site_url('safe_conduct/view/' . $safeConduct['id']) ?>" target="_blank">
                                <?= $safeConduct['no_safe_conduct'] ?>
                            </a>
                        </td>
                        <td><?= $safeConduct['vehicle_type'] ?></td>
                        <td><?= $safeConduct['driver'] ?></td>
                        <td><?= $safeConduct['no_police'] ?></td>
                        <td><?= readable_date($safeConduct['security_in_date']) ?></td>
                        <td><?= readable_date($safeConduct['security_out_date']) ?></td>
                        <td><?= readable_date($safeConduct['created_at']) ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#detail-safe-conduct-<?= $safeConduct['id'] ?>">
                                Reveal
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="detail-safe-conduct-<?= $safeConduct['id'] ?>">
                        <?php if(empty($safeConduct['details']['containers']) && empty($safeConduct['details']['goods'])): ?>
                            <td colspan="8" class="text-center">No any data available</td>
                        <?php else: ?>
                            <td></td>
                            <td colspan="7" style="max-width: 767px">
                                <?php $this->load->view('safe_conduct/_data_detail', [
                                    'safeConductContainers' => $safeConduct['details']['containers'],
                                    'safeConductGoods' => $safeConduct['details']['goods'],
                                ]) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            No safe conduct available
        <?php endif; ?>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Handlings</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($handlings)): ?>
            <table class="table no-datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Handling Type</th>
                    <th>No Handling</th>
                    <th>Handling Date</th>
                    <th>Created At</th>
                    <th style="width: 50px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($handlings as $handling): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $handling['handling_type'] ?></td>
                        <td>
                            <a href="<?= site_url('handling/view/' . $handling['id']) ?>" target="_blank">
                                <?= $handling['no_handling'] ?>
                            </a>
                        </td>
                        <td><?= readable_date($handling['handling_date']) ?></td>
                        <td><?= readable_date($handling['created_at']) ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#detail-handling-<?= $handling['id'] ?>">
                                Reveal
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="detail-handling-<?= $handling['id'] ?>">
                        <?php if(empty($handling['details']['containers']) && empty($handling['details']['goods'])): ?>
                            <td colspan="6" class="text-center">No any data available</td>
                        <?php else: ?>
                            <td></td>
                            <td colspan="5" style="max-width: 767px">
                                <?php $this->load->view('handling/_data_detail', [
                                    'handlingContainers' => $handling['details']['containers'],
                                    'handlingGoods' => $handling['details']['goods'],
                                ]) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            No handling available
        <?php endif; ?>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Jobs</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($workOrders)): ?>
            <table class="table no-datatable responsive">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Handling Type</th>
                    <th>No Handling</th>
                    <th>No Job</th>
                    <th>Completed Date</th>
                    <th>Created At</th>
                    <th style="width: 50px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($workOrders as $workOrder): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $workOrder['handling_type'] ?></td>
                        <td>
                            <a href="<?= site_url('handling/view/' . $workOrder['id_handling']) ?>" target="_blank">
                                <?= $workOrder['no_handling'] ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>" target="_blank">
                                <?= $workOrder['no_work_order'] ?>
                            </a>
                        </td>
                        <td><?= format_date($workOrder['completed_at'], 'd F Y H:i') ?></td>
                        <td><?= format_date($workOrder['created_at'], 'd F Y H:i') ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#detail-job-<?= $workOrder['id'] ?>">
                                Reveal
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="detail-job-<?= $workOrder['id'] ?>">
                        <?php if(empty($workOrder['details']['containers']) && empty($workOrder['details']['goods'])): ?>
                            <td colspan="7" class="text-center">No any data available</td>
                        <?php else: ?>
                            <td></td>
                            <td colspan="6" style="max-width: 767px">
                                <?php $this->load->view('workorder/_data_detail', [
                                    'containers' => $workOrder['details']['containers'],
                                    'goods' => $workOrder['details']['goods'],
                                    'handling_type' => $workOrder['handling_type'],
                                ]) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            No job available
        <?php endif; ?>
    </div>
</div>