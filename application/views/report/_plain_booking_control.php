<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
</head>
<body>
<table border="1" class="table table-solid table-bordered table-condensed no-wrap">
    <thead>
    <tr class="success">
        <th>#</th>
        <th>Booking In</th>
        <th>Type</th>
        <th>Customer</th>
        <th>Booking Date</th>
        <th>Status</th>
        <th>Upload</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="excel-text">
            <a href="<?= site_url('booking/view/' . $booking['id']) ?>">
                <?= $booking['no_booking'] ?>
            </a><br>
            <?= $booking['no_reference'] ?>
        </td>
        <td><?= $booking['booking_type'] ?></td>
        <td><?= $booking['customer_name'] ?></td>
        <td><?= readable_date($booking['booking_date']) ?></td>
        <td><?= $booking['status_control'] ?></td>
        <td>
            <a href="<?= site_url('upload-document/download-file/' . $booking['id_upload']) ?>">
                <?= $booking['no_upload'] ?>
            </a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="6">
            <table border="1" class="table table-solid table-bordered table-condensed no-wrap">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Job</th>
                    <th>Completed At</th>
                    <th>Tally</th>
                    <th>Safe Conduct</th>
                    <th>Driver</th>
                    <th>No Police</th>
                    <th>Status</th>
                    <th>Doc</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($booking['work_orders'] as $index => $workOrder): ?>
                    <tr class="warning">
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                <?= $workOrder['no_work_order'] ?>
                            </a>
                        </td>
                        <td><?= readable_date($workOrder['completed_at']) ?></td>
                        <td><?= $workOrder['tally_name'] ?></td>
                        <td>
                            <?php if(empty($workOrder['no_safe_conduct'])): ?>
                                -
                            <?php else: ?>
                                <a href="<?= site_url('safe_conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                    <?= $workOrder['no_safe_conduct'] ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= if_empty($workOrder['driver'], '-') ?></td>
                        <td><?= if_empty($workOrder['no_police'], '-') ?></td>
                        <td><?= if_empty($workOrder['status'], '-') ?></td>
                        <td>
                            <?php if (empty($workOrder['attachment'])): ?>
                                -
                            <?php else: ?>
                                <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                    Download
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="8">
                            <?php if(!empty($workOrder['containers'])): ?>
                                <?php $this->load->view('booking_control/_data_container', [
                                    'containers' => $workOrder['containers'],
                                    'borderPlain' => true
                                ]) ?>
                            <?php endif; ?>

                            <?php if(!empty($workOrder['goods'])): ?>
                                <?php $this->load->view('booking_control/_data_goods', [
                                    'goods' => $workOrder['goods'],
                                    'borderPlain' => true
                                ]) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if(empty($booking['work_orders'])): ?>
                    <tr>
                        <td colspan="9">No jobs available</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

<table>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>

<table border="1" class="table table-solid table-bordered table-condensed no-wrap">
    <thead>
    <tr class="danger">
        <th>#</th>
        <th>Booking Out</th>
        <th>Type</th>
        <th>Customer</th>
        <th>Booking Date</th>
        <th>Status</th>
        <th>Upload</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($bookingOuts as $index => $bookingOut): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td class="excel-text">
                <a href="<?= site_url('booking/view/' . $bookingOut['id']) ?>">
                    <?= $bookingOut['no_booking'] ?>
                </a><br>
                <?= $bookingOut['no_reference'] ?>
            </td>
            <td><?= $booking['booking_type'] ?></td>
            <td><?= $bookingOut['customer_name'] ?></td>
            <td><?= readable_date($bookingOut['booking_date']) ?></td>
            <td><?= $booking['status_control'] ?></td>
            <td>

                <a href="<?= site_url('upload-document/download-file/' . $booking['id_upload']) ?>">
                    <?= $booking['no_upload'] ?>
                </a>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6">
                <table class="table table-condensed table-striped table-bordered no-datatable" border="1">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Job</th>
                        <th>Completed At</th>
                        <th>Tally</th>
                        <th>Safe Conduct</th>
                        <th>Driver</th>
                        <th>No Police</th>
                        <th>Status</th>
                        <th>Doc</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bookingOut['work_orders'] as $index => $workOrder): ?>
                        <tr class="warning">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                    <?= $workOrder['no_work_order'] ?>
                                </a>
                            </td>
                            <td><?= readable_date($workOrder['completed_at']) ?></td>
                            <td><?= $workOrder['tally_name'] ?></td>
                            <td>
                                <?php if(empty($workOrder['no_safe_conduct'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('safe_conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                        <?= $workOrder['no_safe_conduct'] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= if_empty($workOrder['driver'], '-') ?></td>
                            <td><?= if_empty($workOrder['no_police'], '-') ?></td>
                            <td><?= if_empty($workOrder['status'], '-') ?></td>
                            <td>
                                <?php if (empty($workOrder['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                        Download
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8">
                                <?php if(!empty($workOrder['containers'])): ?>
                                    <?php $this->load->view('booking_control/_data_container', [
                                        'containers' => $workOrder['containers'],
                                        'borderPlain' => true
                                    ]) ?>
                                <?php endif; ?>

                                <?php if(!empty($workOrder['goods'])): ?>
                                    <?php $this->load->view('booking_control/_data_goods', [
                                        'goods' => $workOrder['goods'],
                                        'borderPlain' => true
                                    ]) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <?php if(empty($bookingOut['work_orders'])): ?>
                        <tr>
                            <td colspan="9">No jobs available</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
</body>
</html>