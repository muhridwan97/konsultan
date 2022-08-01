<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Link TEP <?= $transporterEntryPermitTracking['tep_code'] ?></h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">TEP Code</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('transporter-entry-permit/view/' . $transporterEntryPermitTracking['id_tep']) ?>">
                                    <?= $transporterEntryPermitTracking['tep_code'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Linked Vehicle</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $transporterEntryPermitTracking['phbid_no_vehicle'] ?: '-' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Linked Order</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $transporterEntryPermitTracking['phbid_no_order'] ?: '-' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Site Actual Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($transporterEntryPermitTracking['site_transit_actual_date'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Unload Actual Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($transporterEntryPermitTracking['unloading_actual_date'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    TransporterEntryPermitTrackingModel::STATUS_LINKED => 'warning',
                                    TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT => 'primary',
                                    TransporterEntryPermitTrackingModel::STATUS_UNLOADED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $transporterEntryPermitTracking['status'], 'default') ?>">
                                    <?= $transporterEntryPermitTracking['status'] ?: 'NOT LINKED' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($transporterEntryPermitTracking['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $transporterEntryPermitTracking['creator_name'] ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($transporterEntryPermitTracking['created_at'], 'd F Y H:i') ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($transporterEntryPermitTracking['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">PhBid Tracking</h3>
            </div>
            <div class="box-body">
                <div role="form" class="form-horizontal form-view">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Take / Ambil Kontainer</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $transporterEntryPermitTracking['tanggal_ambil_kontainer'] ?: '-' ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">RM Kolam / Stuffing</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $transporterEntryPermitTracking['tanggal_stuffing'] ?: '-' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Site Transit / Dooring</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $transporterEntryPermitTracking['tanggal_dooring'] ?: '-' ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Unload / Tanggal kembali Kedepo</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $transporterEntryPermitTracking['tanggal_kontainer_kembali_kedepo'] ?: '-' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Safe Conduct Handovers</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 50px" class="text-center">No</th>
                        <th>No Safe Conduct</th>
                        <th>Received Date</th>
                        <th>Driver Handover Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($safeConductHandovers as $index => $safeConduct): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <a href="<?= site_url('safe-conduct-handover/view/' . $safeConduct['id']) ?>">
                                    <?= $safeConduct['no_safe_conduct'] ?>
                                </a>
                            </td>
                            <td><?= format_date($safeConduct['received_date'], 'd F Y H:i') ?: '-' ?></td>
                            <td><?= format_date($safeConduct['driver_handover_date'], 'd F Y H:i') ?: '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($safeConductHandovers)): ?>
                        <tr>
                            <td colspan="4">No safe conduct available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status Histories</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 50px" class="text-center">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Data</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statusHistories as $index => $statusHistory): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $statusHistory['status'], 'default') ?>">
                                    <?= $statusHistory['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($statusHistory['description'], '-') ?></td>
                            <td>
                                <?php if(empty($statusHistory['data'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('history/view/' . $statusHistory['id']) ?>">
                                        View History
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= format_date($statusHistory['created_at'], 'd F Y H:i') ?></td>
                            <td><?= if_empty($statusHistory['creator_name'], "No user") ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($statusHistories)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>