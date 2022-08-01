<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <?php if (UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                    data-add-all-customer="1"
                                    name="customer" id="customer"
                                    data-placeholder="Select customer">
                                <option value=""></option>
                                <?php if (!empty($selectedCustomer)): ?>
                                    <option value="<?= $selectedCustomer['id'] ?>" selected>
                                        <?= $selectedCustomer['name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        <?php else: ?>
                            <p class="form-control-static"><?= UserModel::authenticatedUserData('name') ?></p>
                            <input type="hidden" name="customer" id="customer" value="<?= UserModel::authenticatedUserData('id_person') ?>">
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select class="form-control select2" name="priority" id="priority" data-placeholder="Priority">
                            <?php
                            $filterPriorities = [
                                'Top Urgent',
                                '1st',
                                '2nd',
                                '3rd',
                            ];
                            ?>
                            <option value="0">ALL PRIORITY</option>
                            <?php foreach ($filterPriorities as $filterPriority): ?>
                                <option value="<?= $filterPriority ?>"<?= set_select('priority', $filterPriority, get_url_param('priority') == $filterPriority) ?>>
                                    <?= $filterPriority ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="panel-footer text-right">
            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
