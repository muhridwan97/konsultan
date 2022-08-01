<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Capacity</h3>
    </div>
    <div role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Customer Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $customerStorageCapacity['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Effective Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($customerStorageCapacity['effective_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Expired Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($customerStorageCapacity['expired_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    CustomerStorageCapacityModel::STATUS_PENDING => 'warning',
                                    CustomerStorageCapacityModel::STATUS_EXPIRED => 'danger',
                                    CustomerStorageCapacityModel::STATUS_ACTIVE => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $customerStorageCapacity['status'], 'default') ?>">
                                    <?= $customerStorageCapacity['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($customerStorageCapacity['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Warehouse</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= numerical($customerStorageCapacity['warehouse_capacity'], 2, true) ?> (M<sup>2</sup>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Yard</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= numerical($customerStorageCapacity['yard_capacity'], 2, true) ?> (M<sup>2</sup>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Covered Yard</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= numerical($customerStorageCapacity['covered_yard_capacity'], 2, true) ?> (M<sup>2</sup>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($customerStorageCapacity['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($customerStorageCapacity['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </div>
</div>