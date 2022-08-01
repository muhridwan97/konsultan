<form role="form" method="get" class="form-filter" id="form-filter-goods" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_goods" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="search">General Search</label>
                <input type="search" value="<?= get_url_param('filter_goods') ? set_value('search', get_url_param('search')) : '' ?>" class="form-control" id="search" name="search" placeholder="Type query search">
            </div>
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
                                <?php if (get_url_param('filter_goods')): ?>
                                    <?php foreach ($owners as $owner): ?>
                                        <option value="<?= $owner['id'] ?>" selected>
                                            <?= $owner['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                <?php endif; ?>
                            </select>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= UserModel::authenticatedUserData('name') ?>
                                (<?= UserModel::authenticatedUserData('email') ?>)
                            </p>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="goods">Goods</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                data-key-id="id" data-key-label="name"
                                name="goods[]" id="goods"
                                data-placeholder="Select goods" multiple>
                            <option value=""></option>
                            <?php foreach ($goods as $item): ?>
                                <option value="<?= $item['id'] ?>" selected>
                                    <?= $item['name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_type">Transaction Date</label>
                        <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                            <option value=""></option>
                            <option value="bookings.booking_date" <?= get_url_param('filter_goods') ? get_url_param('date_type') == 'booking_date' ? 'selected' : '' : '' ?>>BOOKING DATE</option>
                            <option value="security_in_date" <?= get_url_param('filter_goods') ? get_url_param('date_type') == 'security_in_date' ? 'selected' : '' : '' ?>>SECURITY IN DATE</option>
                            <option value="security_out_date" <?= get_url_param('filter_goods') ? get_url_param('date_type') == 'security_out_date' ? 'selected' : '' : '' ?>>SECURITY OUT DATE</option>
                            <option value="taken_at" <?= get_url_param('filter_goods') ? get_url_param('date_type') == 'taken_at' ? 'selected' : '' : '' ?>>TALLY START</option>
                            <option value="completed_at" <?= get_url_param('filter_goods') ? get_url_param('date_type') == 'completed_at' ? 'selected' : '' : '' ?>>TALLY END</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_from">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from" autocomplete="off"
                                       maxlength="50" value="<?= get_url_param('filter_goods') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_to">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to" autocomplete="off"
                                       maxlength="50" value="<?= get_url_param('filter_goods') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-3">
                    <select class="form-control select2" id="data" name="data">
                        <option value="realization" <?= get_url_param('filter_goods') ? get_url_param('data', 'realization') == 'realization' ? 'selected' : '' : '' ?>>REALIZATION</option>
                        <option value="all" <?= get_url_param('filter_goods') ? get_url_param('data') == 'all' ? 'selected' : '' : ''  ?>>ALL DATA</option>
                    </select>
                </div>
                <div class="col-sm-9 text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
