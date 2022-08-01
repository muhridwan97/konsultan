<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Investigation</h3>
    </div>
    <form action="<?= site_url('complain/save_investigation/' . $complain['id']) ?>" class="form" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer"
                        data-placeholder="Select customer" disabled style="width: 100%">
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('complain') == '' ?: 'has-error'; ?> complain-class">
                <label for="complain">Investigation</label>
                 <textarea class="form-control" id="investigation" name="investigation" placeholder="Enter Investigation" maxlength="500"><?= set_value('investigation') ?></textarea>
                <?= form_error('investigation', '<span class="help-block">', '</span>'); ?>
            </div>
             <div class="form-group <?= form_error('corrective') == '' ?: 'has-error'; ?>">
                <label for="corrective">Corrective</label>
                 <textarea class="form-control" id="corrective" name="corrective" placeholder="Enter Corrective" maxlength="500"><?= set_value('corrective') ?></textarea>
                <?= form_error('corrective', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('prevention') == '' ?: 'has-error'; ?>">
                <label for="prevention">Prevention</label>
                 <textarea class="form-control" id="prevention" name="prevention" placeholder="Enter Prevention" maxlength="500"><?= set_value('prevention') ?></textarea>
                <?= form_error('prevention', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('investigation_attachment') == '' ?: 'has-error'; ?>">
                <label for="investigation_attachment">Investigation Attachment</label>
                <input type="file" name="investigation_attachment" id="investigation_attachment"
                       accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                       placeholder="Select document"  <?= $complain['value_type'] == ComplainCategoryModel::TYPE_MAJOR ? 'required' : ''; ?>>
                <?= form_error('investigation_attachment', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>