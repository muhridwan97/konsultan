<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Gate Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('gate/_scanner') ?>
    </div>
</div>

<div class="box box-primary security-wrapper">
    <div class="box-header with-border">
        <h3 class="box-title">Security Safe Conduct Detail</h3>
    </div>
    <div class="box-body">
        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>Safe Conduct Delivering Pass</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $safeConduct['no_safe_conduct'] ?>">
                    <p class="lead" style="margin-top: 10px">No Safe Conduct: <?= $safeConduct['no_safe_conduct'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-data"
                          data-id="<?= $safeConduct['id'] ?>"
                          data-driver="<?= $safeConduct['driver'] ?>"
                          data-no-police="<?= $safeConduct['no_police'] ?>"
                          data-expedition="<?= $safeConduct['expedition'] ?>"
                          data-label="<?= $safeConduct['no_safe_conduct'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Type Safe Conduct</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $safeConduct['type'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Booking</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($safeConduct['no_booking'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Police</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $safeConduct['no_police'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Driver</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $safeConduct['driver'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Expedition</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $safeConduct['expedition'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($safeConduct['description'], 'No Description') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Check In</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php if (empty($safeConduct['security_in_date'])): ?>
                                        <?php $isPassed = false ?>
                                        <span class="text-warning">Please security in first</span>
                                    <?php else: ?>
                                        <?php $isPassed = true ?>
                                        <?= (new DateTime($safeConduct['security_in_date']))->format('d F Y H:i') ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Check Out</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php if (empty($safeConduct['security_out_date'])): ?>
                                        <span class="text-warning">Please security out first</span>
                                    <?php else: ?>
                                        <?= (new DateTime($safeConduct['security_out_date']))->format('d F Y H:i') ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Create Job</label>
                            <div class="col-sm-8">
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_CREATE)): ?>
                                    <?php if(!$lockCreateJob): ?>
                                        <a href="<?= site_url('work-order/create/' . $safeConduct['type']) ?>"
                                           class="btn btn-primary <?= $isPassed && !$lockCreateJobStatus && !$lockCreateJobBySecurityEnd ? '' : 'disabled' ?>"
                                           data-id-booking=""
                                           data-id-handling=""
                                           data-id-safe-conduct="<?= $safeConduct['id'] ?>"
                                           data-type="<?= $safeConduct['type'] ?>"
                                           id="btn-create-job">
                                            Create <?= strtolower($safeConduct['type']) ?> job
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary disabled">
                                            Create <?= strtolower($safeConduct['type']) ?> job
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="form-control-static">Unauthorized to create job</p>
                                <?php endif; ?>
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
<?php $this->load->view('gate/_modal_check_in', ['category' => $safeConduct['type']]) ?>
<?php $this->load->view('gate/_modal_check_out', ['category' => $safeConduct['type']]) ?>
<?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>

<script src="<?= base_url('assets/app/js/work-order.js') ?>" defer></script>
