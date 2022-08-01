<form role="form" method="get" class="form-filter" id="form-filter-outbound-progress" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_outbound_progress" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
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
            <div class="form-group">
                <label for="branch">Branch (Default Current Branch)</label>
                <select class="form-control select2" name="branch[]" id="branch" data-placeholder="Select branch" multiple>
                    <option value=""></option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= in_array($branch['id'], get_url_param('branch', [])) ? 'selected' : '' ?>>
                            <?= $branch['branch'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_type">Transaction Date</label>
                        <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                            <option value=""></option>
                            <option value="uploads.created_at"<?= get_url_param('date_type') == 'uploads.created_at' ? ' selected' : '' ?>>UPLOAD DATE</option>
                            <option value="draft_documents.draft_date"<?= get_url_param('date_type') == 'draft_documents.draft_date' ? ' selected' : '' ?>>DRAFT DATE</option>
                            <option value="confirmation_documents.confirmation_date"<?= get_url_param('date_type') == 'confirmation_documents.confirmation_date' ? ' selected' : '' ?>>CONFIRMATION DATE</option>
                            <option value="sppb_documents.sppb_date"<?= get_url_param('date_type') == 'sppb_documents.sppb_date' ? ' selected' : '' ?>>SPPB DATE</option>
                            <option value="sppd_documents.sppd_date"<?= get_url_param('date_type') == 'sppd_documents.sppd_date' ? ' selected' : '' ?>>SPPD DATE</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_from">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= get_url_param('filter_outbound_progress') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_to">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= get_url_param('filter_outbound_progress') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-3 col-md-2">
                    <select class="form-control select2" id="data" name="data" aria-label="data">
                        <option value="0">ALL</option>
                        <option value="not_sppd" <?= get_url_param('data') == 'not_sppd' ? 'selected' : ''  ?>>NOT SPPD</option>
                        <option value="sppd" <?= get_url_param('data') == 'sppd' ? 'selected' : ''  ?>>SPPD</option>
                    </select>
                </div>
                <div class="col-sm-9 col-md-10 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
