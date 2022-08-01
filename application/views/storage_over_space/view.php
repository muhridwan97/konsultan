<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Over Space</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageOverSpace['type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Date Range</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($storageOverSpace['date_from'], 'd F Y') ?>
                                -
                                <?= format_date($storageOverSpace['date_to'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Validated</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageOverSpace['total_validated'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Skipped</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageOverSpace['total_skipped'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Pending</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageOverSpace['total_pending'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Total Proceed</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageOverSpace['total_proceed'] ?> / <?= $storageOverSpace['total_customer_over_space'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($storageOverSpace['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $overSpaceStatuses = [
                                    StorageOverSpaceModel::STATUS_PENDING => 'default',
                                    StorageOverSpaceModel::STATUS_PROCEED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($overSpaceStatuses, $storageOverSpace['status'], 'default') ?>">
                                    <?= $storageOverSpace['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($storageOverSpace['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($storageOverSpace['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Customer Over Spaces</h3>
                <?php if ($storageOverSpace['status'] == StorageOverSpaceModel::STATUS_PENDING): ?>
                    <div class="pull-right">
                        <a href="<?= site_url('storage-over-space/approve-all/' . $storageOverSpace['id']) ?>"
                           class="btn btn-success btn-validate" data-validate="validate all" data-label="All customer over space">
                            <i class="ion-checkmark mr10"></i>Validate All
                        </a>
                        <a href="<?= site_url('storage-over-space/skip-all/' . $storageOverSpace['id']) ?>"
                           class="btn btn-danger btn-validate" data-validate="skip all" data-label="All customer over space">
                            <i class="ion-close mr10"></i>Skip All
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px" rowspan="2">No</th>
                        <th rowspan="2">Customer</th>
                        <th colspan="3" class="text-center">Over Space (<?= $storageOverSpace['type'] ?>)</th>
                        <th class="text-center" rowspan="2">Status</th>
                        <th style="width: 50px" class="text-center" rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th class="text-center">Warehouse</th>
                        <th class="text-center">Yard</th>
                        <th class="text-center">Covered Yard</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $statuses = [
                        StorageOverSpaceCustomerModel::STATUS_PENDING => 'default',
                        StorageOverSpaceCustomerModel::STATUS_VALIDATED => 'success',
                        StorageOverSpaceCustomerModel::STATUS_SKIPPED => 'danger',
                        '1' => 'danger',
                        '0' => 'success',
                    ]
                    ?>
                    <?php foreach ($storageOverSpaceCustomers as $index => $storageOverSpaceCustomer): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $storageOverSpaceCustomer['customer_name'] ?></td>
                            <td class="text-center">
                                <span class="label label-<?= get_if_exist($statuses, $storageOverSpaceCustomer['is_warehouse_over_space'], 'default') ?>">
                                    <?= $storageOverSpaceCustomer['is_warehouse_over_space'] ? 'YES' : 'NO' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="label label-<?= get_if_exist($statuses, $storageOverSpaceCustomer['is_yard_over_space'], 'default') ?>">
                                    <?= $storageOverSpaceCustomer['is_yard_over_space'] ? 'YES' : 'NO' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="label label-<?= get_if_exist($statuses, $storageOverSpaceCustomer['is_covered_yard_over_space'], 'default') ?>">
                                    <?= $storageOverSpaceCustomer['is_covered_yard_over_space'] ? 'YES' : 'NO' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="label label-<?= get_if_exist($statuses, $storageOverSpaceCustomer['status'], 'default') ?>">
                                    <?= $storageOverSpaceCustomer['status'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-header">ACTION</li>
                                        <li>
                                            <a href="<?= site_url("storage-over-space-customer/view/{$storageOverSpaceCustomer['id']}/WAREHOUSE") ?>">
                                                <i class="fa ion-search"></i> View Warehouse
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= site_url("storage-over-space-customer/view/{$storageOverSpaceCustomer['id']}/YARD") ?>">
                                                <i class="fa ion-search"></i> View Yard
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= site_url("storage-over-space-customer/view/{$storageOverSpaceCustomer['id']}/COVERED+YARD") ?>">
                                                <i class="fa ion-search"></i> View Covered Yard
                                            </a>
                                        </li>
                                        <?php if ($storageOverSpaceCustomer['status'] == StorageOverSpaceCustomerModel::STATUS_PENDING): ?>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE)): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a href="<?= site_url('storage-over-space-customer/approve/' . $storageOverSpaceCustomer['id']) ?>"
                                                       class="btn-validate" data-validate="validate" data-label="Storage Over Space <?= $storageOverSpaceCustomer['customer_name'] ?>">
                                                        <i class="fa ion-checkmark"></i> Validate
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= site_url('storage-over-space-customer/skip/' . $storageOverSpaceCustomer['id']) ?>"
                                                       class="btn-validate" data-validate="skip" data-label="Storage Over Space <?= $storageOverSpaceCustomer['customer_name'] ?>">
                                                        <i class="fa ion-close"></i> Skip
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($storageOverSpaceCustomers)): ?>
                        <tr>
                            <td colspan="4">No storage over space customer</td>
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
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statusHistories as $index => $statusHistory): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($overSpaceStatuses, $statusHistory['status'], 'default') ?>">
                                    <?= $statusHistory['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($statusHistory['description'], '-') ?></td>
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>