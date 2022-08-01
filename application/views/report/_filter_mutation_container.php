<form role="form" method="get" class="form-filter" id="form-filter-mutation-container" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_container" value="1">
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
        <div class="panel-footer text-right">
            <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
