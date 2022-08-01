<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Readdress Booking</h3>
    </div>
    <form action="<?= site_url('readdress/save') ?>" role="form" method="post" id="form-readdress">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                <label for="booking">Booking</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                        name="booking" id="booking"
                        data-placeholder="Select related booking">
                    <option value=""></option>
                </select>
                <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Change to Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer"
                        data-placeholder="Select customer" required>
                    <option value=""></option>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">
                    If you don't find any customer
                    <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                </span>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required placeholder="Readdress description"
                          maxlength="1000"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Readdress</button>
        </div>
    </form>
</div>