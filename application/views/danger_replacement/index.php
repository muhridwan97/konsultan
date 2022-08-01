<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Danger Replacement</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_DANGER_REPLACEMENT_CREATE)): ?>
            <div class="pull-right">
                <a href="<?= site_url('danger_replacement/create') ?>" class="btn btn-primary">
                    Replace Danger Status
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-danger-replacement">
            <thead>
            <tr>
                <th>No</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>Replace Danger To</th>
                <th>Created At</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-danger-replacement-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DANGER_REPLACEMENT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('danger_replacement/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Detail
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DANGER_REPLACEMENT_VALIDATE)): ?>
                <li class="validate">
                    <a href="<?= site_url('danger_replacement/validate_danger_replacement/{{id}}') ?>"
                       class="btn-validate-danger-replacement"
                       data-id="{{id}}"
                       data-label="{{no_booking}}">
                        <i class="fa ion-checkmark"></i>Validate
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DANGER_REPLACEMENT_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-danger-replacement">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validating Danger Replacement</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate danger replacement booking no
                            <strong id="danger-replacement-title"></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="REJECTED">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="APPROVED">Approve and Commit New Status</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<script src="<?= base_url('assets/app/js/danger_replacement.js?v=2') ?>" defer></script>