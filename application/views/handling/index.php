<div class="box box-primary" id="handling-list">
    <div class="box-header with-border">
        <h3 class="box-title">Handling</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_CREATE)): ?>
                <a href="<?= site_url('handling/create') ?>" class="btn btn-primary">
                    Create Handling
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" data-page-length='25' id="table-handling">
            <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th>Customer</th>
                <th>No Handling</th>
                <th>Type</th>
                <th>Handling Date</th>
                <th>Remaining</th>
                <th id="handling-status">Status</th>
                <th style="width: 60px" id="handling-action">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-approve-handling">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Approve Handling</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to
                            <strong class="text-success">Approve</strong> handling
                            <strong id="handling-title"></strong>?
                        </p>
                        <p class="small text-danger">
                            Approving handling request allow proceed to creating job sheet.
                        </p>
                        <p>
                            We will notify customer <strong id="handling-customer"></strong>
                            with email address <strong id="handling-email"></strong>.
                        </p>
                        <div id="component-wrapper">Fetching handling component...</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" disabled>Approve Handling</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-reject-handling">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Reject Handling</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to <strong class="text-danger">Reject</strong> handling
                            <strong id="handling-title"></strong>?</p>
                        <p class="small text-danger">
                            Rejecting handling request freeze the process to creating job sheet.
                        </p>
                        <p>
                            We will notify customer <strong id="handling-customer"></strong>
                            with email address <strong id="handling-email"></strong>.
                        </p>
                        <div class="form-group">
                            <label for="reason" class="control-label">Reason</label>
                            <textarea name="reason" id="reason" cols="30" rows="3" required
                                      class="form-control" placeholder="Input reason rejection"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Reject Handling</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-handling">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Handling</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete handling
                            <strong id="handling-title"></strong>?</p>
                        <p class="small text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Handling</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script id="control-handling-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right"
            data-id="{{id}}"
            data-id-handling-type="{{id_handling_type}}"
            data-customer="{{customer_name}}"
            data-email="{{customer_email}}"
            data-label="{{no_handling}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_VIEW)): ?>
                <li>
                    <a href="<?= site_url('handling/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_PRINT)): ?>
                <li>
                    <a href="<?= site_url('handling/print_handling/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Handling
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_VALIDATE)): ?>
                <li role="separator" class="divider"></li>
                <li class="{{allow_approve}}">
                    <a href="<?= site_url('handling/validate/approve/{{id}}') ?>"
                       class="btn-approve-handling">
                        <i class="fa ion-checkmark"></i> Approve
                    </a>
                </li>

                <li class="{{allow_reject}}">
                    <a href="<?= site_url('handling/validate/reject/{{id}}') ?>"
                       class="btn-reject-handling">
                        <i class="fa ion-close"></i> Reject
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('handling/validate/{{resend_label}}/{{id}}') ?>"
                       class="btn-{{resend_label}}-handling">
                        <i class="fa ion-android-send"></i>
                        Resend validation
                    </a>
                </li>
                <li role="separator" class="divider"></li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_EDIT)): ?>
                <!--<li class="{{allow_edit}}">
                    <a href="<?= site_url('handling/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>-->
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_DELETE)): ?>
                <li role="separator" class="divider {{allow_delete}}"></li>
                <li class="{{allow_delete}}">
                    <a href="<?= site_url('handling/delete/{{id}}') ?>" class="btn-delete-handling">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/handling.js?v=3') ?>" defer></script>