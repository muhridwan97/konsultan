<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Security Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('security/_scanner') ?>
    </div>
</div>

<div class="box box-primary security-wrapper">
    <div class="box-header with-border">
        <h3 class="box-title">Heavy Equipment Entry Permit</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>HEEP Pass Code</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px"><?= get_setting('meta_url') ?></p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $heep['heep_code'] ?>">
                    <p class="lead" style="margin-top: 10px">No HEEP: <?= $heep['heep_code'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-data" data-id="<?= $heep['id'] ?>" data-label="<?= $heep['customer_name'] ?> - <?= $heep['heep_code'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($heep['customer_name'], '-');?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Expired At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= format_date($heep['expired_at'], 'd F Y H:i') ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">No HEEP</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($heep['no_heep'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label">No Reference</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($heep['no_reference'],'-') ?>
                                </p>
                            </div>
                        </div> -->
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($heep['description'], 'No Description') ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Security Start</label>
                            <div class="col-sm-8">
                                <?php if(empty($heep['checked_in_at'])): ?>
                                    <p class="form-control-static">Security Start First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($heep['checked_in_at']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Security End</label>
                            <div class="col-sm-8">
                            <?php if(empty($heep['checked_out_at'])): ?>
                                <p class="form-control-static">Security End First</p>
                            <?php else: ?>
                                <p class="form-control-static">
                                    <?= (new DateTime($heep['checked_out_at']))->format('d F Y H:i') ?>
                                </p>
                            <?php endif; ?>
                            </div>
                        </div> <p></p>
                            <p>
                            <?php if( empty($heep['checked_in_at'])): ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist Start</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($heep['checked_in_at'])): ?>  
                                             <a href="<?= site_url('heavy-equipment-entry-permit/check-in/'.$heep['id']. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-check-heep" date-id="<?= $heep['heep_code'] ?>" data-label="<?= $heep['heep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                        <?php else: ?>
                                             <a href="<?= site_url('transporter-entry-permit/view_checklist_in_goods/' . $heep['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist Start</a>
                                        <?php endif; ?>    
                                    </div>
                                </div>
                            <?php endif; ?>
                            </p>
                            
                            <?php if( empty($heep['checked_out_at'])): ?>
                                <div class="form-group">
                                <label class="col-sm-4 control-label">Checklist End</label>
                                <div class="col-sm-8">
                                    <?php if (empty($heep['checked_in_at'])): ?>
                                    Security Start First
                                <?php else: ?>
                                    <?php if (!empty($heep['checked_in_at'])): ?>  
                                            <a href="<?= site_url('heavy-equipment-entry-permit/check-out/'.$heep['id']. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-check-out-heep" date-id="<?= $heep['heep_code'] ?>" data-label="<?= $heep['heep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                    <?php else: ?>
                                        <a href="<?= site_url('transporter-entry-permit/view_checklist_out_goods/' . $heep['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $this->load->view('security/_modal_heep_check') ?>
<?php $this->load->view('security/_modal_heep_check_out') ?>
<script src="<?= base_url('assets/app/js/photo-scanner.js?v=2') ?>" defer></script>
<script src="<?= base_url('assets/app/js/delete-file.js?v=1') ?>" defer></script>

<?php $this->load->view('tally/_modal_take_photo') ?>