<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Change Ownership</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Transaction</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $changeOwnership['no_change_ownership'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">Change Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= readable_date($changeOwnership['change_date']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">Owner Before</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $changeOwnership['owner_from'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">New Owner</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $changeOwnership['owner_to'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $changeOwnership['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= readable_date($changeOwnership['created_at']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if (!empty($changeOwnershipContainers)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Containers</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Container</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Seal</th>
                            <th>Status</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($changeOwnershipContainers as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $container['no_container'] ?></td>
                                <td><?= $container['type'] ?></td>
                                <td><?= $container['size'] ?></td>
                                <td><?= if_empty($container['seal'], '-') ?></td>
                                <td><?= if_empty($container['status'], '-') ?></td>
                                <td><?= if_empty($container['description'], 'No description') ?></td>
                            </tr>
                            <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <table class="table table-condensed no-datatable">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Tonnage (Kg)</th>
                                                <th>Volume (M<sup>3</sup>)</th>
                                                <th>No Pallet</th>
                                                <th>Description</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($container['goods'] as $item): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td><?= $item['name'] ?></td>
                                                    <td><?= numerical($item['quantity']) ?></td>
                                                    <td><?= $item['unit'] ?></td>
                                                    <td><?= numerical($item['tonnage']) ?></td>
                                                    <td><?= numerical($item['volume']) ?></td>
                                                    <td><?= if_empty($item['position'], 'No position') ?></td>
                                                    <td><?= if_empty($item['no_pallet'], 'No pallet') ?></td>
                                                    <td><?= if_empty($item['description'], 'No description') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($changeOwnershipContainers)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($changeOwnershipGoods)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Goods</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Tonnage (Kg)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                            <th>Position</th>
                            <th>No Pallet</th>
                            <th>No DO</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($changeOwnershipGoods as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= numerical($item['quantity']) ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td><?= numerical($item['tonnage']) ?></td>
                                <td><?= numerical($item['volume']) ?></td>
                                <td><?= if_empty($item['position'], 'No position') ?></td>
                                <td><?= if_empty($item['no_pallet'], 'No pallet') ?></td>
                                <td><?= if_empty($item['no_delivery_order'], 'No DO') ?></td>
                                <td><?= if_empty($item['description'], 'No description') ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($changeOwnershipGoods)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <a href="<?= site_url('change_ownership') ?>" class="btn btn-primary">Back to Ownership List</a>
    </div>
    <!-- /.box-footer -->
</div>