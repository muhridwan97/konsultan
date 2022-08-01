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
                            <label class="col-sm-4">Opname Date</label>
                            <div class="col-sm-8">
                                <p><?= format_date($opname['opname_date'], 'd F Y') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4">Requested By</label>
                            <div class="col-sm-8">
                                 <p><?= if_empty($opname['requested_by'], '-') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4">Location Accuracy</label>
                            <div class="col-sm-8">
                                 <p><?= numerical($opname['location_accuracy'] ?? 0, 2, true) ?>%</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4">Quantity Accuracy</label>
                            <div class="col-sm-8">
                                <p><?= numerical($opname['quantity_accuracy'] ?? 0, 2, true) ?>%</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-4">Status</label>
                            <div class="col-sm-8">
                                 <p class="<?= $opname['status'] != 'APPROVED' ? 'text-danger' : '' ?>">
                                     <?= $opname['status'] ?>
                                 </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4">Description</label>
                            <div class="col-sm-8">
                                <p><?= if_empty($opname['description'], ' No Description') ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4">Validated By</label>
                            <div class="col-sm-8">
                                 <p><?= if_empty($opname['validated_by'], '-') ?></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-4">Completed At</label>
                            <div class="col-sm-8">
                                 <p><?= if_empty(format_date($opname['completed_at'], 'd F Y'), '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php if($opname['opname_type'] == "GOODS"): ?>
            <div class="box box-primary">
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
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
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
                                        <td>
                                            <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                                            <small class="text-muted" style="display: block">
                                               <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                                            </small>
                                        </td>
                                        <td class="text-center"><?= $stock['quantity'] ?></td>
                                        <td class="text-center"><?= $stock['unit'] ?></td>
                                        <td class="text-center"><?= $stock['position_check'] ?></td>
                                        <td class="text-center"><?= $stock['quantity_check'] ?></td>
                                        <td class="text-center"><?= $stock['description_check'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($opnameStocks)): ?>
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
                    <div class="form-group">
                        <label for="Photo">Photo</label>
                        <p class="form-control-static">
                            <a href="<?= base_url('uploads/opname_photo/' . urlencode($opname['photo_check'])) ?>">
                                <?= $opname['photo_check'] ?>
                            </a>
                        </p>
                    </div>
                   
                </div>
                <div class="box-footer">
                    <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                    
                </div>
            </div>
        <?PHP endif; ?>

        <?php if($opname['opname_type'] == "CONTAINER"): ?>
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
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Seal</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($opnameContainers as $container): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td ><?= $container['name'] ?></td>
                                        <td class="text-center">
                                            <?= $container['no_booking'] ?>
                                            <small><?= "(".$container['no_reference'].")" ?></small> 
                                        </td>
                                        <td class="text-center"><?= $container['no_container'] ?></td>
                                        <td>
                                            <?= !empty($container['position']) ? $container['position'] : '-' ?>
                                            <small class="text-muted" style="display: block">
                                               <?= !empty($container['position_block']) ? $container['position_block'] : '' ?>
                                            </small>
                                        </td>
                                        <td class="text-center"><?= if_empty($container['seal'],'-') ?></td>
                                        <td class="text-center"><?= $container['position_check'] ?></td>
                                        <td class="text-center"><?= $container['quantity_check'] ?></td>
                                        <td class="text-center"><?= $container['description_check'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
                     <div class="form-group">
                        <label for="Photo">Photo</label>
                        <p class="form-control-static">
                            <a href="<?= base_url('uploads/opname_photo/' . urlencode($opname['photo_check'])) ?>">
                                <?= $opname['photo_check'] ?>
                            </a>
                        </p>
                    </div>
                   
                </div>
                <div class="box-footer">
                    <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                    
                </div>
            </div>
        <?PHP endif; ?>
</div>