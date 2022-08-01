<form role="form" method="get" class="form-filter" id="form-filter-service-time" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_service_time" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">General Search</label>
                        <input type="search" value="<?= set_value('search', get_url_param('search')) ?>" class="form-control" id="search" name="search" placeholder="Type query search">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sort_by">Sort By</label>
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-control select2" id="sort_by" name="sort_by" data-placeholder="Select data sort">
                                    <option value=""></option>
                                    <option value="security_in_date" <?= get_url_param('sort_by') == 'security_in_date' ? 'selected' : '' ?>>SECURITY START</option>
                                    <option value="security_out_date" <?= get_url_param('sort_by') == 'security_out_date' ? 'selected' : '' ?>>SECURITY END</option>
                                    <option value="trucking_service_time" <?= get_url_param('sort_by') == 'trucking_service_time' ? 'selected' : '' ?>>SECURITY SERVICE TIME</option>
                                    <option value="taken_at" <?= get_url_param('sort_by') == 'taken_at' ? 'selected' : '' ?>>TALLY START</option>
                                    <option value="completed_at" <?= get_url_param('sort_by') == 'completed_at' ? 'selected' : '' ?>>TALLY END</option>
                                    <option value="tally_service_time" <?= get_url_param('sort_by') == 'tally_service_time' ? 'selected' : '' ?>>TALLY SERVICE TIME</option>
                                    <option value="gate_in_date" <?= get_url_param('sort_by') == 'gate_in_date' ? 'selected' : '' ?>>GATE START</option>
                                    <option value="gate_out_date" <?= get_url_param('sort_by') == 'gate_out_date' ? 'selected' : '' ?>>GATE END</option>
                                    <option value="gate_service_time" <?= get_url_param('sort_by') == 'gate_service_time' ? 'selected' : '' ?>>GATE SERVICE TIME</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control select2" id="order" name="order" data-placeholder="Order sort">
                                    <option value=""></option>
                                    <option value="desc" <?= get_url_param('order') == 'desc' ? 'selected' : '' ?>>LATEST</option>
                                    <option value="asc"<?= get_url_param('order') == 'asc' ? 'selected' : '' ?>>NEWEST</option>
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
                    <div class="form-group">
                        <label for="container">No Container</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                data-key-id="id" data-key-label="no_container"
                                id="container" name="container[]" data-placeholder="Select container" multiple>
                            <option value=""></option>
                            <?php foreach ($containers as $container): ?>
                                <option value="<?= $container['id'] ?>" selected>
                                    <?= $container['no_container'] ?>
                                </option>
                            <?php endforeach; ?>
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
                            <option value="security_in_date" <?= get_url_param('date_type') == 'security_in_date' ? 'selected' : '' ?>>SECURITY START</option>
                            <option value="security_out_date" <?= get_url_param('date_type') == 'security_out_date' ? 'selected' : '' ?>>SECURITY END</option>
                            <option value="taken_at" <?= get_url_param('date_type') == 'taken_at' ? 'selected' : '' ?>>TALLY START</option>
                            <option value="completed_at" <?= get_url_param('date_type') == 'completed_at' ? 'selected' : '' ?>>TALLY END</option>
                            <option value="gate_in_date" <?= get_url_param('date_type') == 'gate_in_date' ? 'selected' : '' ?>>GATE START</option>
                            <option value="gate_out_date" <?= get_url_param('date_type') == 'gate_out_date' ? 'selected' : '' ?>>GATE END</option>
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
