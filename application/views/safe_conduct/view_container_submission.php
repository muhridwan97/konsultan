<div class="box box-primary" id="table-safe-conduct">
    <div class="box-header with-border">
        <h3 class="box-title">View Warehouse Origin Container</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <div class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>">
                                    <?= $safeConduct['no_safe_conduct'] ?>
                                </a>
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
                                <a href="<?= site_url('booking/view/' . $safeConduct['id_booking']) ?>">
                                    <?= $safeConduct['no_reference'] ?>
                                </a>
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
                        <label class="col-sm-4">Expedition</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['expedition'] ?> (<?= $safeConduct['expedition_type'] ?>)
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
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

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Warehouse Origin Container</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-work-order">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Container</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Result</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($warehouseOriginContainers as $index => $warehouseOriginContainer): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $warehouseOriginContainer['no_container'] ?></td>
                            <td class="text-center">
                                <?php
                                $dataLabel = [
                                    WarehouseOriginTrackingContainerModel::STATUS_SUCCESS => 'success',
                                    WarehouseOriginTrackingContainerModel::STATUS_FAILED => 'danger',
                                ];
                                ?>
                                <?php if ($warehouseOriginContainer['status'] == WarehouseOriginTrackingContainerModel::STATUS_FAILED && AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_UPDATE_DATA)): ?>
                                    <a href="<?= site_url('safe-conduct/retry-warehouse-origin-submission/' . $warehouseOriginContainer['id']) ?>" class="btn btn-danger">
                                        RETRY
                                    </a>
                                <?php else: ?>
                                    <span class="label label-<?= get_if_exist($dataLabel, $warehouseOriginContainer['status'], 'primary') ?>">
                                        <?= $warehouseOriginContainer['status'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <pre style="width: 300px"><?= if_empty(str_replace('\"', '"', str_replace('\/', '/', json_encode(json_decode($warehouseOriginContainer['tracking_payload_data'], true), JSON_PRETTY_PRINT))), '-') ?></pre>
                            </td>
                            <td>
                                <pre style="width: 300px"><?= if_empty($warehouseOriginContainer['tracking_payload_result'], '-') ?></pre>
                            </td>
                            <td><?= format_date($warehouseOriginContainer['created_at'], 'd M Y H:i') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($warehouseOriginContainers)): ?>
                        <tr>
                            <td colspan="9">No tracking data available</td>
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