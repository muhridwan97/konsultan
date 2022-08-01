<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Security Check Type</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Category</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $category ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Last Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date(end($securityPhotos)['updated_at'] ?? end($securityPhotos)['created_at'] ?? null, 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Security Check Photo</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Type</th>
                        <th>Photo Title</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($securityPhotos as $index => $securityPhoto): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $securityPhoto['type'] ?></td>
                            <td><?= if_empty($securityPhoto['photo_title'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($securityPhotos)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No data available</td>
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