<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking News</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_CREATE)): ?>
            <a href="<?= site_url('booking_news/create') ?>" class="btn btn-primary pull-right">
                Create Booking News
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-booking-news">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No BA</th>
                <th>BA Date</th>
                <th>No Sprint</th>
                <th>Sprint Date</th>
                <th>Type</th>
                <th>TPS</th>
                <th>Total</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($bookingNews as $bookingNew): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $bookingNew['no_booking_news'] ?></td>
                    <td><?= (new DateTime($bookingNew['booking_news_date']))->format('d M Y') ?></td>
                    <td><?= $bookingNew['no_sprint'] ?></td>
                    <td class="responsive-hide"><?= (new DateTime($bookingNew['sprint_date']))->format('d M Y') ?></td>
                    <td><?= $bookingNew['type'] ?></td>
                    <td><?= $bookingNew['tps'] ?></td>
                    <td><?= $bookingNew['total_booking'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('booking_news/view/' . $bookingNew['id']) ?>">
                                            <i class="fa ion-search"></i>View Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= site_url('booking_news/print_report/' . $bookingNew['id'] . '?format=excel') ?>">
                                            <i class="fa fa-print"></i>Print Report
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('booking_news/edit/' . $bookingNew['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('booking_news/delete/' . $bookingNew['id']) ?>"
                                           class="btn-delete-booking-news"
                                           data-id="<?= $bookingNew['id'] ?>"
                                           data-label="<?= $bookingNew['no_booking_news'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-booking-news">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Booking News</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete booking news
                            <strong id="booking-news-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Booking News</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/booking_news.js') ?>" defer></script>
