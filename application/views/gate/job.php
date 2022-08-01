<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Gate Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('gate/_scanner') ?>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Gate Job Detail</h3>
    </div>
    <div class="box-body">
        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif; ?>
        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>Job Sheet Assignment</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $workOrder['no_handling'] ?>">
                    <p class="lead" style="margin-top: 10px">No Job: <?= $workOrder['no_work_order'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-workorder"
                          data-id="<?= $workOrder['id'] ?>"
                          data-no="<?= $workOrder['no_work_order'] ?>"
                          data-customer="<?= $workOrder['customer_name'] ?>"
                          data-email="<?= $workOrder['customer_email'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Handling</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $workOrder['no_handling'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Type Handling</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $workOrder['handling_type'] ?> (<?= $workOrder['handling_category'] ?>)
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $workOrder['customer_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($workOrder['description'], 'No description') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Queue</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($workOrder['queue'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Handled By</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($workOrder['tally_name'], 'Not taken yet') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php
                                    $dataLabel = [
                                        WorkOrderModel::STATUS_QUEUED => 'danger',
                                        WorkOrderModel::STATUS_TAKEN => 'warning',
                                        WorkOrderModel::STATUS_COMPLETED => 'success',
                                    ];
                                    ?>
                                    <span class="label label-<?= $dataLabel[$workOrder['status']] ?>">
                                        <?php if(empty($workOrder['gate_in_date'])): ?>
                                            GATE IN
                                        <?php else: ?>
                                            <?= $workOrder['status'] ?>
                                        <?php endif; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Gate In</label>
                            <div class="col-sm-8">
                                <?php if (is_null($workOrder['gate_in_date'])): ?>
                                    <a href="<?= site_url('work-order/check_in/' . $workOrder['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
                                       class="btn btn-success btn-check-in">Check In</a>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($workOrder['gate_in_date']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group <?= is_null($workOrder['gate_in_date']) ? 'mt10' : '' ?>">
                            <label class="col-sm-4 control-label">Gate Out</label>
                            <div class="col-sm-8">
                                <?php if (is_null($workOrder['gate_in_date'])): ?>
                                    <p class="form-control-static">Check In First</p>
                                <?php elseif ($workOrder['status'] != WorkOrderModel::STATUS_COMPLETED && ($workOrder['handling_category'] != HandlingTypeModel::CATEGORY_NON_WAREHOUSE)): ?>
                                    <p class="form-control-static"><?= $workOrder['status'] ?></p>
                                <?php elseif (is_null($workOrder['gate_out_date'])): ?>
                                    <a href="<?= site_url('work-order/check_out/' . $workOrder['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
                                       class="btn btn-danger btn-check-out">Check Out</a>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($workOrder['gate_out_date']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php if ($workOrder['handling_category'] == HandlingTypeModel::CATEGORY_WAREHOUSE && $workOrder['status'] == WorkOrderModel::STATUS_COMPLETED): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Job Result</h3>
        </div>
        <div class="box-body">
            <?php $this->load->view('workorder/_data_detail') ?>
        </div>
    </div>
<?php endif; ?>

<?php $this->load->view('gate/_modal_check_in', ['category' => $workOrder['handling_category']]) ?>
<?php $this->load->view('gate/_modal_check_out', ['category' => $workOrder['handling_category']]) ?>
<?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>

<script src="<?= base_url('assets/app/js/work-order.js') ?>" defer></script>
