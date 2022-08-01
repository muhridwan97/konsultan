<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Branch</h3>
    </div>
    <div class="form-horizontal form-view">
        <div class="box-body">
            <div class="row mb10">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Branch Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $branch['branch'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Branch Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['branch_type'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Address</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['address'], 'No address') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">PIC</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($employee['name'], 'No PIC') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">CSO</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($cso['name'], 'No PIC') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Contact</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['contact'], 'No contact') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Email</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['email'], 'No email') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Warehouses</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if ($branch['total_warehouse'] == 0): ?>
                                    No warehouse
                                <?php else: ?>
                                    <a href="<?= site_url('branch/warehouse/' . $branch['id']) ?>">
                                        <?= numerical($branch['total_warehouse'], 0) ?> warehouses
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Cycle Count Day</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['cycle_count_day'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Cycle Count Goods</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['cycle_count_goods'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Cycle Count Container</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['cycle_count_container'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Max Time Request</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= empty($branch['max_time_request'])? 'no set time' :format_date($branch['max_time_request'], 'H:i') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Opname Day</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['opname_day'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Opname Day Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['opname_day_name'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Opname Pending Day</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['opname_pending_day'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Email Compliance</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['email_compliance'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Email Operational</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($branch['email_operational'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Email Support</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= values($branch['email_support'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Admin Support Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= values($branch['admin_support'], ' - ') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Dashboard Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $branch['dashboard_status'] == 1 ? 'Visible' : 'Not Visible' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Tally Check Approval</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $branch['tally_check_approval'] == 1 ? 'Active' : 'Inactive' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">QR Code Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $branch['qr_code_status'] == 1 ? 'Visible' : 'Not Visible' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(readable_date($branch['created_at']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($branch['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">KPI Inbound</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-3">DO</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $branch['kpi_inbound_do'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">SPPB</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $branch['kpi_inbound_sppb'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Gate In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $branch['kpi_inbound_gate_in'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Stripping</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $branch['kpi_inbound_stripping'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </div>
</div>