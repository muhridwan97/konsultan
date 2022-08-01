<?php $this->load->view('qr/index') ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Found Transporter Entry Permit</h3>
    </div>
    <div class="box-body">
        <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="display: flex; align-items: center">
                <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $tep['tep_code'] ?>" class="mr10">
                <div>
                    <p class="lead mb0">
                        No Entry Permit: <strong><?= $tep['tep_code'] ?></strong>
                    </p>
                    <p class="text-muted mb0"><?= if_empty($tep['receiver_no_police'], 'No plat police') ?> - <?= if_empty(ucwords($tep['receiver_name']), 'No name') ?></p>
                    <p class="text-muted"><?= $tep['tep_category'] ?></p>
                </div>
            </div>
            <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                <div>
                    <a href="<?= site_url('transporter-entry-permit/view/' . $tep['id']) ?>" class="btn btn-primary">
                        View Detail
                    </a>
                    <a href="<?= site_url('transporter-entry-permit/print/' . $tep['id']) ?>" class="btn btn-warning">
                        Print
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Entry Permit Detail</h3>
    </div>
    <div class="box-body">
        <div class="form-horizontal form-view mb0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['tep_category'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Code</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['tep_code'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Expired At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['expired_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Vehicle</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_vehicle'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Police</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_no_police'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Checked In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['checked_in_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Checked Out</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['checked_out_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Additional Guest</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['additional_guest_name'], 'No additional guest') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Check In Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['checked_in_description'], 'No checkout in description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Check Out Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['checked_out_description'], 'No check out description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Email</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_email'], 'No email') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Contact</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_contact'], 'No contact') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['creator_name'], 'Unknown') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= readable_date($tep['created_at']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($tepContainers)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">TEP Containers</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Container</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Seal</th>
                        <th>Position</th>
                        <th>Is Empty</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($tepContainers as $container): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $container['no_container'] ?></td>
                            <td><?= $container['type'] ?></td>
                            <td><?= $container['size'] ?></td>
                            <td><?= if_empty($container['seal'], '-') ?></td>
                            <td><?= if_empty($container['position'], '-') ?></td>
                            <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                                <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                            </td>
                            <td><?= if_empty($container['status'], 'No Status') ?></td>
                            <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                <?= if_empty($container['status_danger'], 'No Status') ?>
                            </td>
                            <td><?= if_empty($container['description'], 'No description') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($tepContainers)): ?>
                        <tr>
                            <td colspan="10" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($tepGoods)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Safe Conduct Goods</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Goods</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Unit Weight (Kg)</th>
                        <th>Total Weight (Kg)</th>
                        <th>Unit Gross (Kg)</th>
                        <th>Total Gross (Kg)</th>
                        <th>Unit Length (M)</th>
                        <th>Unit Width (M)</th>
                        <th>Unit Height (M)</th>
                        <th>Volume (M<sup>3</sup>)</th>
                        <th>Total Volume (M<sup>3</sup>)</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Ex Container</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tepGoods as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?= $item['goods_name'] ?><br>
                                <small class="text-muted"><?= $item['no_goods'] ?></small>
                            </td>
                            <td><?= numerical($item['quantity'], 3, true) ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                            <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                            <td><?= if_empty($item['position'], '-') ?></td>
                            <td><?= if_empty($item['status'], 'No status') ?></td>
                            <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                <?= $item['status_danger'] ?>
                            </td>
                            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                            <td><?= if_empty($item['description'], 'No description') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($tepGoods)): ?>
                        <tr>
                            <td colspan="18" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
<?php endif; ?>

<?php if(!empty($customers)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Customers</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Customer</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($customers as $index => $customer): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $customer['no_person'] ?></td>
                        <td><?= $customer['name'] ?></td>
                        <td><?= $customer['email'] ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($safeConducts)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Safe Conducts</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Safe Conduct</th>
                    <th>Type</th>
                    <th>No Reference</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Source</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($safeConducts as $index => $safeConduct): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $safeConduct['no_safe_conduct'] ?></td>
                        <td><?= $safeConduct['type'] ?></td>
                        <td><?= $safeConduct['no_reference'] ?></td>
                        <td><?= $safeConduct['security_in_date'] ?></td>
                        <td><?= $safeConduct['security_out_date'] ?></td>
                        <td><?= $safeConduct['source_warehouse'] ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($bookings)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Bookings</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Reference</th>
                    <th>Type</th>
                    <th>Source</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= if_empty(get_if_exist($booking, 'no_reference'), $booking['description']) ?></td>
                        <td><?= $booking['category'] ?></td>
                        <td><?= key_exists('no_reference', $booking) && !empty($booking['no_reference']) ? 'BOOKING' : 'UPLOAD' ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>