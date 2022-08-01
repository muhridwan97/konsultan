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
        <h3 class="box-title">Security Safe Conduct Detail</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

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
                            <label class="col-sm-4 control-label">Published At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= (new DateTime($safeConduct['created_at']))->format('d F Y H:i') ?>
                                </p>
                            </div>
                        </div>
                        <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Security Start</label> <!-- internal start -->
                                <div class="col-sm-8">
                                <?php if(empty($safeConduct['security_in_date'])): ?>
                                        <p class="form-control-static">Security Start First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($safeConduct['security_in_date']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                               </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Security End</label> <!-- internal stop -->
                                <div class="col-sm-8">
                                <?php if(empty($safeConduct['security_out_date'])): ?>
                                    <p class="form-control-static">Security End First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= format_date($safeConduct['security_out_date'], 'd F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                               </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Security Start</label> <!-- External start -->
                                <div class="col-sm-8">
                                <?php if(empty($safeConduct['security_in_date'])): ?>
                                        <p class="form-control-static">Security Start First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= (new DateTime($safeConduct['security_in_date']))->format('d F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                               </div>
                            </div>
                            <div class="form-group">
                            <label class="col-sm-4 control-label">Security End</label> <!-- External stop -->
                            <div class="col-sm-8">
                                <?php if(empty($safeConduct['security_out_date'])): ?>
                                    <p class="form-control-static">Security End First</p>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= format_date($safeConduct['security_out_date'], 'd F Y H:i') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                            <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                <?php if(count($allSafeConducts) == 1 || empty(array_filter($allSafeConducts, function($sf) { return !empty($sf['containers']); }))): // related safe conduct group if contain container, then use the other (check list container) ?>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Checklist Start</label>
                                        <div class="col-sm-8">
                                            <?php if (empty($safeConduct['security_in_date']) && $safeConduct['total_check_in'] <= 0): ?>
                                                 <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                            <?php else: ?>
                                                <a href="<?= site_url('safe_conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist Start</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif (empty($safeConduct['security_in_date']) && $safeConduct['total_check_in'] > 0): ?>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Checklist Start</label>
                                        <div class="col-sm-8">
                                            <a href="<?= site_url('safe_conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>" class="btn btn-success">
                                                <i class="fa ion-search mr10"></i>View Checklist Start
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($safeConduct['id_eseal']) && empty($safeConductContainers) && empty($safeConductGoods)): ?>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Checklist End</label>
                                        <div class="col-sm-8">
                                            <?php if (!empty($safeConduct['security_in_date']) && $safeConduct['total_check_out'] <= 0): ?>
                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                            <?php else: ?>
                                                <?php if (empty($safeConduct['security_in_date'])): ?>
                                                    Security Start First
                                                <?php else: ?>
                                                    <a href="<?= site_url('safe_conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if( (empty($safeConductContainers) && empty($safeConductGoods)) || $safeConduct['total_check_in'] > 0): ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist Start</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($safeConduct['security_in_date']) && $safeConduct['total_check_in'] <= 0): ?>  
                                             <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                        <?php else: ?>
                                            <a href="<?= site_url('safe_conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist Start</a>
                                        <?php endif; ?>
                                    </div>
                                </div><p></p>
                                 <?php endif; ?>
                                 <?php if($allowCheckIn): ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist End</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($safeConduct['tep_out_date']) && empty($safeConduct['security_out_date'])): ?>
                                            <?php if ($safeConduct['total_check_out'] <= 0): ?>
                                            <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                            <?php else: ?>
                                                <a href="<?= site_url('safe_conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                           <?php if (!empty($safeConduct['tep_out_date']) && empty($safeConduct['security_out_date'])): ?>  
                                                Checklist End
                                            <?php else: ?>
                                                <a href="<?= site_url('safe_conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                            <?php endif; ?>   
                                        <?php endif; ?>
                                    </div>
                                </div> 
                                 <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist End</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($safeConduct['security_in_date'])): ?>
                                            Security Start First
                                        <?php else: ?>
                                            <?php if (!empty($safeConduct['security_in_date']) && $safeConduct['total_check_out'] <= 0): ?>  
                                                 <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                            <?php else: ?>
                                                <a href="<?= site_url('safe_conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist End</a>
                                            <?php endif; ?>    
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Checklist Start</label>
                                    <div class="col-sm-8">
                                        <?php if (empty($safeConduct['security_in_date']) && $safeConduct['total_check_in'] <= 0): ?>  
                                             <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                        <?php else: ?>
                                            <a href="<?= site_url('safe_conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>" class="btn btn-success">  <i class="fa ion-search mr10"></i>View Checklist Start</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>    
                        <?php endif; ?> 
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php if(!empty($allSafeConducts) && count($allSafeConducts) > 1): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Related Safe Conduct</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable" id="table-work-order">
                <thead>
                <tr>
                    <th style="width: 30px">No</th>
                    <th>No Safe Conduct</th>
                    <th>No Booking</th>
                    <th>Customer</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($allSafeConducts as $index => $relatedSafeConduct): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= site_url('safe-conduct/view/' . $relatedSafeConduct['id']) ?>">
                                <?= $relatedSafeConduct['no_safe_conduct'] ?>
                            </a>
                            <?= $relatedSafeConduct['id'] == $safeConduct['id'] ? '(current)' : '' ?>
                        </td>
                        <td>
                            <a href="<?= site_url('booking/view/' . $relatedSafeConduct['id_booking']) ?>">
                                <?= $relatedSafeConduct['no_booking'] ?>
                            </a>
                            <small class="text-muted"><?= $relatedSafeConduct['no_reference'] ?></small>
                        </td>
                        <td><?= $relatedSafeConduct['customer_name'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php foreach ($allSafeConducts as $allSafeConduct) {
    // TODO: update safe conduct group
    if(empty($allSafeConduct['id_eseal']) && $allSafeConduct['type'] == 'INBOUND' && !empty($allSafeConduct['security_in_date'])) {
        $this->load->view('safe_conduct/_edit_eseal', ['safeConduct' => $allSafeConduct, 'safeConductContainers' => $allSafeConduct['containers'], 'safeConductGoods' => $allSafeConduct['goods']]);
    } else {
        $this->load->view('safe_conduct/_data_detail', ['safeConduct' => $allSafeConduct, 'safeConductContainers' => $allSafeConduct['containers'], 'safeConductGoods' => $allSafeConduct['goods']]);
    }
} ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
    <?php $this->load->view('security/_modal_security_checklist') ?>
<?php endif; ?>
<!-- biar gak redudance safe_conduct.js dgn safe_conduct/_data_detail  -->
<?php foreach ($allSafeConducts as $safeConduct) : ?>
    <?php if(empty($allSafeConduct['id_eseal']) && $allSafeConduct['type'] == 'INBOUND' && !empty($allSafeConduct['security_in_date'])) : ?>
    <script src="<?= base_url('assets/app/js/safe_conduct.js?v=19') ?>" defer></script>
    <?php endif; ?>
<?php endforeach; ?>

<?php $this->load->view('security/_modal_notification') ?>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('tally/_modal_select_position') ?>

<?php $this->load->view('security/_modal_check_in', ['category' => $safeConduct['type']]) ?>
<?php $this->load->view('security/_modal_check_out', ['category' => $safeConduct['type']]) ?>

<?php $checkInReady = true; $checkOutReady = true;?>
<?php foreach ($allSafeConducts as $safeConduct) {
    if (($safeConduct['expedition_type'] == 'INTERNAL'
            && $safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND
            && empty($safeConduct['security_in_date'])
            && ($safeConduct['total_check_in'] > 0 || $allowCheckIn))

        || ($safeConduct['expedition_type'] == 'INTERNAL'
            && $safeConduct['type'] == BookingTypeModel::CATEGORY_OUTBOUND
            && empty($safeConduct['security_in_date']) && $allowCheckIn)

        || ($safeConduct['expedition_type'] != 'INTERNAL'
            && empty($safeConduct['security_in_date'])
            && !empty($safeConduct['tep_in_date']))) {
        // this safe conduct ok
    } else {
        $checkInReady = false;
        break;
    }
} ?>
<?php foreach ($allSafeConducts as $safeConduct) {
    if( ($safeConduct['expedition_type'] == 'INTERNAL'
        && $safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND
        && !empty($safeConduct['security_in_date'])
        && empty($safeConduct['security_out_date'])
        && $allowCheckOut)

    || ($safeConduct['expedition_type'] == 'INTERNAL'
        && $safeConduct['type'] == BookingTypeModel::CATEGORY_OUTBOUND
        && !empty($safeConduct['security_in_date'])
        && empty($safeConduct['security_out_date'])
        && ($safeConduct['total_check_out'] > 0 || $allowCheckOut))

    || ($safeConduct['expedition_type'] != 'INTERNAL'
        && $safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND
        && empty($safeConduct['security_out_date'])
        && $allowCheckOut)

    || ($safeConduct['expedition_type'] != 'INTERNAL'
        && $safeConduct['type'] == BookingTypeModel::CATEGORY_OUTBOUND
        && !empty($safeConduct['security_in_date'])
        && empty($safeConduct['security_out_date'])
        && $allowCheckOut)

    || (empty($safeConduct['security_out_date'])
        && $safeConduct['total_check_out'] > 0
        && empty($safeConductContainers)
        && empty($safeConductGoods)) ) {
        // this safe conduct ok
    } else {
        $checkOutReady = false;
        break;
    }
} ?>
<?php $safeConduct = reset($allSafeConducts) ?>
<?php if($checkInReady):?>
    <a href="<?= site_url('safe-conduct/check_in/' . $safeConduct['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
       data-id="<?= $safeConduct['id'] ?>" data-driver="<?= $safeConduct['driver'] ?>" data-no-police="<?= $safeConduct['no_police'] ?>" data-expedition="<?= $safeConduct['expedition'] ?>" data-label="<?= $safeConduct['no_safe_conduct'] ?>" class="btn btn-danger btn-check-in" style="display: none"></a>
    <script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<?php endif; ?>

<?php if($checkOutReady):?>
    <a href="<?= site_url('safe-conduct/check_out/' . $safeConduct['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" data-id="<?= $safeConduct['id'] ?>" data-label="<?= $safeConduct['no_safe_conduct'] ?>" class="btn btn-danger btn-check-out" style="display: none"></a>
    <script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/photo-scanner.js?v=2') ?>" defer></script>
<script src="<?= base_url('assets/app/js/delete-file.js?v=1') ?>" defer></script>

<?php $this->load->view('tally/_modal_take_photo') ?>