<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Item</h3>
    </div>
    <div class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Item Code</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $item['no_goods'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">HS Code</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($item['no_hs'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Whey Number</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($item['whey_number'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $item['name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Goods Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($item['type_goods'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Assembly</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(empty($item['no_assembly'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('assembly-goods/view?goods=' . $item['id']) ?>">
                                        <?= $item['no_assembly'] ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shrink Tolerance</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($item['shrink_tolerance'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($item['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Unit Weight</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_weight']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Unit Gross Weight</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_gross_weight']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Unit Length</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_length']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Unit Width</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_width']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Unit Height</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_height']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Unit Volume</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($item['unit_volume']), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($item['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!empty($package)): ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Package</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-3">No Goods</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static"><?= $package['no_goods'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">Package Name</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">
                                            <a href="<?= site_url('goods/view/' . $package['id']) ?>">
                                                <?= if_empty($package['name'], '-') ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">Total Items</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static"><?= $package['total_items'] ?> items</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if(!empty($assemblies)): ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Assemblies</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Goods Name</th>
                                <th>No Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($assemblies as $index => $assembly): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('goods/view/' . $assembly['id_goods']) ?>">
                                            <?= $assembly['goods_name'] ?>
                                        </a>
                                    </td>
                                    <td><?= $assembly['no_goods'] ?></td>
                                    <td><?= numerical($assembly['quantity'], 2, true) ?></td>
                                    <td><?= $assembly['unit'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <a href="<?= site_url('goods/edit/' . $item['id']) ?>>" class="btn btn-primary pull-right">Edit</a>
        </div>
    </div>
</div>