<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Item</h3>
    </div>
    <form action="<?= site_url('item_compliance/save') ?>" class="form need-validation" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('item_name') == '' ?: 'has-error'; ?>">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name"
                       placeholder="Enter item name"
                       required value="<?= set_value('item_name') ?>">
                <?= form_error('item_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('no_hs') == '' ?: 'has-error'; ?>">
                <label for="no_hs">No HS</label>
                <input type="text" class="form-control" id="no_hs" name="no_hs"
                       placeholder="Enter HS number"
                       required value="<?= set_value('no_hs') ?>">
                <?= form_error('no_hs', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('unit') == '' ?: 'has-error'; ?>">
                <label for="unit">Unit</label>
                <input type="text" class="form-control" id="unit" name="unit"
                       placeholder="Enter unit"
                       required value="<?= set_value('unit') ?>">
                <?= form_error('unit', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people_all_branch') ?>"
                        data-key-id="id" data-key-label="name" data-key-sublabel="no_person" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer"
                        data-placeholder="Select customer" required>
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?> - <?= $customer['no_person'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Item description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Item description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>