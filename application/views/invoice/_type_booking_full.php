<div style="display: none" id="type-booking-full">
    <div class="row">
        <div class="col-md-6" id="booking-wrapper">
            <div class="form-group <?= form_error('booking_in') == '' ?: 'has-error'; ?>">
                <label for="booking_in">Booking In</label>
                <select class="form-control select2" id="booking_in" name="booking_in"
                        style="width: 100%" data-placeholder="Select related booking in">
                    <option value=""></option>
                    <?php if(isset($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?= $booking['id'] ?>" <?= set_select('booking_in', $booking['id']) ?>>
                                <?= $booking['no_booking'] . ' (' . if_empty($booking['no_reference'], '-') .')' ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
                <?= form_error('booking_in', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="col-md-6" id="outbound-wrapper">
            <div class="form-group <?= form_error('outbound_date') == '' ?: 'has-error'; ?>">
                <label for="outbound_date">Outbound Date</label>
                <input type="text" class="form-control datepicker" id="outbound_date" name="outbound_date"
                       placeholder="Select invoice date"
                       required maxlength="50" value="<?= set_value('outbound_date', readable_date('now', false)) ?>">
                <?= form_error('outbound_date', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
    </div>
</div>