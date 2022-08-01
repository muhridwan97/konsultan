<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Item Priority History
        </h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $goods['no_reference'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Goods Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static" title="<?= $goods['no_goods'] ?>">
                                <?= $goods['goods_name'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Unit</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $goods['unit'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Ex No Container</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($goods['ex_no_container'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr class="text-nowrap">
                <th style="width: 30px">No</th>
                <th>Goods</th>
                <th>Unit</th>
                <th>Ex No Container</th>
                <th>Priority</th>
                <th>Unload Location</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Created By</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tepPriorityItems as $index => $priorityItem): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?= $priorityItem['goods_name'] ?><br>
                        <small class="text-muted"><?= $priorityItem['no_reference'] ?></small>
                    </td>
                    <td><?= $priorityItem['unit'] ?></td>
                    <td><?= if_empty($priorityItem['ex_no_container'], '-') ?></td>
                    <td><?= $priorityItem['priority'] ?></td>
                    <td><?= $priorityItem['unload_location'] ?></td>
                    <td><?= if_empty($priorityItem['description'], '-') ?></td>
                    <td><?= $priorityItem['created_at'] ?></td>
                    <td><?= $priorityItem['creator_name'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        <div class="pull-right">
            <a href="<?= site_url('transporter-entry-permit-request-priority/edit') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-success">
                Edit Priority & Location
            </a>
        </div>
    </div>
</div>