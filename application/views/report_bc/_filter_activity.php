<form role="form" method="get" class="form-filter" id="form-filter-container" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_activity" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">General Search</label>
                        <input type="search" value="<?= set_value('q', get_url_param('q')) ?>" class="form-control" id="search" name="q" placeholder="Type query search">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sort_by">Sort By</label>
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-control select2" id="sort_by" name="sort_by" data-placeholder="Select data sort">
                                    <option value=""></option>
                                    <option value="booking_date" <?= get_url_param('sort_by') == 'booking_date' ? 'selected' : '' ?>>BOOKING DATE</option>
                                    <option value="reference_date" <?= get_url_param('sort_by') == 'reference_date' ? 'selected' : '' ?>>DOC DATE</option>
                                    <option value="safe_conduct_date" <?= get_url_param('sort_by') == 'safe_conduct_date' ? 'selected' : '' ?>>SAFE CONDUCT DATE</option>
                                    <option value="transaction_date" <?= get_url_param('sort_by') == 'transaction_date' ? 'selected' : '' ?>>TRANSACTION DATE</option>
                                    <option value="quantity" <?= get_url_param('quantity') == 'quantity' ? 'selected' : '' ?>>ITEM QUANTITY</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control select2" id="order_method" name="order_method" data-placeholder="Order sort">
                                    <option value=""></option>
                                    <option value="desc" <?= get_url_param('order') == 'desc' ? 'selected' : '' ?>>DESC</option>
                                    <option value="asc"<?= get_url_param('order') == 'asc' ? 'selected' : '' ?>>ASC</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="owner">Owner</label>
                        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="name" data-key-label="name"
                                    name="owner[]" id="owner"
                                    data-placeholder="Select owner" multiple>
                                <option value=""></option>
                                <?php foreach ($owners as $owner): ?>
                                    <option value="<?= $owner['name'] ?>" selected>
                                        <?= $owner['name'] ?>
                                    </option>
                                <?php endforeach ?>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="container">Item</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('report_bc/ajax_get_container_and_goods_by_name') ?>"
                                        data-key-id="item_name" data-key-label="item_name"
                                        id="item" name="item[]" data-placeholder="Select item" multiple>
                                    <option value=""></option>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item['item_name'] ?>" selected>
                                            <?= $item['item_name'] ?>
                                        </option>
                                    <?php endforeach ?>
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
                            <option value="booking_date" <?= get_url_param('date_type') == 'booking_date' ? 'selected' : '' ?>>BOOKING DATE</option>
                            <option value="reference_date" <?= get_url_param('date_type') == 'reference_date' ? 'selected' : '' ?>>DOC DATE</option>
                            <option value="safe_conduct_date" <?= get_url_param('date_type') == 'safe_conduct_date' ? 'selected' : '' ?>>SAFE CONDUCT DATE</option>
                            <option value="transaction_date" <?= get_url_param('date_type') == 'transaction_date' ? 'selected' : '' ?>>TRANSACTION DATE</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
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
                        <option value="realization" <?= get_url_param('data', 'realization') == 'realization' ? 'selected' : ''  ?>>REALIZATION</option>
                        <option value="all" <?= get_url_param('data') == 'all' ? 'selected' : ''  ?>>ALL DATA</option>
                    </select>
                </div>
                <div class="col-sm-9 text-right">
                    <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
