<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View TEP</h3>
    </div>
    <div class="box-body">
        <div class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">TEP Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['tep_category'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(!empty($multiBookings)): 
                                    foreach ($multiBookings as $booking):  ?>
                                        <?= if_empty($booking['no_booking'], '-');?></br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking Category</label>
                        <div class="col-sm-9">
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
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                            <!-- <?= if_empty($tep['no_reference'], 'No PIC') ?> -->
                            <?php if(!empty($multiBookings)):
                                foreach ($multiBookings as $booking):  ?>
                                    <?= if_empty($booking['no_reference'], '-');?></br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Code</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <strong><?= if_empty($tep['tep_code'], '-') ?></strong>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Expired At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['expired_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Checked In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['checked_in_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Checked Out</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($tep['checked_out_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Vehicle</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_vehicle'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Police</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_no_police'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Aju</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (!empty($tep['id_upload'])) : ?>
                                    <a href="<?= site_url('upload/view/' . $tep['id_upload']) ?>">
                                        <?= if_empty($tep['no_aju'], '-') ?></a>
                                <?php else :
                                    $id_upload_array = explode(",",$tep['id_upload_multi']);
                                    $no_aju_array = explode(",",$tep['no_aju_multi']);?>
                                    <?php foreach ($id_upload_array as $key => $id_upload) : ?>
                                    <a href="<?= site_url('upload/view/' . $id_upload) ?>">
                                        <?= if_empty($no_aju_array[$key], '-') ?></a> </br>
                                <?php endforeach ?>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
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
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Check In Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['checked_in_description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Check Out Desc</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['checked_out_description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Email</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_email'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Contact</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['receiver_contact'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Safe Conduct</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(empty($tep['no_safe_conduct'])): ?>
                                    <?php if(empty($tep['no_safe_conduct_tep2'])): ?>
                                    -
                                    <?php else: ?>
                                        <?= str_replace(',', '<br>', $tep['no_safe_conduct_tep2']) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= str_replace(',', '<br>', $tep['no_safe_conduct']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($tep['creator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($tep['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('transporter-entry-permit/view/' . $tep['id_tep_reference']) ?>">
                                    <?= if_empty($tep['tep_code_reference'], '-') ?></a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Queue time</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($tep['queue_time'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Request</label>
                        <div class="col-sm-9">
                            <div class="form-control-static">
                                <?php if(empty($tepRequests)): ?>
                                    -
                                <?php else: ?>
                                    <?php foreach ($tepRequests as $tepRequest): ?>
                                        <p><?= $tepRequest['no_request'] ?></p>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Request DO/Memo</label>
                        <div class="col-sm-9">
                            <div class="form-control-static">
                                <?php if(empty($tepRequests)): ?>
                                    -
                                <?php else: ?>
                                    <?php foreach ($tepRequests as $tepRequest): ?>
                                <button class="btn btn-danger btn-view-file" data-id = "<?= $tepRequest['id'] ?>">
                                    <i class="fa fa-folder"></i>
                                </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TEP Request Express Service</label>
                        <div class="col-sm-9">
                            <div class="form-control-static">
                                <?php if(empty($tepRequests)): ?>
                                    -
                                <?php else: ?>
                                    <?php foreach ($tepRequests as $tepRequest): ?>
                                <button class="btn btn-danger btn-view-express-file" data-id = "<?= $tepRequest['id'] ?>">
                                    <i class="fa fa-folder"></i>
                                </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!empty($linkedTep)): ?>
            <div class="box box-danger form-horizontal form-view">
                <div class="box-header with-border">
                    <h3 class="box-title">Linked TEP</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3">Branch</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">
                                        <?= if_empty($linkedTep['branch'], '-') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">TEP Code</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">
                                        <a href="<?= site_url('p/' . $linkedTep['id_branch'] . '/transporter-entry-permit/view/' . $linkedTep['id'], false) ?>">
                                            <?= if_empty($linkedTep['tep_code'], '-') ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><span class="hidden-xs">List </span>Goods</h3>
            </div>
            <div class="box-body">
                <div class="table-editor-wrapper">
                    <div class="table-responsive">
                        <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods" >
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>No Reference</th>
                                <th>No Invoice</th>
                                <th>No BL</th>
                                <th>No Goods</th>
                                <th>Goods Name</th>
                                <th>Whey Number</th>
                                <th>Unit</th>
                                <th>Stock Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($listGoods)): ?>
                                <?php foreach ($listGoods as $key => $goods) :?>
                                <tr>
                                    <td>
                                        <?=$key+1 ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['no_reference'], '-') ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['no_invoice'], '-') ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['no_bl'], '-') ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['no_goods'], '-') ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['goods_name'], '-') ?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['whey_number'], '-')?>
                                    </td>
                                    <td>
                                        <?= if_empty($goods['unit'], '-') ?>
                                    </td>
                                    <td>
                                        <?= numerical($goods['quantity'], 3, true) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="row-placeholder">
                                <td colspan="9">No goods data</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Edit History</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Edit At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($tepHistories as $history): ?>
                        <tr>
                            <td class="responsive-hide"><?= $no++ ?></td>
                            <td class="responsive-title">
                                <?= values($history['author_name'], '-') ?>
                            </td>
                            <td><?= values($history['description'], '-') ?></td>
                            <td><?= readable_date($history['created_at']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if(empty($tepHistories)): ?>
                        <tr>
                            <td colspan="5">No history available</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if(!empty($tepChecklistsIn)||!empty($tepChecklistsOut)): ?>
        <div class="table-responsive">
            <?php $this->load->view('transporter_entry_permit/_data_photos') ?>
        </div>
    <?php endif ?>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>


<?php $this->load->view('transporter_entry_permit/_modal_view_file') ?>
<?php $this->load->view('transporter_entry_permit/_modal_view_express_file') ?>
<script src="<?= base_url('assets/app/js/tep.js?v=6') ?>" defer></script>