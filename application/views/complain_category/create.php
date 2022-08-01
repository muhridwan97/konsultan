<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Complain Category</h3>
    </div>
    <form action="<?= site_url('complain_category/save') ?>" class="need-validation" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('value_type') == '' ?: 'has-error'; ?>">
                        <label for="value_type">Value Type</label>
                        <select class="form-control select2" name="value_type" id="value_type"
                                data-placeholder="Select Value Type" required style="width: 100%">
                            <option value=""></option>
                            <option value="MAJOR"<?= set_select('value_type', ComplainCategoryModel::TYPE_MAJOR) ?>>MAJOR</option>
                            <option value="MINOR"<?= set_select('value_type', ComplainCategoryModel::TYPE_MINOR) ?>>MINOR</option>
                        </select>
                        <?= form_error('value_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('category_type') == '' ?: 'has-error'; ?>">
                        <label for="category_type">Category Type</label>
                        <select class="form-control select2" name="category_type" id="category_type"
                                data-placeholder="Select category Type" required style="width: 100%">
                            <option value=""></option>
                            <option value="<?= ComplainCategoryModel::CATEGORY_COMPLAIN ?>"<?= set_select('category_type', ComplainCategoryModel::CATEGORY_COMPLAIN) ?>>
                                <?= ComplainCategoryModel::CATEGORY_COMPLAIN ?>
                            </option>
                            <option value="<?= ComplainCategoryModel::CATEGORY_CONCLUSION ?>"<?= set_select('category_type', ComplainCategoryModel::CATEGORY_CONCLUSION) ?>>
                                <?= ComplainCategoryModel::CATEGORY_CONCLUSION ?>
                            </option>
                        </select>
                        <?= form_error('category_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category"
                       placeholder="Enter Category"
                       required value="<?= set_value('category') ?>">
                <?= form_error('category', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>