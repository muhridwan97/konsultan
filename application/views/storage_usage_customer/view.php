<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Usage <?= $storageUsageCustomer['customer_name'] ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($storageUsageCustomer['date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Warehouse Capacity</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['warehouse_capacity'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Yard Capacity</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['yard_capacity'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Covered Yard Capacity</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['covered_yard_capacity'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Warehouse Usage</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['warehouse_usage'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Yard Usage</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['yard_usage'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Covered Yard Usage</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageUsageCustomer['covered_yard_usage'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    StorageUsageCustomerModel::STATUS_PENDING => 'default',
                                    StorageUsageCustomerModel::STATUS_VALIDATED => 'success',
                                    StorageUsageCustomerModel::STATUS_SKIPPED => 'danger',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $storageUsageCustomer['status'], 'default') ?>">
                                    <?= $storageUsageCustomer['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($storageUsageCustomer['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
                                <span class="label label-<?= get_if_exist($statuses, $statusHistory['status'], 'default') ?>">
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