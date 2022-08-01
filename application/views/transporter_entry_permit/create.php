<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Entry Permit</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit/save') ?>" role="form" method="post" id="form-tep">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="tep_category">Transporter Entry Permit Category</label>
                <select class="form-control select2 select2" required name="tep_category" id="tep_category" data-placeholder="Select Category">
                    <option value=""></option>
                    <option value="INBOUND">INBOUND</option>
                    <option value="EMPTY CONTAINER">EMPTY CONTAINER</option>
                </select>
            </div>

            <div class="form-group booking-wrapper">
                <label for="booking">Booking</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword') ?>"
                        data-key-id="id" data-key-label="no_reference" data-params="type=<?= "INBOUND" ?>" data-key-sublabel="customer_name"
                        name="booking[]" id="booking"
                        data-placeholder="Select booking" multiple>
                    <option value=""></option>
                    <?php if(!empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?= $booking['id'] ?>" selected>
                                <?= $booking['no_reference'] ?> - <?= $booking['customer_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?> customer-wrapper">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer[]" id="customer"
                        data-placeholder="Select customer" style="width: 100%" multiple>
                    <option value=""></option>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">
                    If you don't find any customer
                    <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                </span>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('total_code') == '' ?: 'has-error'; ?>">
                        <label for="total_code">Total Code</label>
                        <input type="number" class="form-control" id="total_code" name="total_code"
                               placeholder="Total generated item" required min="1" max="10"
                               value="<?= set_value('total_code', 1) ?>">
                        <?= form_error('total_code', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8" id="email-type-field">
                    <div class="form-group <?= form_error('email_type') == '' ?: 'has-error'; ?>">
                        <label for="email_type">Send Code To</label>
                        <select class="form-control select2" name="email_type" id="email_type" style="width: 100%">
                            <option value="CUSTOMER">SEND TO CUSTOMER</option>
                            <option value="INPUT">INPUT EMAIL MANUAL</option>
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
            <div class="row" id="tep-before-wrapper" style="display:none">
                <div class="col-md-12" id="tep-before-field">
                    <div class="form-group <?= form_error('tep_before') == '' ?: 'has-error'; ?>">
                        <label for="tep_before">Has TEP Before?</label>
                        <select class="form-control select2" name="tep_before" id="tep_before" style="width: 100%">
                            <option value="no">NO</option>
                            <option value="yes">YES</option>
                        </select>
                        <?= form_error('tep_before', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6" id="tep-reference-field" style="display: none">
                    <div class="form-group <?= form_error('tep_reference') == '' ?: 'has-error'; ?>">
                        <label for="tep_reference">TEP Reference</label>
                        <select class="form-control select2" name="tep_reference" id="tep_reference" style="width: 100%">
                            <option value=""></option>
                        </select>
                        <?= form_error('tep_reference', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
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
                Generate TEP
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/tep.js?v=7') ?>" defer></script>
