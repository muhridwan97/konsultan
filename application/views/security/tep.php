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
        <h3 class="box-title">Transporter Entry Permit</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>TEP Pass Code</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px"><?= get_setting('meta_url') ?></p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $tep['tep_code'] ?>">
                    <p class="lead" style="margin-top: 10px">No TEP: <?= $tep['tep_code'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-data" data-id="<?= $tep['id'] ?>" data-label="<?= $tep['customer_name_out'] ?> - <?= $tep['tep_code'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">TEP Category</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $tep['tep_category'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Expired At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= format_date($tep['expired_at'], 'd F Y H:i') ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Category</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <!-- <?= if_empty($tep['category'], '-') ?> -->
                                    <?php if(!empty($multiBookings)): ?>
                                        <?= if_empty($multiBookings[0]['category'], '-') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Booking</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <!-- <a href="<?= site_url('booking/view/' . $tep['id_booking']) ?>">
                                        <?= if_empty($tep['no_booking'], '-') ?>
                                    </a> -->
                                    
                            <?php if(!empty($multiBookings)):
                                foreach ($multiBookings as $booking):  ?>
                                    <a href="<?= site_url('booking/view/' . $booking['id_booking']) ?>">
                                    <?= if_empty($booking['no_booking'], '-');?>
                                    </a>
                                    </br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Reference</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <!-- <?= if_empty($tep['no_reference'],'-') ?> -->
                                    <?php if(!empty($multiBookings)):
                                foreach ($multiBookings as $booking):  ?>
                                    <?= if_empty($booking['no_reference'], '-');?>
                                    </br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php if (!empty($multiCustomer)) { 
                                        foreach ($multiCustomer as $customer) { ?>
                                            <?= if_empty($customer['name'], '-');?></br> 
                                        <?php }
                                    }?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($tep['description'], 'No Description') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Carrier</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($tep['receiver_name'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Vehicle</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($tep['receiver_vehicle'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Police</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($tep['receiver_no_police'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Contact</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($tep['receiver_contact'], '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Security Start</label>
                            <div class="col-sm-8">
                                <?php if(empty($tep['checked_in_at'])): ?>
                                    <p class="form-control-static">Security Start First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($tep['checked_in_at']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Security End</label>
                            <div class="col-sm-8">
                            <?php if(empty($tep['checked_out_at'])): ?>
                                <?php if(!empty($SafeConductByTEP)): ?>
                                    <p class="form-control-static">Security End By Safe Conduct</p>
                                <?php else: ?>
                                    <p class="form-control-static">Security End First</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="form-control-static">
                                    <?= (new DateTime($tep['checked_out_at']))->format('d F Y H:i') ?>
                                </p>
                            <?php endif; ?>
                            </div>
                        </div> <p></p>
                        <?php if ($tep['category'] == BookingTypeModel::CATEGORY_INBOUND || $tep['tep_category'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                            <p>
                            <?php if( (empty($tepContainers) && empty($tep['checked_in_at']) && (!empty($tep['checked_in_description']))) || (empty($tepContainers) && !empty($tep['checked_out_at'])) ): ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist Start</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($tep['checked_in_at']) && $tep['total_check_in'] <= 0): ?>  
                                             <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $tep['tep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                        <?php else: ?>
                                             <a href="<?= site_url('transporter-entry-permit/view_checklist_in_goods/' . $tep['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist Start</a>
                                        <?php endif; ?>    
                                    </div>
                                </div>
                            <?php endif; ?>
                            </p>
                            <?php if(empty($SafeConductByTEP) || $tep['total_check_out'] > 0): ?>  
                             <div class="form-group">
                                <label class="col-sm-4 control-label">Checklist End</label>
                                <div class="col-sm-8">
                                 <?php if (empty($tep['checked_in_at'])): ?>
                                    Security Start First
                                <?php else: ?>
                                    <?php if (!empty($tep['checked_in_at']) && $tep['total_check_out'] <= 0): ?>  
                                         <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $tep['tep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                    <?php else: ?>
                                        <a href="<?= site_url('transporter-entry-permit/view_checklist_out_goods/' . $tep['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Checklist Start</label>
                                <div class="col-sm-8">
                                <?php if( empty($tep['checked_in_at']) && $tep['total_check_in'] <= 0 ): ?>
                                     <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $tep['tep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                <?php else: ?>
                                     <a class="btn btn-success" href="<?= site_url('transporter-entry-permit/view_checklist_in_goods/' . $tep['id']) ?>">
                                        <i class="fa ion-search mr10"></i>View Checklist Start
                                    </a>
                                <?php endif; ?> 
                                </div>
                            </div>
                            <?php if(empty($tep['id_safe_conduct']) && !empty($tep['checked_in_at']) ): ?>
                            <p></p>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Checklist End</label>
                                <div class="col-sm-8">
                                <!-- <a data-id="<?= $tep['id'] ?>"  data-label="<?= $tep['tep_code'] ?>" href="<?= site_url('transporter-entry-permit/check-out-now/') ?>" class="btn btn-danger btn-check-out-now">  <i class="fa ion-search mr10"></i>Check Out Now</a> -->
                                <?php if (!empty($tep['checked_in_at']) && $tep['total_check_out'] <= 0): ?>  
                                         <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $tep['tep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                    <?php else: ?>
                                        <a href="<?= site_url('transporter-entry-permit/view_checklist_out_goods/' . $tep['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?> 
                    </form>
                </div>
            </div>
            <?php $this->load->view('transporter_entry_permit/_data_detail') ?>
        </section>
    </div>
</div>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('security/_modal_tep_check') ?>
<?php $this->load->view('security/_modal_tep_check_out') ?>
<?php $this->load->view('security/_modal_check_out_now') ?>
<?php 
    if((( empty($tep['checked_in_at']) && ($tep['category'] == BookingTypeModel::CATEGORY_OUTBOUND || $tep['category'] == "EMPTY CONTAINER") || 
          ($tep['tep_category'] == BookingTypeModel::CATEGORY_OUTBOUND || $tep['tep_category'] == "EMPTY CONTAINER") &&
          $tep['total_check_in'] > 0 ) ||
        ( empty($tep['checked_in_at']) && ($tep['category'] == BookingTypeModel::CATEGORY_INBOUND || $tep['tep_category'] == BookingTypeModel::CATEGORY_INBOUND) && (($allowCheckIn && (!empty($tepContainers) || !empty($tepContainers))) || 
            ($tep['total_check_in'] > 0 && empty($tepContainers) && empty($tepGoods))) ))):  ?>
<a href="<?= site_url('transporter-entry-permit/check_in/' . $tep['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-check-tep" data-id="<?= $tep['id'] ?>" data-label="<?= $tep['customer_name_in'] ?> - <?= $tep['tep_code'] ?>" style="display: none"></a>
<script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<?php endif; ?>

<?php if ($tep['expedition_type'] != 'INTERNAL' && (( ($tep['category'] == BookingTypeModel::CATEGORY_INBOUND || $tep['tep_category'] == BookingTypeModel::CATEGORY_INBOUND) && empty($tep['checked_out_at']) && $tep['total_check_out'] > 0)) && empty($SafeConductByTEP)): ?>
<a href="<?= site_url('transporter-entry-permit/check_out/' . $tep['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-check-tep-out" data-id="<?= $tep['id'] ?>" data-label="<?= $tep['customer_name_in'] ?> - <?= $tep['tep_code'] ?>" style="display: none"></a> 
<script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<?php endif; ?>

<?php if ( ($tep['total_check_out'] > 0) && empty($tep['checked_out_at']) && ($tep['tep_category'] == BookingTypeModel::CATEGORY_OUTBOUND || $tep['tep_category'] == "EMPTY CONTAINER")): ?>  
    <a data-id="<?= $tep['id'] ?>"  data-label="<?= $tep['tep_code'] ?>" href="<?= site_url('transporter-entry-permit/check-out-now/' . $tep['id']) ?>" class="btn btn-danger btn-check-out-now" style="display: none">  <i class="fa ion-search mr10"></i>Check Out Now</a>
    <script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/photo-scanner.js?v=2') ?>" defer></script>
<script src="<?= base_url('assets/app/js/delete-file.js?v=1') ?>" defer></script>
<?php $this->load->view('tally/_modal_take_photo') ?>