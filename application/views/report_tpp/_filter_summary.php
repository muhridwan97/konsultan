<?php $filterSummary = isset($filter_summary) ? $filter_summary : '' ?>
<form role="form" method="get" class="form-filter" id="<?= $filterSummary ?>" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="<?= $filterSummary ?>" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="owner">Owner</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                                    <select class="form-control select2 select2-ajax"
                                            data-url="<?= site_url('people/ajax_get_people') ?>"
                                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                            name="owner[]" id="owner"
                                            data-placeholder="Select owner" multiple>
                                        <option value=""></option>
                                        <?php if(!empty($owners)): ?>
                                            <?php foreach ($owners as $owner): ?>
                                                <option value="<?= $owner['id'] ?>" selected>
                                                    <?= $owner['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="owner">Booking</label>
                                <select class="form-control select2" id="booking" name="booking[]" data-placeholder="Select booking" multiple>
                                    <option value=""></option>
                                    <?php foreach ($bookings as $booking): ?>
                                        <option value="<?= $booking['id_booking'] ?>" <?= in_array($booking['id_booking'], get_url_param('booking', [])) && get_url_param($filterSummary) ? 'selected' : '' ?>>
                                            <?= $booking['no_booking'] ?> - <?= $booking['no_reference'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="container_size">Container Size</label>
                        <select class="form-control select2" id="container_size" name="container_size[]" data-placeholder="Select container size" multiple>
                            <option value=""></option>
                            <option value="20" <?= in_array(20, get_url_param('container_size', [])) ? 'selected' : '' ?>>
                                20 Feet
                            </option>
                            <option value="40" <?= in_array(40, get_url_param('container_size', [])) ? 'selected' : '' ?>>
                                40 Feet
                            </option>
                            <option value="45" <?= in_array(45, get_url_param('container_size', [])) ? 'selected' : '' ?>>
                                45 Feet
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="out_date">From Date (Outbound)</label>
                                <input type="text" class="form-control datepicker" value="<?= set_value('out_date', get_url_param('out_date')) ?>"
                                       name="out_date" id="out_date" placeholder="Stock outbound date">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="stock_date">To Date (Stock)</label>
                                <input type="text" class="form-control datepicker" value="<?= set_value('stock_date', get_url_param('stock_date')) ?>"
                                       name="stock_date" id="stock_date" placeholder="Stock until date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse">Warehouse</label>
                                <select class="form-control select2" id="warehouse" name="warehouse" data-placeholder="Select warehouse">
                                    <option value=""></option>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse['id'] ?>" <?= get_url_param('warehouse', []) && get_url_param($filterSummary) ? 'selected' : '' ?>>
                                            <?= $warehouse['warehouse'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="age">Range Age</label>
                                <select class="form-control select2" id="age" name="age" data-placeholder="Select age range">
                                    <option value="ALL"<?= set_select('age', 'ALL', get_url_param('age', 'ALL') == 'ALL') ?>>ALL</option>
                                    <option value="GROWTH"<?= set_select('age', 'GROWTH', get_url_param('age') == 'GROWTH') ?>>GROWTH (0 - 365 Days)</option>
                                    <option value="SLOW GROWTH"<?= set_select('age', 'SLOW GROWTH', get_url_param('age') == 'SLOW GROWTH') ?>>SLOW GROWTH (366 - 730 Days)</option>
                                    <option value="NO GROWTH"<?= set_select('age', 'NO GROWTH', get_url_param('age') == 'NO GROWTH') ?>>NO GROWTH (>= 731 Days)</option>
                                </select>
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
                        <option value="all" <?= get_url_param('data', 'all') == 'all' ? 'selected' : ''  ?>>ALL DATA</option>
                        <option value="stock" <?= get_url_param('data') == 'stock' ? 'selected' : ''  ?>>STOCK ONLY</option>
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
