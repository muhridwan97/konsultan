<form role="form" method="get" class="form-filter" id="form-filter-mutation-goods" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_mutation" value="1">
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
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="item">Item</label>
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
            <div class="form-group">
                <label for="booking">Booking In</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                        name="booking[]" id="booking"
                        data-placeholder="Select booking" multiple>
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>" selected>
                            <?= $booking['no_reference'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
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
            <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
