<?php $this->load->view('qr/index') ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Found Safe Conduct</h3>
    </div>
    <div class="box-body">
        <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="display: flex; align-items: center">
                <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $safeConduct['no_safe_conduct'] ?>" class="mr10">
                <div>
                    <p class="lead mb0">
                        No Safe Conduct: <strong><?= $safeConduct['no_safe_conduct'] ?></strong>
                    </p>
                    <p class="text-muted mb0"><?= $safeConduct['customer_name'] ?></p>
                    <p class="text-muted"><?= $safeConduct['type'] ?></p>
                </div>
            </div>
            <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                <div>
                    <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>" class="btn btn-primary">
                        View Detail
                    </a>
                    <a href="<?= site_url('safe-conduct/print-safe-conduct-mode2/' . $safeConduct['id']) ?>" class="btn btn-warning">
                        Print
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Safe Conduct Detail</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view mb0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['no_safe_conduct'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Booking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['no_booking'], 'No booking') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['no_reference']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Warehouse of Origin</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['source_warehouse'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">E-seal</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['no_eseal'], 'No eseal') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Vehicle Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['vehicle_type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Police</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['no_police'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Driver</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['driver'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Expedition</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['expedition'] ?> (<?= $safeConduct['expedition_type'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Check In</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= empty($safeConduct['security_in_date']) ? '-' : readable_date($safeConduct['security_in_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Check Out</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= empty($safeConduct['security_out_date']) ? '-' : readable_date($safeConduct['security_out_date']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">TEP Code</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['tep_code'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">TEP Check In</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($safeConduct['tep_in_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">TEP Check Out</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($safeConduct['tep_out_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Check In Remark</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['security_in_description'], 'No check in remark') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Check Out Remark</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['security_out_description'], 'No check out remark') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Attachment</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (empty($safeConduct['attachment'])): ?>
                                    No attachment
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/safe_conducts/' . $safeConduct['attachment']) ?>">
                                        Download Safe Conduct
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Photo</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (empty($safeConduct['photo'])): ?>
                                    No photo
                                <?php else: ?>
                                    <?php $safeConduct['photo'] = explode('/', $safeConduct['photo']); ?>
                                    <?php foreach ($safeConduct['photo'] as $photo): ?>
                                        <a href="<?= base_url('uploads/safe_conducts_photo/' . $photo) ?>">
                                            <?= $photo ?>
                                        </a> <br>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">CY Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($safeConduct['cy_date'], false, 'No date') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($safeConduct['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($safeConduct['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['creator_name'], 'Unknown') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($safeConductContainers)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Safe Conduct Containers</h3>
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
                    <?php foreach ($safeConductContainers as $container): ?>
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

                    <?php if (empty($safeConductContainers)): ?>
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

<?php if(!empty($safeConductGoods)): ?>
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
                    <?php foreach ($safeConductGoods as $index => $item): ?>
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

                    <?php if (empty($safeConductGoods)): ?>
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

<?php if(!empty($safeConductChecklists)): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Checklist Start</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Link Photo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($safeConductChecklists as $checklist): ?>
                            <?php if($checklist['type'] == 'CHECK IN'): ?>
                                <tr>
                                    <td><?= $i; ?></td>
                                    <td>
                                        <a href="<?= base_url("uploads/safe-conducts-checklist/" . $checklist['attachment']) ?>">
                                            <?= strtoupper($checklist['attachment'])?>
                                        </a>
                                    </td>
                                </tr>
                                <?php if (!empty($checklist['attachment_seal'])): ?>
                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td>
                                            <a href="<?= base_url("uploads/safe-conducts-checklist/" . $checklist['attachment_seal']) ?>">
                                                Photo Seal : <?= strtoupper($checklist['attachment_seal'])?>
                                            </a><br>
                                            Description Seal : <?= if_empty(strtoupper($checklist['description']), '-') ?>
                                        </td>
                                    </tr>
                                    <?php $i++ ?>
                                <?php endif; ?>
                                <?php $i++ ?>
                            <?php endif; ?>
                        <?php endforeach ?>
                        <?php if ($i == 1): ?>
                            <tr>
                                <td colspan="2">No checklist data</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Checklist Stop</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Link Photo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($safeConductChecklists as $checklist): ?>
                            <?php if($checklist['type'] == 'CHECK OUT'): ?>
                                <tr>
                                    <td><?= $i; ?></td>
                                    <td>
                                        <a href="<?= base_url("uploads/safe-conducts-checklist/" . $checklist['attachment']) ?>">
                                            <?= strtoupper($checklist['attachment'])?>
                                        </a>
                                    </td>
                                </tr>
                                <?php if (!empty($checklist['attachment_seal'])): ?>
                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td>
                                            <a href="<?= base_url("uploads/safe-conducts-checklist/" . $checklist['attachment_seal']) ?>">
                                                Photo Seal : <?= strtoupper($checklist['attachment_seal'])?>
                                            </a><br>
                                            Description Seal : <?= if_empty(strtoupper($checklist['description']), '-') ?>
                                        </td>
                                    </tr>
                                    <?php $i++ ?>
                                <?php endif; ?>
                                <?php $i++ ?>
                            <?php endif; ?>
                        <?php endforeach ?>
                        <?php if ($i == 1): ?>
                            <tr>
                                <td colspan="2">No checklist data</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>