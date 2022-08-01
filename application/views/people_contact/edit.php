<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Contact</h3>
    </div>
    <form action="<?= site_url('people_contact/update/' . $contact['id']) ?>" class="form" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('contact_name') == '' ?: 'has-error'; ?>">
                <label for="contact_name">Name</label>
                <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Name"
                       value="<?= set_value('contact_name', $contact['name']) ?>">
                <?= form_error('contact_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('occupation') == '' ?: 'has-error'; ?>">
                <label for="occupation">Occupation</label>
                <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Occupation"
                       value="<?= set_value('occupation', $contact['occupation']) ?>">
                <?= form_error('occupation', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('phone') == '' ?: 'has-error'; ?>">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone"
                       value="<?= set_value('phone', $contact['occupation']) ?>">
                <?= form_error('phone', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                       value="<?= set_value('email', $contact['email']) ?>">
                <?= form_error('email', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('address') == '' ?: 'has-error'; ?>">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address"
                       value="<?= set_value('address', $contact['address']) ?>">
                <?= form_error('address', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?= site_url('people/view/' . $contact['id_person']) ?>" class="btn btn-primary">Back to detail view</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Update Contact</button>
        </div>
    </form>
</div>