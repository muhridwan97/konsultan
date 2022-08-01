<div class="box box-primary">
    <div class="box-header with-border">
        <?php if ($cycleCounts['type'] == "GOODS"): ?>
            <h3 class="box-title">Cycle Count Goods</h3>
        <?php else: ?>
            <h3 class="box-title">Cycle Count Containers</h3>
        <?php endif; ?>
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
                                <?= $cycleCounts['branch'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cycleCounts['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Cycle Count Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($cycleCounts['cycle_count_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if ($cycleCounts['type'] == "GOODS"): ?>
            <form action="<?= site_url('cycle-count/save_process/' . $cycleCounts['id']) ?>" method="post" enctype="multipart/form-data" id="form-process-cycle-count">
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
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; ?>
                                <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PENDING): ?>
                                    <?php foreach ($cycleCountDetails as $stock): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $stock['name'] ?></td>
                                            <td class="text-center">
                                                <?= $stock['no_booking'] ?>
                                                <small><?= "(" . $stock['no_reference'] . ")" ?></small>
                                            </td>
                                            <td class="text-center"><?= $stock['no_goods'] ?></td>
                                            <td class="text-center"><?= $stock['goods_name'] ?></td>
                                            <td class="text-center"><?= if_empty(numerical($stock['quantity'], 3, true), ' - ') ?></td>
                                            <td class="text-center"><?= $stock['unit'] ?></td>
                                            <td class="text-center">
                                                <input type="text" name="position[]"
                                                       autocomplete="off"
                                                       class="form-control-static" required>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" min="0" name="quantity[]"
                                                       class="form-control-static" required>
                                            </td>
                                            <td class="text-center">
                                                <textarea class="form-control-static"
                                                          name="description[]"></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PROCESSED || $cycleCounts['status'] == CycleCountModel::STATUS_REOPENED): ?>
                                        <?php foreach ($cycleCountDetails as $stock): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= $stock['name'] ?></td>
                                                <td class="text-center">
                                                    <?= $stock['no_booking'] ?>
                                                    <small><?= "(" . $stock['no_reference'] . ")" ?></small>
                                                </td>
                                                <td class="text-center"><?= $stock['no_goods'] ?></td>
                                                <td class="text-center"><?= $stock['unit'] ?></td>
                                                <td class="text-center">
                                                    <input type="text" name="position[]"
                                                           class="form-control-static" required
                                                           value="<?= set_value('position', $stock['position_check']) ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" min="0" name="quantity[]"
                                                           class="form-control-static" required
                                                           value="<?= set_value('quantity', $stock['quantity_check']) ?>">
                                                </td>
                                                <td class="text-center">
                                                    <textarea class="form-control-static"
                                                              name="description[]"><?= set_value('description', $stock['description_check']) ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (empty($cycleCountDetails)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center"><?= "No Document" ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PENDING && (!empty($cycleCountDetails))): ?>
                            <div class="form-group">
                                <label for="Photo">Photo</label>
                                <input type="file" name="photo" id="photo" required
                                       accept="image/*"
                                       placeholder="Photo">
                                <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                            </div>
                        <?php else: ?>
                            <?php if (($cycleCounts['status'] == CycleCountModel::STATUS_PROCESSED || $cycleCounts['status'] == CycleCountModel::STATUS_REOPENED) && (!empty($cycleCountDetails))): ?>
                                <div class="form-group">
                                    <label for="Photo">Old Photo</label>
                                    <p class="form-control-static">
                                        <a href="<?= base_url('uploads/cycle_count_photo/' . urlencode($cycleCounts['photo'])) ?>">
                                            <?= $cycleCounts['photo'] ?>
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

        <?php if ($cycleCounts['type'] == "CONTAINER"): ?>
            <form action="<?= site_url('cycle-count/save_process/' . $cycleCounts['id']) ?>" method="post"
                  enctype="multipart/form-data" id="form-process-cycle-count">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped no-datatable">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 20px">No</th>
                                    <th class="text-center">Owner Name</th>
                                    <th class="text-center">No Booking</th>
                                    <th class="text-center">No Container</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Seal</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; ?>
                                <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PENDING): ?>
                                    <?php foreach ($cycleCountContainers as $container): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $container['name'] ?></td>
                                            <td class="text-center">
                                                <?= $container['no_booking'] ?>
                                                <small><?= "(" . $container['no_reference'] . ")" ?></small>
                                            </td>
                                            <td class="text-center"><?= $container['no_container'] ?></td>
                                            <td class="text-center"><?= if_empty(numerical($container['quantity'], 3, true), ' - ') ?></td>
                                            <td class="text-center"><?= $container['seal'] ?></td>
                                            <td class="text-center">
                                                <input type="text" name="position[]" autocomplete="off" class="form-control-static" required>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" min="0" name="quantity[]" class="form-control-static" required>
                                            </td>
                                            <td class="text-center">
                                                <textarea class="form-control-static" name="description[]"></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PROCESSED || $cycleCounts['status'] == CycleCountModel::STATUS_REOPENED): ?>
                                        <?php foreach ($cycleCountContainers as $container): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= $container['name'] ?></td>
                                                <td class="text-center">
                                                    <?= $container['no_booking'] ?>
                                                    <small><?= "(" . $container['no_reference'] . ")" ?></small>
                                                </td>
                                                <td class="text-center"><?= $container['no_container'] ?></td>
                                                <td class="text-center"><?= $container['seal'] ?></td>
                                                <td class="text-center"><input type="text" name="position[]"
                                                                               class="form-control-static" required
                                                                               value="<?= set_value('position', $container['position_check']) ?>">
                                                </td>
                                                <td class="text-center"><input type="number" min="0" name="quantity[]"
                                                                               class="form-control-static" required
                                                                               value="<?= set_value('quantity', $container['quantity_check']) ?>">
                                                </td>
                                                <td class="text-center"><textarea class="form-control-static"
                                                                                  name="description[]"> <?= set_value('description', $container['description_check']) ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (empty($cycleCountContainers)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center"><?= "No Document" ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($cycleCounts['status'] == CycleCountModel::STATUS_PENDING && (!empty($cycleCountContainers))): ?>
                            <div class="form-group">
                                <label for="Photo">Photo</label>
                                <input type="file" name="photo" id="photo" required
                                       accept="image/*"
                                       placeholder="Photo">
                                <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                            </div>
                        <?php else: ?>
                            <?php if (($cycleCounts['status'] == CycleCountModel::STATUS_PROCESSED || $cycleCounts['status'] == CycleCountModel::STATUS_REOPENED) && (!empty($cycleCountContainers))): ?>
                                <div class="form-group">
                                    <label for="Photo">Old Photo</label>
                                    <p class="form-control-static">
                                        <a href="<?= base_url('uploads/cycle_count_photo/' . urlencode($cycleCounts['photo'])) ?>">
                                            <?= $cycleCounts['photo'] ?>
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
