<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Booking News</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_NEWS_EDIT)): ?>
            <a href="<?= site_url('booking_news/edit/' . $bookingNews['id']) ?>" class="btn btn-primary pull-right">
                Edit Booking News
            </a>
        <?php endif; ?>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No BA</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['no_booking_news'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">BA Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= (new DateTime($bookingNews['booking_news_date']))->format('d F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No SPRINT</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['no_sprint'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">SPRINT Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= (new DateTime($bookingNews['sprint_date']))->format('d F Y') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">TPS</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['tps'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Chief Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['chief_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Chief NIP</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $bookingNews['chief_nip'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($bookingNews['created_at']) ?>
                                by <?= if_empty($bookingNews['creator_name'], 'Unknown') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($bookingNews['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking News Detail</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No BC</th>
                        <th>No Container</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>DOG</th>
                        <th>Condition</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($bookingNewsDetails as $bookingNewsDetail): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $bookingNewsDetail['no_booking'] ?></td>
                            <td><?= if_empty($bookingNewsDetail['no_container'], '-') ?></td>
                            <td><?= if_empty($bookingNewsDetail['type'], '-') ?></td>
                            <td><?= if_empty($bookingNewsDetail['size'], '-') ?></td>
                            <td><?= if_empty($bookingNewsDetail['dog'], 'No DOG') ?></td>
                            <td><?= if_empty($bookingNewsDetail['condition'], '-') ?></td>
                            <td><?= if_empty($bookingNewsDetail['description'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($bookingNewsDetails)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void()" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>