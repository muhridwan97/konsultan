<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Rating</h3>
        <div class="pull-right">
            <a href="#form-filter-booking-rating" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_booking_rating', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-booking-rating" <?= !isset($_GET['filter_booking_rating']) ? 'style="display:none"' : ''  ?>>
            <input type="hidden" name="filter_booking_rating" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner">Owner</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                                    <select class="form-control select2 select2-ajax"
                                            data-url="<?= site_url('people/ajax_get_people') ?>"
                                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                            name="owner[]" id="owner"
                                            data-placeholder="Select owner" multiple>
                                        <option value=""></option>
                                        <?php foreach ($owners as $owner): ?>
                                            <option value="<?= $owner['id'] ?>" selected>
                                                <?= $owner['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                        (<?= UserModel::authenticatedUserData('email') ?>)
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="is_completed">Is Completed</label>
                                    <select class="form-control select2" id="is_completed" name="is_completed" data-placeholder="COMPLETED">
                                        <option value="1" <?= get_url_param('is_completed', '-1') == "1" ? 'selected' : '' ?>>YES</option>
                                        <option value="0" <?= get_url_param('is_completed', '-1') == "0" ? 'selected' : '' ?>>NO</option>
                                        <option value="-1" <?= get_url_param('is_completed', '-1') == "-1" ? 'selected' : '' ?>>BOTH</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="is_sppb">Is SPPB</label>
                                    <select class="form-control select2" id="is_sppb" name="is_sppb" data-placeholder="SPPB">
                                        <option value="1" <?= get_url_param('is_sppb', '-1') == "1" ? 'selected' : '' ?>>YES</option>
                                        <option value="0" <?= get_url_param('is_sppb', '-1') == "0" ? 'selected' : '' ?>>NO</option>
                                        <option value="-1" <?= get_url_param('is_sppb', '-1') == "-1" ? 'selected' : '' ?>>BOTH</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="category">Category</label>
                                <select class="form-control select2" id="category" name="category" data-placeholder="Select category">
                                    <option value=""></option>
                                    <option value="INBOUND" <?= get_url_param('category') == 'INBOUND' ? 'selected' : '' ?>>INBOUND</option>
                                    <option value="OUTBOUND" <?= get_url_param('category') == 'OUTBOUND' ? 'selected' : '' ?>>OUTBOUND</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="star_from">Star From</label>
                                        <select class="form-control select2" id="star_from" name="star_from" data-placeholder="Star from">
                                            <option value=""></option>
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>" <?= get_url_param('star_from') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="star_to">Star To</label>
                                        <select class="form-control select2" id="star_to" name="star_to" data-placeholder="Star to">
                                            <option value=""></option>
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>" <?= get_url_param('star_to') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Transaction Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value=""></option>
                                    <option value="completed_date" <?= get_url_param('date_type') == 'completed_date' ? 'selected' : '' ?>>COMPLETED DATE</option>
                                    <option value="rated_at" <?= get_url_param('date_type') == 'rated_at' ? 'selected' : '' ?>>RATING DATE</option>
                                    <option value="sppb_date" <?= get_url_param('date_type') == 'sppb_date' ? 'selected' : '' ?>>SPPB DATE</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Date from"
                                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Date to"
                                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped no-wrap table-ajax table-responsive" id="table-report-booking-rating">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th>SPPB Date</th>
                <th>Completed Date</th>
                <th>Type</th>
                <th>Rating Date</th>
                <th class="type-rating">Rating</th>
                <th>Description</th>
            </tr>
            </thead>
        </table>

        <div class="row mt20">
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Completed / SPPB</p>
                        <h3 class="mt0">
                            <a href="<?= site_url('report/outstanding-completed?' . $_SERVER['QUERY_STRING']) ?>"
                               data-toggle="tooltip" title="Show <?= ($totalSppb - $totalCompletedSppb) < 0 ? 0 : ($totalSppb - $totalCompletedSppb) ?> SPPB booking does not completed yet">
                                <?= numerical($totalCompletedSppb, 0, true) ?>
                                /
                                <?= numerical($totalSppb, 0, true) ?>
                            </a>
                            <small>bookings</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Rated / Completed</p>
                        <h3 class="mt0">
                            <a href="<?= site_url('report/outstanding-rating?' . $_SERVER['QUERY_STRING']) ?>"
                               data-toggle="tooltip" title="Show <?= ($totalCompleted - $totalRated) < 0 ? 0 : ($totalCompleted - $totalRated) ?> completed booking does not rated yet">
                                <?= numerical($totalRated, 0, true) ?>
                                /
                                <?= numerical($totalCompleted, 0, true) ?>
                            </a>
                            <small>bookings</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Rating Outstanding</p>
                        <h3 class="mt0">
                            <?= numerical($totalOutstanding, 0, true) ?> <small>bookings</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">Average Rated</p>
                        <h3 class="mt0">
                            <?= numerical($totalAverage, 2, true) ?>
                            <small>
                                <i class="fa fa-star-o"></i> stars
                            </small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
