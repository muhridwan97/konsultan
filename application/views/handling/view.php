<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Handling</h3>
    </div>
    <div class="box-body">
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Handling</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $handling['no_handling'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Handling</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $handling['handling_type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $handling['handling_category'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    HandlingModel::STATUS_PENDING => 'default',
                                    HandlingModel::STATUS_REJECTED => 'danger',
                                    HandlingModel::STATUS_APPROVED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $handling['status'], 'default') ?>"><?= $handling['status'] ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Handling Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= (new DateTime($handling['handling_date']))->format('d F Y') ?>
                                (
                                <?php if ($handling['handling_date_remaining'] < 0): ?>
                                    Passed
                                <?php else: ?>
                                    <span class="<?= $handling['handling_date_remaining'] <= 3 ? 'text-danger' : 'text-success' ?>">
                                            <?= $handling['handling_date_remaining'] ?> days remaining
                                        </span>
                                <?php endif ?>
                                )
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $handling['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $handling['id_booking']) ?>">
                                    <?= $handling['no_booking'] ?>
                                    (<?= if_empty($handling['no_reference'], '-') ?>)
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($handling['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= (new DateTime($handling['created_at']))->format('d F Y H:i') ?>
                                (
                                <?php if ($handling['handling_date_remaining'] < 0): ?>
                                    Passed
                                <?php else: ?>
                                    <span class="<?= ($handling['handling_date_remaining'] - 3) > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $handling['create_date_elapsed'] ?> days elapsed
                                    </span>
                                <?php endif ?>
                                )
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($handling['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($handling['creator_name'], 'Unknown') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php $this->load->view('handling/_data_detail') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Work Orders</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable responsive">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>No Job</th>
                        <th>Queue</th>
                        <th>Created At</th>
                        <th>Gate In</th>
                        <th>Completed At</th>
                        <th>Gate Out</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($workOrders as $workOrder): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                    <?= $workOrder['no_work_order'] ?>
                                </a>
                            </td>
                            <td><?= $workOrder['queue'] ?></td>
                            <td><?= format_date($workOrder['created_at'], 'd F Y') ?></td>
                            <td><?= if_empty(format_date($workOrder['gate_in_date'], 'd F Y H:i'), '-') ?></td>
                            <td><?= if_empty(format_date($workOrder['completed_at'], 'd F Y H:i'), '-') ?></td>
                            <td><?= if_empty(format_date($workOrder['gate_out_date'], 'd F Y H:i'), '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($workOrders)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
        <a href="<?= site_url('handling/print-handling/' . $handling['id']) ?>" class="btn btn-primary pull-right">
            Print Handling
        </a>
    </div>
</div>