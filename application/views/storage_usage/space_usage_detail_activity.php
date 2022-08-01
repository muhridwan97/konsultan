<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Detail Activity
            <?= if_empty(get_url_param('container'), '', '(', ')') ?>
        </h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $customer['name'] ?? 'No customer' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Goods Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $goods['name'] ?? 'No goods' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Work Order</th>
                <th>Container</th>
                <th>Quantity</th>
                <th class="text-right">In (M<sup>2</sup>)</th>
                <th class="text-right">Out (M<sup>2</sup>)</th>
                <th class="text-right" style="width: 150px">Usage Storage (M<sup>2</sup>)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($customerStorageDetails as $index => $storageUsage): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <a href="<?= site_url('work-order/view/' . $storageUsage['id_work_order']) ?>">
                            <?= $storageUsage['no_work_order'] ?>
                        </a><br>
                        <small class="text-muted">
                            Total Item <?= numerical($storageUsage['total_goods_loaded_quantity'], 2, true) ?>
                        </small>
                    </td>
                    <td>
                        <?= $storageUsage['no_container'] ?>
                        <?= if_empty($storageUsage['size'], '', '(', ')') ?>
                        <?= $storageUsage['no_container'] != 'LCL' && $storageUsage['is_lcl'] ? '(LCL)' : '' ?><br>
                        <small class="text-muted">
                            Capacity <?= numerical($storageUsage['container_capacity'], 2, true) ?> M<sup>2</sup>
                        </small>
                    </td>
                    <td>
                        <?= numerical($storageUsage['quantity'], 3, true) ?>
                        <?= $storageUsage['unit'] ?>
                    </td>
                    <td class="text-right">
                        <?= numerical($storageUsage['inbound_storage'], 3, true) ?>
                    </td>
                    <td class="text-right">
                        <?= numerical($storageUsage['outbound_storage'], 3, true) ?>
                    </td>
                    <td class="text-right">
                        <?= numerical($storageUsage['used_storage'], 3, true) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td colspan="5"><strong>TOTAL</strong></td>
                <td class="text-right">
                    <?= numerical(array_sum(array_column($customerStorageDetails, 'used_storage')), 3, true) ?> M<sup>2</sup>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>