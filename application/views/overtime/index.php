<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Overtime</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_OVERTIME_VIEW)): ?>
                <a href="<?= site_url('overtime/create') ?>" class="btn btn-primary">
                    Create Overtime
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-overtime">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Overtime</th>
                <th>Name Of Day</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($overtimes as $overtime): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $overtime['no_overtime'] ?></td>
                    <td><?= $overtime['name_of_day'] ?></td>
                    <td><?= format_date($overtime['created_at'], 'd F Y') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OVERTIME_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('overtime/view/' . $overtime['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OVERTIME_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('overtime/edit/' . $overtime['id']) ?>">
                                            <i class="fa ion-compose"></i> Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OVERTIME_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('overtime/delete/' . $overtime['id']) ?>" class="btn-delete"
                                           data-id="<?= $overtime['id'] ?>"
                                           data-title="overtime"
                                           data-label="<?= $overtime['name_of_day'] . ' ('.$overtime['no_overtime'].')' ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_OVERTIME_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>