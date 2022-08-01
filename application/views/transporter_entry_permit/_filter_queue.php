<form role="form" method="get" class="form-filter" id="form-filter-queue" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_queue" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="search">Search</label>
                <input type="search" value="<?= set_value('q', get_url_param('q')) ?>" class="form-control"
                       id="search" name="q" placeholder="Search goods or no request">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" class="form-control datepicker" id="date" name="date" autocomplete="off"
                                placeholder="Date"
                                maxlength="50" value="<?= get_url_param('filter_queue') ? set_value('date', get_url_param('date')) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="aju">Aju Reference </label>
                        <select class="form-control select2" name="aju[]" id="aju" multiple
                                data-placeholder="Select Aju Reference" style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($uploads as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= set_select("aju[]",$item['id'], in_array($item['id'], get_if_exist($_GET, 'aju', []))) ?>>
                                    <?= $item['description'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
