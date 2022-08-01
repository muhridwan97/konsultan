<div class="box box-primary">
    <div class="box-header with-border">
        <?php if($opname['opname_type'] == "GOODS"): ?>
            <h3 class="box-title">Opname Goods</h3>
        <?php else: ?>
            <h3 class="box-title">Opname Container</h3>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Opname Date</label>
                        <div class="col-sm-9">
                            <p><strong><?= format_date($opname['opname_date'], 'd F Y') ?> </strong></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Requested By</label>
                        <div class="col-sm-9">
                             <p><strong><?= if_empty($opname['requested_by'], '-') ?></strong></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p><strong><?= if_empty($opname['description'], ' No Description') ?></strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                             <p class="<?= $opname['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><strong><?= $opname['status'] ?></strong></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3">Validated By</label>
                        <div class="col-sm-9">
                             <p><strong><?= if_empty($opname['validated_by'], '-') ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if($opname['opname_type'] == "GOODS"): ?>
            <form action="<?= site_url('opname/save_process/' . $opname['id']) ?>" role="form" method="post" enctype="multipart/form-data">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Opname Data</h3>
                        <div class="pull-right">
                            <a href="<?= site_url('opname/download-opname/' . $opname['id']) ?>" class="btn btn-primary">
                                <i class="fa fa-download mr10"></i>Download
                            </a>
                            <a href="<?= base_url('uploads/opname_file/' . urlencode($opname['file_check'])) ?>" class="btn btn-primary">
                                <i class="fa fa-download mr10"></i>Download Old File
                            </a>
                            <a href="<?= site_url('opname/upload-opname/' . $opname['id']) ?>" class="btn btn-success">
                                <i class="fa fa-upload mr10"></i> Upload Result
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped no-datatable">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 20px">No</th>
                                    <th class="text-center">No Pallet</th>
                                    <th class="text-center">Owner Name</th>
                                    <th class="text-center">No Booking</th>
                                    <th class="text-center">No Goods</th>
                                    <th class="text-center">Goods Name</th>
                                    <th class="text-center">Ex No Container</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; ?>
                                <?php if ($opname['status'] == OpnameModel::STATUS_PENDING): ?>
                                    <?php foreach ($opnameStocks as $stock): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center"><?= $stock['no_pallet'] ?></td>
                                            <td ><?= $stock['name'] ?></td>
                                            <td class="text-center">
                                                <?= $stock['no_booking'] ?>
                                                <small><?= "(".$stock['no_reference'].")" ?></small>
                                            </td>
                                            <td class="text-center"><?= $stock['no_goods'] ?></td>
                                            <td class="text-center"><?= $stock['name_goods'] ?></td>
                                            <td class="text-center"><?= if_empty($stock['ex_no_container'],'-') ?></td>
                                            <td class="text-center"><?= $stock['unit'] ?></td>
                                            <td class="text-center"><input type="text" name="position[]" autocomplete="off" class="form-control"></td>
                                            <td class="text-center"><input type="number" min="0" name="quantity[]" class="form-control"></td>
                                            <td class="text-center"><textarea class="form-control" name="description[]"></textarea></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if($opname['status'] == OpnameModel::STATUS_PROCESSED || $opname['status'] == OpnameModel::STATUS_REOPENED): ?>
                                        <?php foreach ($opnameStocks as $stock): ?>
                                            <?php $qty = $stock['quantity_check'] != null ? numerical($stock['quantity_check'], 3, true) : null ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td class="text-center"><?= $stock['no_pallet'] ?></td>
                                                <td ><?= $stock['name'] ?></td>
                                                <td class="text-center">
                                                    <?= $stock['no_booking'] ?>
                                                    <small><?= "(".$stock['no_reference'].")" ?></small>
                                                </td>
                                                <td class="text-center"><?= $stock['no_goods'] ?></td>
                                                <td class="text-center"><?= $stock['name_goods'] ?></td>
                                                <td class="text-center"><?= if_empty($stock['ex_no_container'],'-') ?></td>
                                                <td class="text-center"><?= if_empty(numerical($stock['quantity'], 3, true), ' - ') ?></td>
                                                <td class="text-center"><?= $stock['unit'] ?></td>
                                                <td class="text-center"><input type="text" name="position[]" class="form-control" value="<?= set_value('position',  $stock['position_check']) ?>"></td>
                                                <td class="text-center"><input type="number" min="0" name="quantity[]" class="form-control text-center" value="<?= set_value('quantity',  $qty) ?>"></td>
                                                <td class="text-center"><textarea class="form-control" name="description[]"> <?= set_value('description', $stock['description_check']) ?></textarea></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(empty($opnameStocks)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center"><?= "No Document" ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <label for="Photo">General Description : </label>
                            <?= if_empty($opname['description_check'],'No Description') ?>
                        </div>
                        <?php if ($opname['status'] == OpnameModel::STATUS_PENDING && (!empty($opnameStocks))): ?>
                            <div class="form-group">
                                <label for="Photo">Photo</label>
                                <input type="file" name="photo" id="photo" required
                                       accept="image/*"
                                       placeholder="Photo">
                                <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                            </div>
                        <?php else: ?>
                            <?php if(($opname['status'] == OpnameModel::STATUS_PROCESSED || $opname['status'] == OpnameModel::STATUS_REOPENED) && (!empty($opnameStocks))):  ?>
                                <div class="form-group">
                                    <label for="Photo">Old Photo</label>
                                    <p class="form-control-static">
                                        <a href="<?= base_url('uploads/opname_photo/' . urlencode($opname['photo_check'])) ?>">
                                            <?= $opname['photo_check'] ?>
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
                        <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <?php if($opname['opname_type'] == "CONTAINER"): ?>
            <form action="<?= site_url('opname/save_process/' . $opname['id']) ?>" role="form" method="post" enctype="multipart/form-data">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Opname Data</h3>
                        <div class="pull-right">
                            <a href="<?= site_url('opname/download-opname/' . $opname['id']) ?>" class="btn btn-primary">
                                <i class="fa fa-download mr10"></i>Download
                            </a>
                            <a href="<?= site_url('opname/upload-opname/' . $opname['id']) ?>" class="btn btn-success">
                                <i class="fa fa-upload mr10"></i> Upload Result
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped no-datatable">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 20px">No</th>
                                    <th class="text-center">Owner Name</th>
                                    <th class="text-center">No Booking</th>
                                    <th class="text-center">No Container</th>
                                    <th class="text-center">Seal</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; ?>
                                <?php if ($opname['status'] == OpnameModel::STATUS_PENDING): ?>
                                    <?php foreach ($opnameContainers as $container): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td ><?= $container['name'] ?></td>
                                            <td class="text-center">
                                                <?= $container['no_booking'] ?>
                                                <small><?= "(".$container['no_reference'].")" ?></small>
                                            </td>
                                            <td class="text-center"><?= $container['no_container'] ?></td>
                                            <td class="text-center"><?= $container['seal'] ?></td>
                                            <td class="text-center"><input type="text" name="position[]" autocomplete="off" class="form-control"></td>
                                            <td class="text-center"><input type="number" min="0" name="quantity[]" class="form-control"></td>
                                            <td class="text-center"><textarea class="form-control" name="description[]"></textarea></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if($opname['status'] == OpnameModel::STATUS_PROCESSED || $opname['status'] == OpnameModel::STATUS_REOPENED): ?>
                                        <?php foreach ($opnameContainers as $container): ?>
                                            <?php $qty = $container['quantity_check'] != null ? numerical($container['quantity_check'], 3, true) : null ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td ><?= $container['name'] ?></td>
                                                <td class="text-center">
                                                    <?= $container['no_booking'] ?>
                                                    <small><?= "(".$container['no_reference'].")" ?></small>
                                                </td>
                                                <td class="text-center"><?= $container['no_container'] ?></td>
                                                <td class="text-center"><?= $container['seal'] ?></td>
                                                <td class="text-center"><input type="text" name="position[]" class="form-control" value="<?= set_value('position',  $container['position_check']) ?>"></td>
                                                <td class="text-center"><input type="number" min="0" name="quantity[]" class="form-control" value="<?= set_value('quantity',  $qty) ?>"></td>
                                                <td class="text-center"><textarea class="form-control" name="description[]"> <?= set_value('description', $container['description_check']) ?></textarea></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(empty($opnameContainers)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center"><?= "No Document" ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <label for="Photo">General Description : </label>
                            <?= if_empty($opname['description_check'],'No Description') ?>
                        </div>
                        <?php if ($opname['status'] == OpnameModel::STATUS_PENDING && (!empty($opnameContainers))): ?>
                            <div class="form-group">
                                <label for="Photo">Photo</label>
                                <input type="file" name="photo" id="photo" required
                                       accept="image/*"
                                       placeholder="Photo">
                                <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                            </div>
                        <?php else: ?>
                            <?php if(($opname['status'] == OpnameModel::STATUS_PROCESSED || $opname['status'] == OpnameModel::STATUS_REOPENED) && (!empty($opnameContainers))):  ?>
                                <div class="form-group">
                                    <label for="Photo">Old Photo</label>
                                    <p class="form-control-static">
                                        <a href="<?= base_url('uploads/opname_photo/' . urlencode($opname['photo_check'])) ?>">
                                            <?= $opname['photo_check'] ?>
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
                        <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
