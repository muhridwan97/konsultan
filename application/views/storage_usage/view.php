<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Usage <?= $storageUsage['date'] ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($storageUsage['date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Validated</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageUsage['total_validated'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Skipped</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageUsage['total_skipped'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Pending</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageUsage['total_pending'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Total Proceed</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $storageUsage['total_proceed'] ?> / <?= $storageUsage['total_customer_usage'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($storageUsage['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    StorageUsageModel::STATUS_PENDING => 'default',
                                    StorageUsageModel::STATUS_PROCEED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $storageUsage['status'], 'default') ?>">
                                    <?= $storageUsage['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($storageUsage['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Customer Usages</h3>
                <?php if ($storageUsage['status'] == StorageUsageModel::STATUS_PENDING): ?>
                    <div class="pull-right">
                        <a href="<?= site_url('storage-usage/approve-all/' . $storageUsage['id']) ?>"
                           class="btn btn-success btn-validate" data-validate="validate all" data-label="All customer usage">
                            <i class="ion-checkmark mr10"></i>Validate All
                        </a>
                        <a href="<?= site_url('storage-usage/skip-all/' . $storageUsage['id']) ?>"
                           class="btn btn-danger btn-validate" data-validate="skip all" data-label="All customer usage">
                            <i class="ion-close mr10"></i>Skip All
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px">No</th>
                        <th rowspan="2">Customer</th>
                        <th colspan="3" class="text-center">Capacity</th>
                        <th colspan="3" class="text-center">Usage</th>
                        <th rowspan="2" class="text-center">Status</th>
                        <th rowspan="2" class="text-center">Action</th>
                    </tr>
                    <tr>
                        <th>Warehouse</th>
                        <th>Yard</th>
                        <th>Covered Yard</th>
                        <th>Warehouse</th>
                        <th>Yard</th>
                        <th>Covered Yard</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $statuses = [
                        StorageUsageCustomerModel::STATUS_PENDING => 'default',
                        StorageUsageCustomerModel::STATUS_VALIDATED => 'success',
                        StorageUsageCustomerModel::STATUS_SKIPPED => 'danger',
                    ]
                    ?>
                    <?php foreach ($storageUsageCustomers as $index => $storageUsageCustomer): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $storageUsageCustomer['customer_name'] ?></td>
                            <td><?= $storageUsageCustomer['warehouse_capacity'] ?></td>
                            <td><?= $storageUsageCustomer['yard_capacity'] ?></td>
                            <td><?= $storageUsageCustomer['covered_yard_capacity'] ?></td>
                            <td>
                                <?= $storageUsageCustomer['warehouse_usage'] ?>
                                (<?= numerical($storageUsageCustomer['used_warehouse_percent'], 1, true) ?>%)
                            </td>
                            <td>
                                <?= $storageUsageCustomer['yard_usage'] ?>
                                (<?= numerical($storageUsageCustomer['used_yard_percent'], 1, true) ?>%)
                            </td>
                            <td>
                                <?= $storageUsageCustomer['covered_yard_usage'] ?>
                                (<?= numerical($storageUsageCustomer['used_covered_yard_percent'], 1, true) ?>%)
                            </td>
                            <td class="text-center">
                                <span class="label label-<?= get_if_exist($statuses, $storageUsageCustomer['status'], 'default') ?>">
                                    <?= $storageUsageCustomer['status'] ?>
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
                                            <a href="<?= site_url('storage-usage-customer/view/' . $storageUsageCustomer['id']) ?>">
                                                <i class="fa ion-search"></i> View
                                            </a>
                                        </li>
                                        <?php if ($storageUsageCustomer['status'] == StorageUsageCustomerModel::STATUS_PENDING): ?>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE)): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a href="<?= site_url('storage-usage-customer/approve/' . $storageUsageCustomer['id']) ?>"
                                                       class="btn-validate" data-validate="validate" data-label="Storage Usage <?= $storageUsageCustomer['customer_name'] ?>">
                                                        <i class="fa ion-checkmark"></i> Validate
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= site_url('storage-usage-customer/skip/' . $storageUsageCustomer['id']) ?>"
                                                       class="btn-validate" data-validate="skip" data-label="Storage Usage <?= $storageUsageCustomer['customer_name'] ?>">
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
                    <?php if(empty($storageUsageCustomers)): ?>
                        <tr>
                            <td colspan="10">No storage usage customer</td>
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