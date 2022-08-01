<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Item</h3>
    </div>
    <form action="<?= site_url('item-compliance/update/' . $itemCompliance['id']) ?>" class="form need-validation" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('item_name') == '' ?: 'has-error'; ?>">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name"
                       placeholder="Enter item name"
                       value="<?= set_value('item_name', $itemCompliance['item_name']) ?>">
                <?= form_error('item_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('no_hs') == '' ?: 'has-error'; ?>">
                <label for="no_hs">No HS</label>
                <input type="text" class="form-control" id="no_hs" name="no_hs"
                       placeholder="Enter no_hs name"
                       value="<?= set_value('no_hs', $itemCompliance['no_hs']) ?>">
                <?= form_error('no_hs', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('unit') == '' ?: 'has-error'; ?>">
                <label for="unit">Unit</label>
                <input type="text" class="form-control" id="unit" name="unit"
                       placeholder="Enter unit name"
                       value="<?= set_value('unit', $itemCompliance['unit']) ?>">
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
                    <?php if(!empty($itemCompliance['customer_name'])): ?>
                        <option value="<?= $itemCompliance['id_customer'] ?>" selected>
                            <?= $itemCompliance['customer_name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Unit description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Unit description"
                          maxlength="500"><?= set_value('description', $itemCompliance['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Unit</button>
        </div>
    </form>
</div>