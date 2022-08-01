<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Assignment Data</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_ASSIGNMENT_CREATE)): ?>
            <a href="<?= site_url('booking-assignment/create') ?>" class="btn btn-primary pull-right">
                Create Assignment
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-booking-assignment">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Booking</th>
                <th>Assigned To</th>
                <th class="type-date">Assigned At</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_ASSIGNMENT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-booking-assignment-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_ASSIGNMENT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('booking-assignment/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_ASSIGNMENT_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('booking-assignment/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Assignment"
                       data-label="{{assignment_label}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/booking-assignment.js') ?>" defer></script>