<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Linked Entry Permit</h3>
    </div>

    <form action="<?= site_url('linked-entry-permit/save') ?>" role="form" method="post" class="need-validation" id="form-linked-tep">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group" style="display: none">
                <label for="tep_category">Transporter Entry Permit Category</label>
                <select class="form-control select2 select2" required name="tep_category" id="tep_category" data-placeholder="Select category">
                    <option value="OUTBOUND" selected></option>
                </select>
            </div>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?> customer-wrapper">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer"
                        data-placeholder="Select customer" style="width: 100%" required>
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">
                    If you don't find any customer
                    <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                </span>
            </div>
            <div class="form-group <?= form_error('uploads') == '' ?: 'has-error'; ?>">
                <label for="aju">Upload Reference</label>
                <select class="form-control select2" name="uploads[]" id="aju" multiple
                        data-placeholder="Select no reference" style="width: 100%" required>
                    <option value=""></option>
                    <?php foreach ($uploads as $upload): ?>
                        <option value="<?= $upload['id'] ?>" selected>
                            <?= $upload['description'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('uploads', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('total_code') == '' ?: 'has-error'; ?>">
                        <label for="total_code">Total Code</label>
                        <input type="number" class="form-control" id="total_code" name="total_code"
                               placeholder="Total generated item" required min="1" max="10" readonly
                               value="1">
                        <?= form_error('total_code', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8" id="email-type-field">
                    <div class="form-group <?= form_error('email_type') == '' ?: 'has-error'; ?>">
                        <label for="email_type">Send Code To</label>
                        <select class="form-control select2" name="email_type" id="email_type" data-placeholder="Sent to" required style="width: 100%">
                            <option value=""></option>
                            <option value="0"<?= set_select('email_type', '0') ?>>DO NOT SENT TEP</option>
                            <option value="CUSTOMER"<?= set_select('email_type', 'CUSTOMER') ?>>SEND TO CUSTOMER</option>
                            <option value="INPUT"<?= set_select('email_type', 'INPUT') ?>>INPUT EMAIL MANUAL</option>
                        </select>
                        <?= form_error('email_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4" id="email-input-field" style="display: none">
                    <div class="form-group <?= form_error('input_email') == '' ?: 'has-error'; ?>">
                        <label for="input_email">Input Email</label>
                        <input type="text" class="form-control" id="input_email" name="input_email"
                               placeholder="Input email, separate by comma"
                               value="<?= set_value('input_email') ?>">
                        <?= form_error('input_email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('linked_tep') == '' ?: 'has-error'; ?>">
                <label for="linked_tep">Linked TEP</label>
                <select class="form-control select2" name="linked_tep" id="linked_tep" data-placeholder="Select linked tep" required style="width: 100%">
                    <option value=""></option>
                </select>
                <?= form_error('linked_tep', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">
                    Linked to blank TEP (no check in-out) from another branch
                </span>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Create TEP
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/tep.js?v=7') ?>" defer></script>
<script src="<?= base_url('assets/app/js/linked-tep.js?v=1') ?>" defer></script>