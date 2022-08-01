<form role="form" class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">No Safe Conduct</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['no_safe_conduct'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Type</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['type'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Booking</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('booking/view/' . $safeConduct['id_booking']) ?>">
                            <?= if_empty($safeConduct['no_booking'], 'No booking') ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">No Reference</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['no_reference']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">No Handling</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('handling/view/' . $safeConduct['id_handling']) ?>">
                            <?= $safeConduct['no_handling'] ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Warehouse of Origin</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['source_warehouse'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">E-seal</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['no_eseal'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Tracking Status</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('report/eseal-route/' . $safeConduct['id']) ?>">
                            <?= if_empty($safeConduct['status_tracking'], '-') ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Vehicle Type</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['vehicle_type'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">No Police</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['no_police'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Driver</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['driver'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Expedition</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['expedition'] ?> (<?= $safeConduct['expedition_type'] ?>)
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Source</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $safeConduct['source'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">TEP Code</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php if(!empty($safeConduct['tep_code'])): ?>
                            <a href="<?= site_url('transporter-entry-permit/view/' . $safeConduct['id_transporter_entry_permit']) ?>">
                                <?= if_empty($safeConduct['tep_code'], '-') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">TEP Check In</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($safeConduct['tep_in_date']) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">Safe Conduct Group</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php if(empty($safeConduct['no_safe_conduct_group'])): ?>
                            -
                        <?php else: ?>
                            <a href="<?= site_url('safe-conduct-group/view/' . $safeConduct['id_safe_conduct_group']) ?>">
                                <?= $safeConduct['no_safe_conduct_group'] ?>
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php if($safeConduct['total_check_in'] > 0): ?>
            <div class="form-group">
                <label class="col-sm-4">Checklist Start</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('safe-conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>">
                            View Checklist Start
                        </a>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            <?php if($safeConduct['total_check_out'] > 0): ?>
            <div class="form-group">
                <label class="col-sm-4">Checklist End</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('safe-conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>">
                            View Checklist End
                        </a>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="col-sm-4">Description</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['description'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Gate Out TPS</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty(format_date($safeConduct['tps_gate_out_date'], 'd F Y'), '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Check In</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= empty($safeConduct['security_in_date']) ? '-' : readable_date($safeConduct['security_in_date']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Check Out</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= empty($safeConduct['security_out_date']) ? '-' : readable_date($safeConduct['security_out_date']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Service Time</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['service_time'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Check In Remark</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['security_in_description'], 'No check in remark') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Check Out Remark</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['security_out_description'], 'No check out remark') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Attachment</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php if (empty($safeConduct['attachment'])): ?>
                            No attachment
                        <?php else: ?>
                            <a href="<?= base_url('uploads/safe_conducts/' . $safeConduct['attachment']) ?>">
                                Download Safe Conduct
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Photo</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php if (empty($safeConduct['photo'])): ?>
                            No attachment
                        <?php else: ?>
                            <?php $safeConduct['photo'] = explode('/', $safeConduct['photo']); ?>
                            <?php foreach ($safeConduct['photo'] as $photo): ?>
                                <a href="<?= base_url('uploads/safe_conducts_photo/' . $photo) ?>">
                                    <?= $photo ?>
                                </a> <br>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">CY Date</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($safeConduct['cy_date']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($safeConduct['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Updated At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($safeConduct['updated_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created By</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($safeConduct['creator_name'], 'Unknown') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
