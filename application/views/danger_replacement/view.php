<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Danger Replacement</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Booking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $dangerReplacement['id_booking']) ?>">
                                    <?= $dangerReplacement['no_booking'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $dangerReplacement['no_reference'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Replace Danger To</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($dangerReplacement['status_danger'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    'PENDING' => 'default',
                                    'APPROVED' => 'success',
                                    'REJECTED' => 'danger',
                                    'EXPIRED' => 'warning',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$dangerReplacement['status']] ?>">
                                    <?= $dangerReplacement['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($dangerReplacement['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= readable_date($dangerReplacement['validated_at']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($dangerReplacement['validator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($dangerReplacement['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php if (!empty($containers)): ?>
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
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($containers as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $container['no_container'] ?></td>
                                <td><?= $container['type'] ?></td>
                                <td><?= $container['size'] ?></td>
                                <td><?= if_empty($container['description'], 'No description') ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($containers)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($goods)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Goods</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Goods</th>
                            <th>Goods</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($goods as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['no_name'] ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= if_empty($item['description'], 'No description') ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($bookingGoods)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>
<!-- /.box -->