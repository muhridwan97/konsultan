<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Handling Type</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Handling Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['handling_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Code</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['handling_code'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Category</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['category'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Container Multiplier</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['multiplier_container'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Duration</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['duration'] ?> minutes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Goods Multiplier</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['multiplier_goods'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $handlingType['description'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($handlingType['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($handlingType['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Attachment Photo</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Photo Name</th>
                        <th>Condition</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    foreach ($handlingTypePhotos as $handlingTypePhoto): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $handlingTypePhoto['photo_name'] ?></td>
                            <td><?= if_empty($handlingTypePhoto['condition'], '-') ?></td>
                            <td><?= if_empty($handlingTypePhoto['photo_description'], '-') ?></td>
                            <td><?= readable_date($handlingTypePhoto['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($handlingTypePhotos) <= 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Handling Component</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Handling Component</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    foreach ($components as $component): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $component['handling_component'] ?></td>
                            <td><?= if_empty($component['component_category'], '-') ?></td>
                            <td><?= if_empty($component['component_description'], '-') ?></td>
                            <td><?= readable_date($component['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($components) <= 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>
<!-- /.box -->