<div style="display: none" id="type-booking-full-extension">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group <?= form_error('outbound_date') == '' ?: 'has-error'; ?>">
                <label for="outbound_date">Outbound Date</label>
                <input type="text" class="form-control datepicker" id="outbound_date" name="outbound_date"
                       placeholder="Select invoice date"
                       required maxlength="50" value="<?= set_value('outbound_date', readable_date('now', false)) ?>">
                <?= form_error('outbound_date', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group <?= form_error('source') == '' ?: 'has-error'; ?>">
                <label for="source">Data Source</label>
                <select style="width: 100%" type="text" class="form-control select2" id="source" name="source" data-placeholder="Invoice source data">
                    <option value="STOCK">STOCK</option>
                    <option value="BOOKING">RAW BOOKING</option>
                </select>
                <?= form_error('source', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
    </div>
    <div class="form-group <?= form_error('booking_invoice') == '' ?: 'has-error'; ?>">
        <label for="booking_invoice">Last Invoice Booking Full</label>
        <select class="form-control select2" id="booking_invoice" name="booking_invoice"
                style="width: 100%" data-placeholder="Select last invoice">
            <option value=""></option>
            <?php if(isset($invoices)): ?>
                <?php foreach ($invoices as $invoice): ?>
                    <option value="<?= $invoice['id'] ?>" <?= set_select('booking_invoice', $booking['id']) ?>>
                        <?= $invoice['no_invoice'] . ' (' . if_empty($invoice['no_reference'], '-') .')' ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <?= form_error('booking_invoice', '<span class="help-block">', '</span>'); ?>
    </div>
</div>