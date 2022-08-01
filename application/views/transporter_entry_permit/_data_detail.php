<?php if (empty($tep['checked_in_at']) && empty($tep['checked_in_description']) && empty($tepContainers) && empty($tepGoods) && ($tep['category'] == BookingTypeModel::CATEGORY_INBOUND || $tep['tep_category'] == BookingTypeModel::CATEGORY_INBOUND) ): ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Update Data</h3>
    </div>
    <form action="<?= site_url('transporter-entry-permit/update/' . $tep['id']) ?>?edit_type=security_tep" role="form" method="post" id="form-tep" enctype="multipart/form-data">
        <div class="box-body">
            <input type="hidden" name="id_booking" id="id_booking_security" value="<?= set_value('id_booking', $tep['id_booking']) ?>">
             <div id="field-eseal-tep" class="form-group <?= form_error('eseal') == '' ?: 'has-error'; ?>">
                <label for="eseal">E-seal</label>
                <select class="form-control select2" name="eseal" id="eseal-tep" <?= empty($tep['id_booking']) && empty($tep['id_customer']) ? '' : 'required';  ?> style="width: 100%" data-placeholder="Select available e-seal">
                    <option value=""></option>
                    <?php foreach ($eseals as $eseal): ?>
                        <option value="<?= $eseal['id'] ?>" <?= set_select('eseal', $eseal['id']) ?>>
                            <?= $eseal['no_eseal'] ?> - <?= $eseal['device_name'] ?> (ID <?= if_empty($eseal['id_device'], 'Not Connected') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">
                    Add more e-seal? <a href="<?= site_url('eseal/create') ?>" target="_blank">click here</a>. If your not found your eseal,
                    it may used in another truck (<a href="<?= site_url('eseal') ?>">find here</a>), try to security or gate out to release the e-seal.
                </span>
                <?= form_error('eseal', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php $this->load->view('tally/_tally_editor', [
                'inputSource' => 'STOCK',
                'withDetailContainer' => false,
                'withDetailGoods' => false,
                'allowIn' => true,
            ]) ?>
            <div class="box-footer clearfix">
                <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                    Back
                </a>
                <button type="submit" class="btn btn-primary pull-right" id="btn-safe-conduct-update">
                    Update TEP
                </button>
            </div>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>

<?php endif; ?>


<?php if( !empty($tepContainers) ): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Containers</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th class="text-center">Action</th>
                    <th>No Container</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Seal</th>
                    <th>Is Empty</th>
                    <th>Is Hold</th>
                    <th>Status</th>
                    <th>Danger</th>
                    <th>Description</th>
                    
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($tepContainers as $container): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
                                    <?php if($tep['category'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                                        <?php if( empty($tep['checked_in_at']) && $container['total_check_in'] <= 0 ): ?>
                                             <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                        <?php else: ?>
                                             <a class="btn btn-success" href="<?= site_url('transporter-entry-permit/view_checklist_in/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist Start
                                            </a>
                                        <?php endif; ?> 
                                    <?php else: ?>
                                        <?php if( !empty($tep['checked_in_at']) && empty($tep['checked_out_at']) && $container['total_check_out'] <= 0 ): ?>
                                            <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                        <?php else: ?>
                                            <a class="btn btn-success" href="<?= site_url('transporter-entry-permit/view_checklist_out/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist End
                                            </a>
                                        <?php endif; ?>   
                                    <?php endif; ?>    
                                <?php endif; ?>
                          </div>
                        </td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['type'] ?></td>
                        <td><?= $container['size'] ?></td>
                        <td><?= if_empty($container['seal'], '-') ?></td>
                        <td class="<?= $container['is_empty'] ? 'bg-red' :'' ?>">
                            <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                        </td>
                        <td class="<?= $container['is_hold'] ? 'bg-red' :'' ?>">
                            <?= $container['is_hold'] ? 'Yes' : 'No' ?>
                        </td>
                        <td><?= if_empty($container['status'], 'No Status') ?></td>
                        <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                            <?= if_empty($container['status_danger'], 'No Status') ?>
                        </td>
                        <td><?= if_empty($container['description'], 'No description') ?></td>
                    </tr>
                    <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                        <tr>
                            <td></td>
                            <td colspan="10">
                                <div class="table-responsive">
                                <table class="table table-condensed no-datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px">No</th>
                                        <th>Goods</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Weight (Kg)</th>
                                        <th>Gross (Kg)</th>
                                        <th>Length (M)</th>
                                        <th>Width (M)</th>
                                        <th>Height (M)</th>
                                        <th>Volume (M<sup>3</sup>)</th>
                                        <th>Is Hold</th>
                                        <th>Status</th>
                                        <th>Danger</th>
                                        <th>Description</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $innerNo = 1; ?>
                                    <?php foreach ($container['goods'] as $item): ?>
                                        <tr>
                                            <td><?= $innerNo++ ?></td>
                                            <td><?= $item['goods_name'] ?></td>
                                            <td><?= numerical($item['quantity'], 3, true) ?></td>
                                            <td><?= $item['unit'] ?></td>
                                            <td><?= numerical($item['tonnage'], 3, true) ?></td>
                                            <td><?= numerical($item['tonnage_gross'], 3, true) ?></td>
                                            <td><?= numerical($item['length'], 3, true) ?></td>
                                            <td><?= numerical($item['width'], 3, true) ?></td>
                                            <td><?= numerical($item['height'], 3, true) ?></td>
                                            <td><?= numerical($item['volume']) ?></td>
                                            <td><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                                            <td><?= if_empty($item['status'], 'No status') ?></td>
                                            <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                                            <td><?= if_empty($item['description'], 'No description') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if (empty($tepContainers)): ?>
                    <tr>
                        <td colspan="10" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($tepGoods)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Goods</h3>
            <div class="btn-group pull-right">
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
                    <?php if($tep['category'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                        <?php if( empty($tep['checked_in_at']) && $tep['total_check_in'] <= 0 ): ?>
                             <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $tep['tep_code'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                        <?php else: ?>
                             <a class="btn btn-success" href="<?= site_url('transporter-entry-permit/view_checklist_in_goods/' . $tep['id']) ?>">
                                <i class="fa ion-search mr10"></i>View Checklist Start
                            </a>
                        <?php endif; ?> 
                    <?php else: ?>
                        <?php if( !empty($tep['checked_in_at']) && empty($tep['checked_out_at']) && $tep_code['total_check_out'] <= 0 ): ?>
                            <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                        <?php else: ?>
                            <a class="btn btn-success" href="<?= site_url('transporter-entry-permit/view_checklist_out_goods/' . $tep['id']) ?>">
                                <i class="fa ion-search mr10"></i>View Checklist End
                            </a>
                        <?php endif; ?>   
                    <?php endif; ?>    
                <?php endif; ?>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable ">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Goods</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Weight (Kg)</th>
                    <th>Gross (Kg)</th>
                    <th>Length (M)</th>
                    <th>Width (M)</th>
                    <th>Height (M)</th>
                    <th>Volume (M<sup>3</sup>)</th>
                    <th>Is Hold</th>
                    <th>Status</th>
                    <th>Danger</th>
                    <th>Ex Container</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($tepGoods as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $item['goods_name'] ?></td>
                        <td><?= numerical($item['quantity'], 3, true) ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td><?= numerical($item['tonnage'], 3, true) ?></td>
                        <td><?= numerical($item['tonnage_gross'], 3, true) ?></td>
                        <td><?= numerical($item['length'], 3, true) ?></td>
                        <td><?= numerical($item['width'], 3, true) ?></td>
                        <td><?= numerical($item['height'], 3, true) ?></td>
                        <td><?= numerical($item['volume']) ?></td>
                        <td class="<?= $item['is_hold'] ? 'bg-red' :'' ?>">
                            <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                        </td>
                        <td><?= if_empty($item['status'], 'No status') ?></td>
                        <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                        <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                        <td><?= if_empty($item['description'], 'No description') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($tepGoods)): ?>
                    <tr>
                        <td colspan="16" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
            
        </div>
    </div>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
   <?php $this->load->view('security/_modal_security_checklist') ?>
<?php endif; ?>
<?php if (empty($tepContainers) && empty($tepGoods)): ?>

<script src="<?= base_url('assets/app/js/safe_conduct.js?v=19') ?>" defer></script>
<?php $this->load->view('security/_modal_notification') ?>
<?php else: ?><!-- karena btn-delete-file ada didalam safe_conduct.js -->
<script src="<?= base_url('assets/app/js/delete-file.js?v=1') ?>" defer></script>
<?php endif; ?>
