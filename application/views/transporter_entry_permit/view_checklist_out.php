<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Checklist Out</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Transporter Entry Permit</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $tepChecklists['tep_code'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Container</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($tepChecklists['no_container'],'-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $tepChecklists['type'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Attachment Seal</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= empty($tepChecklists['attachment_seal']) ? '#' : asset_url($tepChecklists['attachment_seal']) ?>">
                                    <?= if_empty(basename($tepChecklists['attachment_seal']),'-');?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChecklists['description'],'-');?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                               <?= $tepChecklists['created_at'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Transporter Entry Permit Checklist Out</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                <table class="table table-bordered no-datatable">
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
                    <?php foreach ($tepChecklistDetails as $tepChecklistDetail): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $tepChecklistDetail['checklist_name'] ?></td>
                        <td> 
                            <span class="label label-<?= $tepChecklistDetail['result'] ? 'success' : 'danger' ?>">
                                <?= $tepChecklistDetail['result'] ? 'GOOD' : 'NOT GOOD' ?>
                            </span>
                        </td>
                        <td><?= if_empty($tepChecklistDetail['description'], 'No description') ?></td>
                        <td><?= $tepChecklistDetail['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tepChecklistDetails)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>