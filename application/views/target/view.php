<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Target</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Target Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $target['target_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= values($target['description'],'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Target</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $target['target'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Target Branch</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Branch</th>
                        <th>Target</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    foreach ($targetBranches as $targetBranch): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $targetBranch['branch_name_vms'] ?></td>
                            <td><?= if_empty($targetBranch['target'], '-') ?></td>
                            <td><?= if_empty($targetBranch['description'], '-') ?></td>
                            <td><?= readable_date($targetBranch['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($targetBranches) <= 0): ?>
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