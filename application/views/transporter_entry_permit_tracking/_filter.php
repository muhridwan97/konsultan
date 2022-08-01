<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control select2" name="status" id="status" data-placeholder="Tracking status">
                                <?php
                                $filterStatuses = [
                                    TransporterEntryPermitTrackingModel::STATUS_LINKED,
                                    TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT,
                                    TransporterEntryPermitTrackingModel::STATUS_UNLOADED,
                                ];

                                $defaultStatus = 'NOT LINKED';
                                if (!AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)) {
                                    $defaultStatus = 'LINKED DATA';
                                }
                                ?>
                                <option value="ALL"<?= get_url_param('status') == 'ALL' ? ' selected' : '' ?>>ALL STATUS</option>
                                <option value="NOT LINKED"<?= get_url_param('status', $defaultStatus) == 'NOT LINKED' ? ' selected' : '' ?>>NOT LINKED</option>
                                <option value="LINKED DATA"<?= get_url_param('status', $defaultStatus) == 'LINKED DATA' ? ' selected' : '' ?>>LINKED DATA (ALL)</option>
                                <?php foreach ($filterStatuses as $filterStatus): ?>
                                    <option value="<?= $filterStatus ?>"<?= set_select('status', $filterStatus, get_url_param('status') == $filterStatus) ?>>
                                        &nbsp; - <?= $filterStatus ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label for="date_from">TEP Date From</label>
                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                               placeholder="Date from" autocomplete="off"
                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label for="date_to">TEP Date To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Date to" autocomplete="off"
                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
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
