<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Types</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_CREATE)): ?>
                <a href="<?= site_url('booking-type/create') ?>" class="btn btn-primary">
                    Create Booking Type
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-booking-type">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Booking Type</th>
                <th>Category</th>
                <th>Type</th>
                <th>DO By</th>
                <th>Main Document</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($bookingTypes as $bookingType): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $bookingType['booking_type'] ?></td>
                    <td><?= $bookingType['category'] ?></td>
                    <td><?= $bookingType['type'] ?></td>
                    <td><?= $bookingType['with_do'] ? 'INTERNAL' : 'EXTERNAL' ?></td>
                    <td><?= $bookingType['default_document'] ?></td>
                    <td><?= if_empty($bookingType['description'], 'No description') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('booking-type/view/' . $bookingType['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('booking-type/edit/' . $bookingType['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= site_url('booking-type/set-module-import/' . $bookingType['id']) ?>">
                                            <i class="fa ion-compose"></i>Set module import
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('booking-type/delete/' . $bookingType['id']) ?>"
                                           class="btn-delete"
                                           data-id="<?= $bookingType['id'] ?>"
                                           data-title="Booking Type"
                                           data-label="<?= $bookingType['booking_type'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/booking_type.js') ?>" defer></script>