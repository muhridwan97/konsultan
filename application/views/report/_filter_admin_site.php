<form role="form" method="get" class="form-filter" id="form-filter-admin-site" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_admin_site" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="customer">PIC Name</label>
                        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2"name="pic[]" id="pic"
                                    data-placeholder="Select PIC" multiple>
                                <option value=""></option>
                                <?php if(isset($pic) && !empty($pic)): ?>
                                    <?php foreach ($pic as $dt_pic): ?>
                                        <?php foreach ($allPIC as $all_PIC): ?>
                                        <option value="<?= $all_PIC['id'] ?>" <?= set_select('pic', $all_PIC['id'], $all_PIC['id'] == $dt_pic['id']) ?>>
                                            <?= $all_PIC['name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                        <?php foreach ($allPIC as $all_PIC): ?>
                                        <option value="<?= $all_PIC['id'] ?>">
                                            <?= $all_PIC['name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= UserModel::authenticatedUserData('name') ?>
                                (<?= UserModel::authenticatedUserData('email') ?>)
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= get_url_param('filter_admin_site') ? set_value('date_from', get_url_param('date_from')) : date('01 F Y') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= get_url_param('filter_admin_site') ? set_value('date_to', get_url_param('date_to')) : date('t F Y') ?>">
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
