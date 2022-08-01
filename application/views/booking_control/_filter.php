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
                        <label for="status">Status</label>
                        <select class="form-control select2" id="status" name="status[]" data-placeholder="Select status" multiple>
                            <option value="OUTSTANDING" <?= in_array('OUTSTANDING', get_url_param('status', [])) ? 'selected' : '' ?>>
                                OUTSTANDING
                            </option>
                            <option value="PENDING" <?= in_array('PENDING', get_url_param('status', [])) ? 'selected' : '' ?>>
                                PENDING
                            </option>
                            <option value="CANCELED" <?= in_array('CANCELED', get_url_param('status', [])) ? 'selected' : '' ?>>
                                CANCELED
                            </option>
                            <option value="DRAFT" <?= in_array('DRAFT', get_url_param('status', [])) ? 'selected' : '' ?>>
                                DRAFT
                            </option>
                            <option value="DONE" <?= in_array('DONE', get_url_param('status', [])) ? 'selected' : '' ?>>
                                DONE
                            </option>
                            <option value="CLEAR" <?= in_array('CLEAR', get_url_param('status', [])) ? 'selected' : '' ?>>
                                CLEAR
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
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
        <div class="panel-footer text-right">
            <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
