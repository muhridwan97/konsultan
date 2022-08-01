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
                            <p><?= format_date($opname['opname_date'], 'd F Y') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Requested By</label>
                        <div class="col-sm-9">
                             <p><?= if_empty($opname['requested_by'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Is Import</label>
                        <div class="col-sm-9">
                            <p><?= $opname['is_import'] ? 'YES' : 'NO' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p><?= if_empty($opname['description'], ' No Description') ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3"> Opname Type</label>
                        <div class="col-sm-9">
                             <p><?= $opname['opname_type'] ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3"> Status</label>
                        <div class="col-sm-9">
                             <p class="<?= $opname['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><?= $opname['status'] ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3"> Validated By</label>
                        <div class="col-sm-9">
                             <p><?= if_empty($opname['validated_by'], '-') ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3"> Photo Check</label>
                        <div class="col-sm-9">
                             <p>
                                 <?php if(empty($opname['photo_check'])): ?>
                                    -
                                 <?php else: ?>
                                     <a href="<?= base_url('uploads/opname_photo/' . urlencode($opname['photo_check'])) ?>">
                                         <?= $opname['photo_check'] ?>
                                     </a>
                                 <?php endif ?>
                             </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3"> Last file uploaded</label>
                        <div class="col-sm-9">
                             <p>
                                 <?php if(empty($opname['file_check'])): ?>
                                    -
                                 <?php else: ?>
                                     <a href="<?= base_url('uploads/opname_file/' . urlencode($opname['file_check'])) ?>">
                                         <?= $opname['file_check'] ?>
                                     </a>
                                 <?php endif ?>
                             </p>
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
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 20px">No</th>
                            <th class="text-center">No Pallet</th>
                            <th class="text-center">Owner Name</th>
                            <th class="text-center">No Booking</th>
                            <th class="text-center">No Goods</th>
                            <th class="text-center">Goods Name</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Ex No Container</th>
                            <th class="text-center">Tonnage</th>
                            <th class="text-center">Tonnage Gross</th>
                            <th class="text-center">Volume</th>
                            <th class="text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($opnameStocks as $stock): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= $stock['no_pallet'] ?></td>
                                <td ><?= $stock['name'] ?></td>
                                <td class="text-center"><?= $stock['no_booking'] ?></td>
                                <td class="text-center"><?= $stock['no_goods'] ?></td>
                                <td class="text-center"><?= $stock['name_goods'] ?></td>
                                <td class="text-center"><?= $stock['unit'] ?></td>
                                <td>
                                    <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                                    <small class="text-muted" style="display: block">
                                       <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                                    </small>
                                </td>
                                <td class="text-center"><?= if_empty($stock['ex_no_container'],'-') ?></td>
                                <td class="text-center"><?= if_empty($stock['tonnage'], ' - ') ?></td>
                                <td class="text-center"><?= if_empty($stock['tonnage_gross'], ' - ')  ?></td>
                                <td class="text-center"><?= if_empty($stock['volume'], ' - ' ) ?></td>
                                <td class="text-center"><?= if_empty($stock['description'], 'No description') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if($opname['opname_type'] == "CONTAINER"): ?>
    <div class="box box-primary">
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 20px">No</th>
                            <th class="text-center">Owner Name</th>
                            <th class="text-center">No Booking</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Seal</th>
                            <th class="text-center">Status Danger</th>
                            <th class="text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($opnameContainers as $container): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td ><?= $container['name'] ?></td>
                                <td class="text-center"><?= $container['no_booking'] ?></td>
                                <td class="text-center"><?= $container['no_container'] ?></td>
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

    <?php endif; ?>
    <div class="box-footer">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
    </div>
</div>
