<form role="form" class="form-horizontal form-view">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">View Heavy Equipment Entry Permit</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No HEEP</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($heep['no_heep'], 'No Number') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">HEEP Code</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <strong><?= if_empty($heep['heep_code'], '-') ?></strong>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Expired At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($heep['expired_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Checked In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($heep['checked_in_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Requisition</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($heep['no_requisition'], '-')?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                            <?= if_empty($heep['customer_name'], '-');?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($heep['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Check In Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($heep['checked_in_description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($heep['creator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($heep['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label class="col-sm-3">HEEP Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('transporter-entry-permit/view/' . $heep['id_tep_reference']) ?>">
                                <?= if_empty($heep['tep_code_reference'], '-') ?></a>
                            </p>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <?php if(!empty($heep['photo_in'])||!empty($heep['photo_out'])): ?>
            <div class="table-responsive">
                <?php $this->load->view('heavy_equipment_entry_permit/_data_photos') ?>
            </div>
        <?php endif ?>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </div>
</form>