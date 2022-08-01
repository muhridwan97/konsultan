<form class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">Type</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $document['type'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Date</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= format_date($document['date'], 'd F Y') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Status</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php
                        $statusLabel = [
                            'EMPTY' => 'label-warning',
                            'PENDING' => 'label-default',
                            'REJECTED' => 'label-danger',
                            'APPROVED' => 'label-success',
                        ];
                        $document['status'] = if_empty($document['status'], $document['total_files'] <= 0 ? 'EMPTY' : 'PENDING');
                        ?>
                        <span class="label <?= $statusLabel[$document['status']] ?>">
                                    <?= $document['status'] ?>
                                </span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Validated By</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($document['validator_name'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Validated At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($document['validated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">Description</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($document['description'], 'No description') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created By</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($document['creator_name'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= format_date($document['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Updated At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= format_date($document['updated_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<table class="table table-bordered table-striped no-datatable" id="table-file">
    <thead>
    <tr>
        <th style="width: 20px">No</th>
        <th>File</th>
        <th>Description</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1;
    foreach ($files as $file): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <a href="<?= base_url('uploads/' . $file['source']) ?>">
                    <?= basename($file['source']) ?>
                </a>
            </td>
            <td><?= if_empty($file['description'], '-') ?></td>
            <td><?= format_date($document['created_at'], 'd F Y H:i') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($files)): ?>
        <tr>
            <td colspan="5">No document files</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>