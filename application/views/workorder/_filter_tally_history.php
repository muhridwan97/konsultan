<form role="form" method="get" class="form-filter" id="form-filter-tally-history" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_tally_history" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                    name="customer[]" id="customer"
                                    data-placeholder="Select Customer" multiple>
                                <option value=""></option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" selected>
                                        <?= $customer['name'] ?>
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
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('handling_type/ajax_get_handling_types') ?>" data-key-id="id" data-key-label="handling_type" name="handling_type[]" id="handling_type"
                                data-placeholder="Select handling type" multiple>
                            <option value=""></option>
                            <?php foreach ($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>" selected>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_type">Date Type</label>
                        <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                            <option value=""></option>
                            <option value="job_date" <?= get_url_param('filter_tally_history') ? get_url_param('date_type') == 'job_date' ? 'selected' : '' : '' ?>>JOB DATE</option>
                            <option value="update_job_date" <?= get_url_param('filter_tally_history') ? get_url_param('date_type') == 'update_job_date' ? 'selected' : '' : '' ?>>UPDATE JOB DATE</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= get_url_param('filter_tally_history') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= get_url_param('filter_tally_history') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="people">Update By</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people_branch') ?>"
                                data-key-id="id" data-key-label="name"
                                name="people[]" id="people"
                                data-placeholder="Select People" multiple>
                            <option value=""></option>
                            <?php foreach ($peoples as $people): ?>
                                <option value="<?= $people['id'] ?>" selected>
                                    <?= $people['name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="gate_status">Gate Status</label>
                    <select class="form-control select2" id="gate_status" name="gate_status" data-placeholder="Select date filter">
                        <option value="">ALL</option>
                        <option value="0" <?= get_url_param('filter_tally_history') ? get_url_param('gate_status') == '0' ? 'selected' : '' : '' ?>>ALL</option>
                        <option value="checkout" <?= get_url_param('filter_tally_history') ? get_url_param('gate_status') == 'checkout' ? 'selected' : '' : '' ?>>CHECK OUT</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter-tally-history">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
