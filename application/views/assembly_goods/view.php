<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Item</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Assembly Code</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $assembly['no_assembly'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Assembly Code</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= numerical($assembly['quantity_package'], 3, true) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($assembly['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($assembly['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($assembly['updated_at']) ?>
                    </p>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Assembly Goods Record</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped responsive">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach($assemblyGoods as $itemGoods): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?= $itemGoods['goods_name'] ?>
                                </td>
                                <td>
                                    <?= numerical($itemGoods['quantity_assembly'], 3, true) ?>
                                </td>
                                <td>
                                    <?= $itemGoods['unit'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>