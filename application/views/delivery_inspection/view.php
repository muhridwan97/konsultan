<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Inspection</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_INSPECTION_EDIT)): ?>
            <a href="<?= site_url('delivery-inspection/edit/' . $deliveryInspection['id']) ?>" class="btn btn-primary pull-right">
                Edit Inspection
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $deliveryInspection['date'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Location</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryInspection['location'], 'Not set') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">PIC TCI</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryInspection['pic_tci'], 'Not set') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">PIC Khaisan</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryInspection['pic_khaisan'], 'Not set') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">PIC SMGP</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryInspection['pic_smgp'], 'Not set') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Total Vehicle</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $deliveryInspection['total_match'] ?> / <?= $deliveryInspection['total_vehicle'] ?> Matched
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($deliveryInspection['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    DeliveryInspectionModel::STATUS_PENDING => 'default',
                                    DeliveryInspectionModel::STATUS_CONFIRMED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $deliveryInspection['status'], 'default') ?>">
                                    <?= $deliveryInspection['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($deliveryInspection['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($deliveryInspection['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Delivery Inspection Details</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>TEP Code</th>
                        <th>No Vehicle</th>
                        <th>Vehicle Type</th>
                        <th>No Order</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($deliveryInspectionDetails as $deliveryInspectionDetail): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $deliveryInspectionDetail['tep_code'] ?></td>
                            <td><?= $deliveryInspectionDetail['no_vehicle'] ?></td>
                            <td><?= $deliveryInspectionDetail['vehicle_type'] ?></td>
                            <td><?= $deliveryInspectionDetail['no_order'] ?></td>
                        </tr>
                    <?php if (!empty($deliveryInspectionDetail['safe_conducts'])): ?>
                        <tr>
                            <td></td>
                            <td colspan="10">
                                <table class="table table-sm no-datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 50px">No</th>
                                        <th>No Safe Conduct</th>
                                        <th>No Reference</th>
                                        <th>No Plate</th>
                                        <th>Goods Load</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                <?php foreach ($deliveryInspectionDetail['safe_conducts'] as $safeConductIndex => $safeConduct): ?>
                                    <tr>
                                        <td><?= $safeConductIndex + 1 ?></td>
                                        <td><?= $safeConduct['no_safe_conduct'] ?></td>
                                        <td><?= $safeConduct['no_reference'] ?></td>
                                        <td><?= $safeConduct['no_police'] ?></td>
                                        <td><?= $safeConduct['goods_load'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($deliveryInspectionDetails)): ?>
                        <tr>
                            <td colspan="5">No delivery inspection detail</td>
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