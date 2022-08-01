<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Opname Space</h3>
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
                                <?= $opnameSpaces['branch'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($opnameSpaces['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Opname Space Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($opnameSpaces['opname_space_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="<?= site_url('opname-space/update/' . $opnameSpaces['id']) ?>" role="form" method="post" enctype="multipart/form-data">
            <div class="box box-primary">
                <div class="box-header with-border">
                <h3 class="box-title">Opname Data</h3>
                    <div class="pull-right">
                        <a href="<?= site_url('opname-space/process/' . $opnameSpaces['id'].'?export=true') ?>" class="btn btn-primary">
                            <i class="fa fa-download mr10"></i>Download
                        </a>
                        <a href="<?= base_url('uploads/opname_space_file/' . urlencode($opnameSpaces['file'])) ?>" class="btn btn-primary">
                            <i class="fa fa-download mr10"></i>Download Old File
                        </a>
                        <a href="<?= site_url('opname-space/upload-opname/' . $opnameSpaces['id']) ?>" class="btn btn-success">
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
                                <th class="text-center">No Reference</th>
                                <th class="text-center">No Ex Container</th>
                                <th class="text-center">No Goods</th>
                                <th class="text-center">Goods Name</th>
                                <th class="text-center">Space Check</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($opnameSpaceDetails as $stock): ?>
                                <?php
                                    $countData = 0;
                                    if(!empty($stock['detail_goods'])){
                                        $countData = count($stock['detail_goods']) ;
                                    }
                                    $countGoods = !empty($stock['detail_goods']) && $countData >= 1 ? $countData : '';
                                ?>
                                <tr>
                                    <td rowspan="<?= $countGoods ?>"><?= $no++; ?></td>
                                    <td rowspan="<?= $countGoods ?>"><?= $stock['no_reference'] ?></td>
                                    <?php if (!empty($countData)): ?>
                                        <td><?= if_empty($stock['detail_goods'][0]['ex_no_container'], '-') ?></td>
                                        <td><?= if_empty($stock['detail_goods'][0]['no_goods'], '-') ?></td>
                                        <td><?= if_empty($stock['detail_goods'][0]['goods_name'], '-') ?></td>
                                    <?php else: ?>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    <?php endif; ?>
                                    <td rowspan="<?= $countGoods ?>" class="text-center"><input type="number" min="0" step="any" name="space_check[]" class="form-control" value="<?= set_value('space_check',  $stock['space_check']) ?>"></td>
                                    <!-- <td rowspan="<?= $countGoods ?>"><?= !empty($stock['space_check'])?numerical($stock['space_check'],2). " m<sup>2</sup>" : 'Not checked' ?> </td> -->
                                </tr>
                                    <?php if (!empty($countData)): ?>
                                        <?php foreach($stock['detail_goods'] AS $index => $detail): ?>
                                            <?php if ($index != 0): ?>
                                                <tr>
                                                    <td><?= if_empty($detail['ex_no_container'], '-') ?></td>
                                                    <td><?= if_empty($detail['no_goods'], '-') ?></td>
                                                    <td><?= if_empty($detail['goods_name'], '-') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (empty($opnameSpaceDetails)): ?>
                                <tr>
                                    <td colspan="8" class="text-center"><?= "No Document" ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Upload Histories</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-datatable">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 20px">No</th>
                                <th class="text-center">File</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Upload By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($opnameSpaceUploadHistories as $history): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><a href="<?= base_url('uploads/opname_space_file/' . urlencode($history['file'])) ?>"><?= $history['file'] ?></a></td>
                                    <td><?= $history['description'] ?></td>
                                    <td><?= $history['creator_name'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($opnameSpaceUploadHistories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center"><?= "No history" ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>
            <div class="box-footer">
                <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update</button>
            </div>
        </form>
    </div>
</div>
