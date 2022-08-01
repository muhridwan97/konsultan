<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Filter</h3>
    </div>
    <form role="form" method="get">
        <div class="box-body">
            <div class="form-group">
                <label for="booking">Booking</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?owner=' . UserModel::authenticatedUserData('person_type')) ?>"
                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                        name="booking" id="booking"
                        data-placeholder="Select booking">
                    <option value=""></option>
                    <?php if (!empty($booking)): ?>
                        <option value="<?= $booking['id'] ?>" selected>
                            <?= $booking['no_reference'] ?>
                        </option>
                    <?php endif ?>
                </select>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Track Booking</button>
        </div>
    </form>
</div>

<?php if(!empty(get_url_param('booking'))): ?>
    <?php $this->load->view('report/_tracker_data'); ?>
<?php endif; ?>