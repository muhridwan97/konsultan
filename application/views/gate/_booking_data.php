<?php if (!empty($bookingContainers) || !empty($bookingGoods)): ?>
    <div class="box box-primary" id="form-booking-job">
        <div class="box-header with-border">
            <h3 class="box-title">Create Booking Job</h3>
        </div>
        <div class="box-body">


            <form action="<?= site_url('work-order/create/' . $booking['category']) ?>" method="post">
                <input type="hidden" name="id_booking" id="id_booking" value="<?= $booking['id'] ?>">
                <input type="hidden" name="id_handling" id="id_handling">
                <input type="hidden" name="id_safe_conduct" id="id_safe_conduct">

                <?php if($source == 'DO'): ?>
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>This booking has <strong>delivery order (DO)</strong> system!</p>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="source" value="<?= $source ?>">

                <?php $this->load->view('tally/_tally_editor', [
                    'inputSource' => 'STOCK',
                    'stockUrl' => site_url("gate/ajax_get_booking_data?id_booking={$booking['id']}"),
                    'withDetailContainer' => false,
                    'withDetailGoods' => false,
                ]) ?>

                <?php $this->load->view('gate/_field_handling_component') ?>

                <div class="form-group <?= form_error('handling_date') == '' ?: 'has-error'; ?>">
                    <label for="handling_date">Handling Date</label>
                    <input type="text" class="form-control daterangepicker2" id="handling_date" name="handling_date"
                           placeholder="Handling date" maxlength="50" required
                           value="<?= set_value('handling_date') ?>">
                    <p class="help-block">Plan of handling should be proceed and ready to realized in work order</p>
                    <?= form_error('handling_date', '<span class="help-block">', '</span>'); ?>
                </div>
                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" id="description" cols="30" rows="2"
                              class="form-control" placeholder="Job description"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg pull-right">
                    Create job
                </button>

            </form>
        </div>
    </div>


    <?php $this->load->view('tally/_modal_container_input', [
        'bookingId' => $booking['id'],
        'customer' => $booking['customer_name']
    ]) ?>
    <?php $this->load->view('tally/_modal_goods_input', [
        'bookingId' => $booking['id'],
        'customer' => $booking['customer_name']
    ]) ?>

<?php else: ?>
    <div class="panel panel-success">
        <div class="panel-body">
            <p class="lead mb0">All booking data already in job phase, check list below.</p>
        </div>
    </div>
<?php endif; ?>

<script>
    var dateRangePickerSettings = {
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: '<?= (new DateTime())->format('d F Y H:i') ?>',
        locale: {
            format: 'DD MMMM YYYY HH:mm'
        }
    };
</script>