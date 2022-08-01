<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Customer</h3>
    </div>
    <form action="<?= site_url('customer/save') ?>" class="form need-validation" method="post" id="form-customer">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="row">
                <div class="col-md-6" id="field-identity-number">
                    <div class="form-group <?= form_error('identity_number') == '' ?: 'has-error'; ?>">
                        <label for="identity_number">Identity Number</label>
                        <input type="text" class="form-control" id="identity_number" name="identity_number"
                               placeholder="Enter code person"
                               value="<?= set_value('identity_number') ?>" pattern="[0-9]{16}" required maxlength="16">
                        <span class="help-block">Format KTP</span>
                        <?= form_error('identity_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('tax_number') == '' ?: 'has-error'; ?>">
                        <label for="tax_number">Tax Number</label>
                        <input type="text" class="form-control" id="tax_number" name="tax_number"
                               placeholder="Put tax number" minlength="15" maxlength="15"
                               value="<?= set_value('tax_number') ?>">
                        <?= form_error('tax_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter person name"
                       value="<?= set_value('name') ?>" required maxlength="50">
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('address') == '' ?: 'has-error'; ?>">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address"
                       placeholder="Enter address"
                       value="<?= set_value('address') ?>">
                <?= form_error('address', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('gender') == '' ?: 'has-error'; ?>">
                        <label for="gender">Gender</label>
                        <select class="form-control select2" name="gender" id="gender"
                                data-placeholder="Select gender" required>
                            <option value=""></option>
                            <option value="MALE"  <?= set_select('gender', "MALE") ?>>MALE</option>
                            <option value="FEMALE" <?= set_select('gender', "FEMALE") ?>>FEMALE</option>
                        </select>
                        <?= form_error('gender', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('birthday') == '' ?: 'has-error'; ?>">
                        <label for="birthday">Birthday</label>
                        <input type="text" class="form-control datepicker" id="birthday" name="birthday"
                               placeholder="Enter birthday"
                               value="<?= set_value('birthday') ?>">
                        <?= form_error('birthday', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('contact') == '' ?: 'has-error'; ?>">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact"
                               placeholder="Enter contact" maxlength="50"
                               value="<?= set_value('contact') ?>">
                        <?= form_error('contact', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter email name"
                               value="<?= set_value('email') ?>">
                        <?= form_error('email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Customer</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/customer.js?v=3') ?>" defer></script>