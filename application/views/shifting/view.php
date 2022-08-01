<div class="box box-primary">
    <div class="box-header">
        <div class="box-title">View Shifting</div>
    </div>
    <div class="box-body">
        <div class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Handling</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $shifting['no_shifting'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shifting Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= readable_date($shifting['shifting_date']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($shifting['description'], 'No description') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $shifting['status'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($shifting['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($shifting['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Shifting Record</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-shifting">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Reference</th>
                        <th>Container / Goods</th>
                        <th>New Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($shiftingDetails as $index => $shiftingDetail) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $shiftingDetail['no_reference'] ?></td>
                            <td><?= !empty($shiftingDetail['id_container']) ? $shiftingDetail['no_container'] : $shiftingDetail['goods_name'] ?></td>
                            <td>
                                <?= if_empty($shiftingDetail['position'],'-') ?>
                                <small class="text-muted" style="display: block">
                                    <?= str_replace(',', ', ', $shiftingDetail['position_blocks']) ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($shiftingDetails)) : ?>
                        <tr>
                            <td class="text-center" colspan="5">No Data</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>
