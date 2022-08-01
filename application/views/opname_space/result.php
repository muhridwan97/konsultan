<div class="box box-primary">
    <div class="box-header with-border">
        <?php if($cycleCounts['type'] == "GOODS"): ?>
        <h3 class="box-title">Cycle Count Goods</h3>
        <?php else: ?>
         <h3 class="box-title">Cycle Count  Container</h3>
        <?php endif; ?>
    </div>
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <form class="form-horizontal form-view">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">Date :</label>
                            <div class="col-sm-9">
                                <p><strong><?= format_date($cycleCounts['cycle_count_date'], 'd F Y') ?> </strong></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Description :</label>
                            <div class="col-sm-9">
                                <p><strong><?= if_empty($cycleCounts['description'], ' No Description') ?></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3"> Status :</label>
                            <div class="col-sm-9">
                                 <p class="<?= $cycleCounts['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><strong><?= $cycleCounts['status'] ?></strong></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3"> Validated By :</label>
                            <div class="col-sm-9">
                                 <p><strong><?= if_empty($cycleCounts['validated_by'], '-') ?></strong></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
        <?php if($cycleCounts['type'] == "GOODS"): ?>
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
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($cycleCountDetails as $stock): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td ><?= $stock['name'] ?></td>
                                        <td class="text-center">
                                            <?= $stock['no_booking'] ?>
                                            <small><?= "(".$stock['no_reference'].")" ?></small> 
                                        </td>
                                        <td class="text-center"><?= $stock['no_goods'] ?></td>
                                        <td class="text-center"><?= $stock['goods_name'] ?></td>
                                        <td>
                                            <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                                            <small class="text-muted" style="display: block">
                                               <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                                            </small>
                                        </td>
                                        <td class="text-center"><?= if_empty(numerical($stock['quantity'],3,true), ' - ') ?></td>
                                        <td class="text-center"><?= $stock['unit'] ?></td>
                                        <td class="text-center"><?= if_empty($stock['description'],'-') ?></td>
                                        <td class="text-center"><?= $stock['position_check'] ?></td>
                                        <td class="text-center"><?= $stock['quantity_check'] ?></td>
                                        <td class="text-center"><?= if_empty($stock['description_check'],'-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($cycleCountDetails)): ?>
                                <tr>
                                    <td colspan="11" class="text-center"><?= "No Document" ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label for="Photo">Photo</label>
                        <p class="form-control-static">
                            <a href="<?= base_url('uploads/cycle_count_photo/' . urlencode($cycleCounts['photo'])) ?>">
                                <?= $cycleCounts['photo'] ?>
                            </a>
                        </p>
                    </div>
                   
                </div>
                <div class="box-footer">
                    <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                    
                </div>
            </div>
        <?PHP endif; ?>

        <?php if($cycleCounts['type'] == "CONTAINER"): ?>
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
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Seal</th>
                                    <th class="text-center">Position Check</th>
                                    <th class="text-center">Quantity Check</th>
                                    <th class="text-center">Description Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($cycleCountContainers as $container): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td ><?= $container['name'] ?></td>
                                        <td class="text-center">
                                            <?= $container['no_booking'] ?>
                                            <small><?= "(".$container['no_reference'].")" ?></small> 
                                        </td>
                                        <td class="text-center"><?= $container['no_container'] ?></td>
                                        <td class="text-center"><?= if_empty(numerical($container['quantity'],3,true), ' - ') ?></td>
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
                                <?php if(empty($cycleCountContainers)): ?>
                                <tr>
                                    <td colspan="8" class="text-center"><?= "No Document" ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                     <div class="form-group">
                        <label for="Photo">Photo</label>
                        <p class="form-control-static">
                            <a href="<?= base_url('uploads/cycle_count_photo/' . urlencode($cycleCounts['photo'])) ?>">
                                <?= $cycleCounts['photo'] ?>
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