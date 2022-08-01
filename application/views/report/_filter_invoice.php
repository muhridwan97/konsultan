<form role="form" method="get" class="form-filter" id="form-filter-invoice" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_invoice" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">General Search</label>
                        <input type="search" value="<?= get_url_param('filter_invoice') ? set_value('search', get_url_param('search')) : '' ?>" class="form-control" id="search" name="search" placeholder="Type query search">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="invoice_type">Invoice Type</label>
                    <select class="form-control select2" id="invoice_type" name="invoice_type" data-placeholder="Select invoice type">
                        <option value=""></option>
                        <option value="BOOKING FULL" <?= get_url_param('filter_invoice') ? get_url_param('invoice_type') == 'BOOKING FULL' ? 'selected' : '' : '' ?>>BOOKING FULL</option>
                        <option value="HANDLING" <?= get_url_param('filter_invoice') ? get_url_param('invoice_type') == 'HANDLING' ? 'selected' : '' : '' ?>>HANDLING</option>
                        <option value="WORK ORDER" <?= get_url_param('filter_invoice') ? get_url_param('invoice_type') == 'WORK ORDER' ? 'selected' : '' : '' ?>>WORK ORDER</option>
                    </select>
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
                    <div class="row">
                        <div class="col-md-6">
                            <label for="date_type">Date From</label>
                            <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                   placeholder="Date from"
                                   maxlength="50" value="<?= get_url_param('filter_invoice') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="date_type">Date To</label>
                            <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                   placeholder="Date to"
                                   maxlength="50" value="<?= get_url_param('filter_invoice') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
