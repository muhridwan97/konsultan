<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Put Away Audit</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Branch</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $putAway['branch'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($putAway['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Put Away Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($putAway['put_away_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="<?= site_url('put-away/save_process/' . $putAway['id']) ?>" method="post" enctype="multipart/form-data" id="form-process-put-away">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-datatable">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 20px">No</th>
                                <th class="text-center">Owner Name</th>
                                <th class="text-center">No Booking</th>
                                <th class="text-center">No Goods</th>
                                <th class="text-center">Goods Name</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Pallet Marking Check</th>
                                <th class="text-center">Type Check</th>
                                <th class="text-center">Position Check</th>
                                <th class="text-center">Quantity Check</th>
                                <th class="text-center">Photo Check</th>
                                <th class="text-center">Description Check</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php if ($putAway['status'] == PutAwayModel::STATUS_PENDING): ?>
                                <?php foreach ($putAwayDetails as $stock): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $stock['name'] ?></td>
                                        <td class="text-center">
                                            <?= $stock['no_booking'] ?>
                                            <small><?= "(" . $stock['no_reference'] . ")" ?></small>
                                        </td>
                                        <td class="text-center"><?= $stock['no_goods'] ?></td>
                                        <td class="text-center"><?= $stock['goods_name'] ?></td>
                                        <td class="text-center"><?= $stock['unit'] ?></td>
                                        <td class="text-center">
                                            <?= if_empty($stock['no_pallet'], ' - ') ?>
                                            <select class="form-control select2" required name="no_pallet[]" id="put_away_no_pallet"
                                                    data-placeholder="Check No Pallet" style="width: 100%" required>
                                                <option value=""></option>
                                                <option value="1">BENAR</option>
                                                <option value="0">SALAH</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <?= if_empty($stock['type_goods'], ' - ') ?>
                                            <select class="form-control select2" required name="type_goods[]" id="put_away_type_goods"
                                                    data-placeholder="Check Type" style="width: 100%" required>
                                                <option value=""></option>
                                                <option value="1">BENAR</option>
                                                <option value="0">SALAH</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <?= if_empty($stock['position'], ' - ') ?>
                                            <small class="text-muted" style="display: block">
                                            <?= if_empty($stock['position_block'], ' - ') ?>
                                            </small>
                                            <select class="form-control select2" required name="position[]" id="put_away_potision"
                                                    data-placeholder="Check Position" style="width: 100%" required>
                                                <option value=""></option>
                                                <option value="1">BENAR</option>
                                                <option value="0">SALAH</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <?= if_empty(numerical($stock['quantity'], 3, true), ' - ') ?>
                                            <select class="form-control select2" required name="quantity[]" id="put_away_quantity"
                                                    data-placeholder="Check Quantity" style="width: 100%" required>
                                                <option value=""></option>
                                                <option value="1">BENAR</option>
                                                <option value="0">SALAH</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm <?= ($stock['count_photo']>0) ? 'btn-info' : 'btn-warning' ?> btn-photo-goods" type="button">
                                                <i class="fa ion-image"></i>
                                            </button>
                                            <input type="hidden" name='photo[]' id="temp_photos" value="">
                                            <input type="hidden" name='photo_description[]' id="temp_photo_descriptions" value="">
                                            <input type="hidden" name='workOrderGoodsId[]' id="workOrderGoodsId" value="<?= $stock['id_work_order_goods'] ?>">
                                        </td>
                                        <td class="text-center">
                                            <textarea class="form-control-static"
                                                        name="description[]"></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php if ($putAway['status'] == PutAwayModel::STATUS_PROCESSED || $putAway['status'] == PutAwayModel::STATUS_REOPENED): ?>
                                    <?php foreach ($putAwayDetails as $stock): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $stock['name'] ?></td>
                                            <td class="text-center">
                                                <?= $stock['no_booking'] ?>
                                                <small><?= "(" . $stock['no_reference'] . ")" ?></small>
                                            </td>
                                            <td class="text-center"><?= $stock['no_goods'] ?></td>
                                            <td class="text-center"><?= $stock['goods_name'] ?></td>
                                            <td class="text-center"><?= $stock['unit'] ?></td>
                                            <td class="text-center">
                                                <?= if_empty($stock['no_pallet'], ' - ') ?>
                                                <select class="form-control select2" required name="no_pallet[]" id="put_away_no_pallet"
                                                        data-placeholder="Check No Pallet" style="width: 100%" required>
                                                    <option value=""></option>
                                                    <option value="1" <?= set_select('pallet_marking','1', $stock['pallet_marking_check'] == 1) ?>>BENAR</option>
                                                    <option value="0" <?= set_select('pallet_marking','0', $stock['pallet_marking_check'] == 0) ?>>SALAH</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <?= if_empty($stock['type_goods'], ' - ') ?>
                                                <select class="form-control select2" required name="type_goods[]" id="put_away_type_goods"
                                                        data-placeholder="Check Type" style="width: 100%" required>
                                                    <option value=""></option>
                                                    <option value="1" <?= set_select('type_goods','1', $stock['type_goods_check'] == 1) ?>>BENAR</option>
                                                    <option value="0" <?= set_select('type_goods','0', $stock['type_goods_check'] == 0) ?>>SALAH</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <?= if_empty($stock['position'], ' - ') ?>
                                                <small class="text-muted" style="display: block">
                                                <?= if_empty($stock['position_block'], ' - ') ?>
                                                </small>
                                                <select class="form-control select2" required name="position[]" id="put_away_potision"
                                                        data-placeholder="Check Position" style="width: 100%" required>
                                                    <option value=""></option>
                                                    <option value="1" <?= set_select('position','1', $stock['position_check'] == 1) ?>>BENAR</option>
                                                    <option value="0" <?= set_select('position','0', $stock['position_check'] == 0) ?>>SALAH</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <?= if_empty(numerical($stock['quantity'], 3, true), ' - ') ?>
                                                <select class="form-control select2" required name="quantity[]" id="put_away_quantity"
                                                        data-placeholder="Check Quantity" style="width: 100%" required>
                                                    <option value=""></option>
                                                    <option value="1" <?= set_select('quantity','1', $stock['quantity_check'] == 1) ?>>BENAR</option>
                                                    <option value="0" <?= set_select('quantity','0', $stock['quantity_check'] == 0) ?>>SALAH</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm <?= ($stock['count_photo']>0) ? 'btn-info' : 'btn-warning' ?> btn-photo-goods" type="button">
                                                    <i class="fa ion-image"></i>
                                                </button>
                                                <input type="hidden" name='photo[]' id="temp_photos" value="">
                                                <input type="hidden" name='photo_description[]' id="temp_photo_descriptions" value="">
                                                <input type="hidden" name='workOrderGoodsId[]' id="workOrderGoodsId" value="<?= $stock['id_work_order_goods'] ?>">
                                            </td>
                                            <td class="text-center">
                                                <textarea class="form-control-static"
                                                            name="description[]"><?= set_value('description', $stock['description_check']) ?></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (empty($putAwayDetails)): ?>
                                <tr>
                                    <td colspan="10" class="text-center"><?= "No Goods" ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($putAway['status'] == PutAwayModel::STATUS_PENDING && (!empty($putAwayDetails))): ?>
                        <div class="form-group">
                            <label for="Photo">Photo</label>
                            <input type="file" name="photo" id="photo"
                                    accept="image/*"
                                    placeholder="Photo">
                            <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                        </div>
                    <?php else: ?>
                        <?php if (($putAway['status'] == PutAwayModel::STATUS_PROCESSED || $putAway['status'] == PutAwayModel::STATUS_REOPENED) && (!empty($putAwayDetails))): ?>
                            <div class="form-group">
                                <label for="Photo">Old Photo</label>
                                <p class="form-control-static">
                                    <a href="<?= asset_url(urlencode($putAway['photo'])) ?>">
                                        <?= $putAway['photo'] ?>
                                    </a>
                                </p>
                            </div>
                            <div class="form-group">
                                <label for="Photo">Replace With Photo</label>
                                <input type="file" name="photo" id="photo"
                                        accept="image/*"
                                        placeholder="Photo">
                                <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="box-footer">
                    <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                    <button type="submit" class="btn btn-success pull-right">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->load->view('put_away/_modal_photo_editor') ?>
<?php $this->load->view('tally/_modal_take_photo') ?>
<?php $this->load->view('template/_modal_confirm'); ?>
<script src="<?= base_url('assets/app/js/put_away.js?v=1') ?>" defer></script>
<script src="<?= base_url('assets/app/js/photo-scanner.js?v=1') ?>" defer></script>