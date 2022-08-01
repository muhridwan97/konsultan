<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Uploads</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CREATE)): ?>
                <a href="<?= site_url('upload/create') ?>" class="btn btn-primary">
                    Upload Documents
                </a>
            <?php endif ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('upload/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped responsive table-ajax" id="table-upload">
            <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Type</th>
                <th rowspan="2" class="type-booking">Booking</th>
                <th rowspan="2">No Upload</th>
                <th rowspan="2">Upload Title</th>
                <th rowspan="2">Customer</th>
                <td colspan="3" class="text-center"><strong>Status Valid</strong></td>
                <th rowspan="2" style="width: 60px" class="type-action">Action</th>
            </tr>
            <tr>
                <th class="type-status-hold">Hold</th>
                <th class="type-status-upload">Status</th>
                <th class="type-status-docs">Docs</th>
            </tr>
            </thead>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<script id="control-upload-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-upload"
            data-id="{{id}}"
            data-label="{{description}}">

            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VIEW)): ?>
                <li>
                    <a href="<?= site_url('upload/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Documents
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                <li class="action-validate {{validate_upload_disable}}">
                    <a class="btn-validate-upload"
                       data-toggle="{{validate_upload_tooltip}}"
                       title="{{validate_upload_title}}"
                       href="<?= site_url('upload/validate/{{id}}') ?>"
                       data-url-view="<?= site_url('upload/view/{{id}}') ?>">
                        <i class="fa ion-checkmark"></i>Validate Upload
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_EDIT)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('upload/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit Title
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_EDIT_UPLOAD_IN)): ?>
                <li class="{{edit_upload_disable}}">
                    <a href="<?= site_url('upload/edit-upload-in/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit Reference
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                <li>
                    <a href="<?= site_url('upload/response/{{id}}') ?>">
                        <i class="fa ion-reply-all"></i>Give Response
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized([PERMISSION_UPLOAD_CREATE, PERMISSION_UPLOAD_VALIDATE])): ?>
                <li class="action-hold">
                    <a href="<?= site_url('upload/hold/{{id}}') ?>" class="btn-validate"
                       data-validate="hold" data-label="{{no_upload}}">
                        <i class="fa fa-close"></i> Hold
                    </a>
                </li>
                <li class="action-release">
                    <a href="<?= site_url('upload/release/{{id}}') ?>" class="btn-validate"
                       data-validate="release" data-label="{{no_upload}}">
                        <i class="fa fa-refresh"></i> Release
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('upload/delete/{{id}}') ?>" class="btn-delete" data-title="Upload" data-label="{{no_upload}}">
                        <i class="fa ion-trash-a"></i>Delete
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_UPLOAD_VALIDATE, PERMISSION_UPLOAD_CREATE])): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete') ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-upload">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validating Upload</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate upload document number
                            <strong id="upload-title"></strong>?
                        </p>
                        <p class="text-danger">
                            Approving this upload will open it's permission for booking process.
                            <!--Validating upload currently can be triggered by upload & validate SPPB document.-->
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <!--<a href="#" id="link-view-upload" class="btn btn-success">Validate SPPB</a>-->
                        <button type="submit" class="btn btn-danger" name="status" value="0">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="1">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<script src="<?= base_url('assets/app/js/upload.js?v=15') ?>" defer></script>
