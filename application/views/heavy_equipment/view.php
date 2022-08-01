<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Heavy Equipment</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Heavy Equipment Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $heavyEquipments['name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Heavy Equipment Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $heavyEquipments['type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $heavyEquipments['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Branches</label>
                        <div class="col-sm-8">
                            <ul class="form-control-static" style="padding-left: 10px">
                                <?php foreach ($heavyEquipmentBranches as $branch): ?>
                                    <li><?= $branch['branch'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($heavyEquipments['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($heavyEquipments['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>