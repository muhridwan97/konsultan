<div class="box box-primary">
    <div class="box-header with-border">
         <?php if($cycleCounts['type'] == "GOODS"): ?>
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
                                <?=  $cycleCounts['branch'] ?>
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

        <div class="box box-primary">
            <?php if($cycleCounts['type'] == "GOODS"): ?>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-file">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 20px">No</th>
                        <th class="text-center">Owner Name</th>
                        <th class="text-center">No Booking</th>
                        <th class="text-center">No Goods</th>
                        <th class="text-center">Goods Name</th>
                        <th class="text-center">Position</th>
                        <th class="text-center">Ex No Container</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Tonnage</th>
                        <th class="text-center">Tonnage Gross</th>
                        <th class="text-center">Volume</th>
                        <th class="text-center">Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?> 
                    <?php foreach ($cycleCountDetails as $CycleCountDetail): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td ><?= $CycleCountDetail['name'] ?></td>
                            <td class="text-center"><?= $CycleCountDetail['no_booking'] ?></td>
                            <td class="text-center"><?= $CycleCountDetail['no_goods'] ?></td>
                            <td class="text-center"><?= $CycleCountDetail['goods_name'] ?></td>
                            <td>
                                <?= !empty($CycleCountDetail['position']) ? $CycleCountDetail['position'] : '-' ?>
                                <small class="text-muted" style="display: block">
                                   <?= !empty($CycleCountDetail['position_block']) ? $CycleCountDetail['position_block'] : '' ?>
                                </small>
                            </td>
                            <td class="text-center"><?= if_empty($CycleCountDetail['ex_no_container'],'-') ?></td>
                            <td class="text-center"><?= if_empty(numerical($CycleCountDetail['quantity'],3,true), ' - ') ?></td>
                            <td class="text-center"><?= $CycleCountDetail['unit'] ?></td>
                            <td class="text-center"><?= if_empty($CycleCountDetail['tonnage'], ' - ') ?></td>
                            <td class="text-center"><?= if_empty($CycleCountDetail['tonnage_gross'], ' - ')  ?></td>
                            <td class="text-center"><?= if_empty($CycleCountDetail['volume'], ' - ' ) ?></td>
                            <td class="text-center"><?= if_empty($CycleCountDetail['description'], 'No description') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($cycleCountDetails)): ?>
                        <tr>
                            <td class="text-center" colspan="12">No document</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif;?>
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
                                    <th class="text-center">Status Danger</th>
                                    <th class="text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($cycleCountContainers as $container): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td ><?= $container['name'] ?></td>
                                        <td class="text-center"><?= $container['no_booking'] ?></td>
                                        <td class="text-center"><?= $container['no_container'] ?></td>
                                        <td class="text-center"><?= if_empty(numerical($container['quantity'],3,true), ' - ') ?></td>
                                        <td>
                                            <?= !empty($container['position']) ? $container['position'] : '-' ?>
                                            <small class="text-muted" style="display: block">
                                               <?= !empty($container['position_block']) ? $container['position_block'] : '' ?>
                                            </small>
                                        </td>
                                        <td class="text-center"><?= if_empty($container['seal'],'-') ?></td>
                                        <td class="text-center"><?= $container['status_danger'] ?></td>
                                        <td class="text-center"><?= if_empty($container['description'], 'No description') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <div class="box-footer">
                <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>

</div>



<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/upload_document_file.js') ?>" defer></script>