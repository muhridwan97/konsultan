<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Storage</h3>
    </div>

    <form action="<?= site_url('customer-storage-capacity/save') ?>" role="form" method="post" class="need-validation" id="form-customer-storage-capacity">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer" data-placeholder="Select customer" required>
                    <option value=""></option>
                    <?php if (!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('effective_date') == '' ?: 'has-error'; ?>">
                        <label for="effective_date">Effective Date</label>
                        <input type="text" class="form-control datepicker" id="effective_date" name="effective_date"
                               placeholder="Enter effective date" autocomplete="off"
                               required value="<?= set_value('effective_date') ?>">
                        <?= form_error('effective_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group <?= form_error('expired_date') == '' ?: 'has-error'; ?>">
                        <label for="expired_date">Expired Date</label>
                        <input type="text" class="form-control datepicker" id="expired_date" name="expired_date"
                               placeholder="Enter expired date" autocomplete="off"
                               required value="<?= set_value('expired_date') ?>">
                        <?= form_error('expired_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('warehouse_capacity') == '' ?: 'has-error'; ?>">
                        <label for="warehouse_capacity">Warehouse Capacity (M<sup>2</sup>)</label>
                        <input type="number" class="form-control" id="warehouse_capacity" name="warehouse_capacity"
                               placeholder="Enter warehouse capacity"
                               required maxlength="15" min="0" max="50000" value="<?= set_value('warehouse_capacity') ?>">
                        <?= form_error('warehouse_capacity', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('yard_capacity') == '' ?: 'has-error'; ?>">
                        <label for="yard_capacity">Yard Capacity (M<sup>2</sup>)</label>
                        <input type="number" class="form-control" id="yard_capacity" name="yard_capacity"
                               placeholder="Enter yard capacity"
                               required maxlength="15" min="0" max="50000" value="<?= set_value('yard_capacity') ?>">
                        <?= form_error('yard_capacity', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('covered_yard_capacity') == '' ?: 'has-error'; ?>">
                        <label for="covered_yard_capacity">Covered Yard Capacity (M<sup>2</sup>)</label>
                        <input type="number" class="form-control" id="covered_yard_capacity" name="covered_yard_capacity"
                               placeholder="Enter covered yard capacity"
                               required maxlength="15" min="0" max="50000" value="<?= set_value('covered_yard_capacity') ?>">
                        <?= form_error('covered_yard_capacity', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Storage update description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Customer Storage
            </button>
        </div>
    </form>
</div>