<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Position</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Warehouse</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <a href="<?= site_url('warehouse/view/' . $position['id_warehouse']) ?>">
                            <?= $position['warehouse'] ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <a href="<?= site_url('people/view/' . $position['id_customer']) ?>">
                            <?= $position['name'] ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Position</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $position['position'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Blocks</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php foreach ($blocks as $block): ?>
                            <span><?= $block['position_block'] ?></span><br>
                        <?php endforeach; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($position['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($position['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date(format_date($position['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </form>
</div>