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
                                <?=  $putAway['branch'] ?>
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

        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-file">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 20px">No</th>
                        <th class="text-center">Owner Name</th>
                        <th class="text-center">No Work Order</th>
                        <th class="text-center">Approver Name</th>
                        <th class="text-center">Approver At</th>
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
                    <?php foreach ($putAwayDetails as $CycleCountDetail): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td ><?= $CycleCountDetail['name'] ?></td>
                            <td class="text-center"><a href="<?= site_url('work-order/view/'.$CycleCountDetail['id_work_order']) ?>" target="_blank"><?= $CycleCountDetail['no_work_order'] ?></a></td>
                            <td class="text-center"><?= $CycleCountDetail['approver_name'] ?></td>
                            <td class="text-center"><?= $CycleCountDetail['approved_at'] ?></td>
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
                    <?php if (empty($putAwayDetails)): ?>
                        <tr>
                            <td class="text-center" colspan="12">No Goods</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>

</div>



<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/upload_document_file.js') ?>" defer></script>