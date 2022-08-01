<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Containers Age</h3>
        <div class="pull-right">
            <a href="#filter_age_container" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_age_container', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="filter_age_container" <?= isset($_GET['filter_age_container']) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_age_container" value="1">
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
                                            <option value="<?= $owner['id'] ?>" <?= set_select('owner', $owner['id'], get_url_param('filter_age_container') ? in_array($owner['id'], get_url_param('owner', [])) : false) ?>>
                                                <?= $owner['name'] ?>
                                            </option>
                                        <?php endforeach ?>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="day_from">Age from</label>
                                        <input type="number" class="form-control" value="<?= set_value('day_from', get_url_param('filter_age_container') ? get_url_param('day_from') : '') ?>"
                                               name="day_from" id="day_from" min="0" placeholder="Age since day">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="day_to">Age to</label>
                                        <input type="number" class="form-control" value="<?= set_value('age_to', get_url_param('filter_age_container') ? get_url_param('day_to') : '') ?>"
                                               name="day_to" id="day_to" min="0" placeholder="Age to day">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped no-wrap table-responsive responsive">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Container Size</th>
                <th>Today</th>
                <th>1 - 30</th>
                <th>31 - 60</th>
                <th>61 - 90</th>
                <th>> 90</th>
                <th>
                    Age Filter
                    <?php if(!empty(get_url_param('filter_age_container'))): ?>
                        <?php if(!empty(get_url_param('day_from')) && !empty(get_url_param('day_to'))): ?>
                            <?= if_empty(get_url_param('day_from'), '0') ?>
                            -
                            <?= if_empty(get_url_param('day_to'), 'end') ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($reportContainers as $container): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $container['container_size'] ?> Feet</td>
                    <td><?= numerical($container['age_today'], 0, true) ?></td>
                    <td><?= numerical($container['age_1_30'], 0, true) ?></td>
                    <td><?= numerical($container['age_31_60'], 0, true) ?></td>
                    <td><?= numerical($container['age_61_90'], 0, true) ?></td>
                    <td><?= numerical($container['age_more_than_90'], 0, true) ?></td>
                    <td><?= key_exists('age_filter', $container) ? numerical($container['age_filter'], 0, true) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Goods Age</h3>
        <div class="pull-right">
            <a href="#filter_age_goods" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_age_goods', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="filter_age_goods" <?= isset($_GET['filter_age_goods']) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_age_goods" value="1">
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
                                            <option value="<?= $owner['id'] ?>" <?= set_select('owner', $owner['id'], get_url_param('filter_age_goods') ? in_array($owner['id'], get_url_param('owner', [])) : false) ?>>
                                                <?= $owner['name'] ?>
                                            </option>
                                        <?php endforeach ?>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="day_from">Age from</label>
                                        <input type="number" class="form-control" value="<?= set_value('day_from', get_url_param('filter_age_goods') ? get_url_param('day_from') : '') ?>"
                                               name="day_from" id="day_from" min="0" placeholder="Age since day">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="day_to">Age to</label>
                                        <input type="number" class="form-control" value="<?= set_value('age_to', get_url_param('filter_age_goods') ? get_url_param('day_to') : '') ?>"
                                               name="day_to" id="day_to" min="0" placeholder="Age to day">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped table-responsive table-ajax responsive" id="table-aging-goods">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Goods</th>
                <th>Unit</th>
                <th class="no-wrap type-number">Today</th>
                <th class="no-wrap type-number">1 - 30</th>
                <th class="no-wrap type-number">31 - 60</th>
                <th class="no-wrap type-number">61 - 90</th>
                <th class="no-wrap type-number">> 90</th>
                <th class="no-wrap type-number">
                    Age Filter
                    <?php if(!empty(get_url_param('filter_age_goods'))): ?>
                        <?php if(!empty(get_url_param('day_from')) && !empty(get_url_param('day_to'))): ?>
                            <?= if_empty(get_url_param('day_from'), '0') ?>
                            -
                            <?= if_empty(get_url_param('day_to'), 'end') ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>