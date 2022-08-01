<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Checklist In</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $safeConductChecklist['no_safe_conduct'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Container</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($safeConductChecklist['no_container'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $safeConductChecklist['type'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Attachment Seal</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= empty($safeConductChecklist['attachment_seal']) ? '#' : asset_url($safeConductChecklist['attachment_seal']) ?>">
                                    <?= if_empty(basename($safeConductChecklist['attachment_seal']),'-');?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConductChecklist['description'],'-');?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                               <?= $safeConductChecklist['created_at'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Safe Conduct Checklist In</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Checklist Name</th>
                        <th>Result</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;?>
                    <?php foreach ($safeConductChecklistDetails as $safeConductChecklistDetail): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $safeConductChecklistDetail['checklist_name'] ?></td>
                        <td> 
                            <span class="label label-<?= $safeConductChecklistDetail['result'] ? 'success' : 'danger' ?>">
                                <?= $safeConductChecklistDetail['result'] ? 'GOOD' : 'NOT GOOD' ?>
                            </span>
                        </td>
                        <td><?= if_empty($safeConductChecklistDetail['description'], 'No description') ?></td>
                        <td><?= $safeConductChecklistDetail['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($safeConductChecklistDetails)): ?>
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
                <h3 class="box-title">Checklist Photo</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Photo Title</th>
                        <th>Download</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;?>
                    <?php foreach ($safeConductChecklistPhotos as $safeConductChecklistPhoto): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $safeConductChecklistPhoto['title'] ?></td>
                            <td><a href="<?= asset_url($safeConductChecklistPhoto['photo']) ?>">Download</a></td>
                            <td><?= $safeConductChecklistPhoto['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($safeConductChecklistPhotos)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No photo available</td>
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