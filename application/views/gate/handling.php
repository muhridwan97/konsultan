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
        <h3 class="box-title">Gate Handling Detail</h3>
    </div>
    <div class="box-body">
        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>Handling Working Pass</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $handling['no_handling'] ?>">
                    <p class="lead" style="margin-top: 10px">No Handling: <?= $handling['no_handling'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Handling</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $handling['no_handling'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $handling['customer_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Type Handling</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $handling['handling_type'] ?> (<?= $handling['handling_category'] ?>)
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($handling['description'], 'No description') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Validated By</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($handling['validator_name'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Created At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= (new DateTime($handling['created_at']))->format('d F Y H:i') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Handling Date</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= (new DateTime($handling['handling_date']))->format('d F Y H:i') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $limitMaxHours = get_setting('max_time_job_after_approved', 8);

                            $handlingDate = new DateTime($handling['handling_date']);
                            $handlingDate->add(new DateInterval('PT' . $limitMaxHours . 'H'));
                            $now = new DateTime('now');
                            $diff = $handlingDate->diff($now);
                            $isPassed = $now <= $handlingDate;
                            ?>
                            <label class="col-sm-4 control-label">
                                Period<br>
                                <span class="label label-<?= !$isPassed ? 'danger' : 'success' ?>">
                                    <?= !$isPassed ? 'OVER SCHEDULE' : 'PASSED' ?>
                                </span>
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php
                                    if ((int)$diff->format('%a') > 0) {
                                        echo $diff->format('%a Day and ');
                                    }
                                    if ($isPassed) {
                                        echo $diff->format('%h hours remaining');
                                    } else {
                                        echo $diff->format('%h hours elapsed');
                                    }
                                    ?>
                                    <br>
                                    <span class="text-muted">Maximum <?= get_setting('max_time_job_after_approved', 8) ?>
                                        hours after handling plan date time.</span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Action</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_CREATE)): ?>
                                        <?php if(count($workOrders) < 1): ?>
                                            <?php if(isset($allowCreateJob) && $allowCreateJob): ?>
                                                <a href="<?= site_url('work-order/create') ?>"
                                                   class="btn btn-primary <?= $isPassed ? '' : 'disabled' ?>"
                                                   data-id-booking="<?= $handling['id_booking'] ?>"
                                                   data-id-handling="<?= $handling['id'] ?>"
                                                   data-id-safe-conduct=""
                                                   data-type="<?= $handling['handling_type'] ?>"
                                                   id="btn-create-job">
                                                    Create <?= $handling['handling_type'] ?> job
                                                </a>
                                            <?php else: ?>
                                                <span class="text-danger">
                                                    Check if you've created invoice or other conditions doesn't meet
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-primary disabled">
                                                Create <?= $handling['handling_type'] ?> job
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="form-control-static">Unauthorized to create job</p>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $this->load->view('gate/_job_data') ?>
<?php $this->load->view('gate/_modal_create_job') ?>
<?php $this->load->view('gate/_modal_check_in', ['category' => $handling['handling_category']]) ?>
<?php $this->load->view('gate/_modal_check_out', ['category' => $handling['handling_category']]) ?>
<?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>

<script src="<?= base_url('assets/app/js/work-order.js') ?>" defer></script>
