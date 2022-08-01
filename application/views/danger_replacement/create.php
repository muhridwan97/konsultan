<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Replace Danger Status</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('danger_replacement/save') ?>" role="form" method="post" id="form-danger-replacement">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                <label for="booking">Booking</label>
                <select type="text" class="form-control select2" id="booking" name="booking"
                        data-placeholder="Select related booking">
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id_booking'] ?>"<?= set_select('booking', $booking['id_booking']) ?>>
                            <?= $booking['no_booking'] . ' (' . if_empty($booking['no_reference'], '-') . ')' . ' - ' . if_empty($booking['customer_name'], $booking['customer_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php $this->load->view('workorder/_booking_stock_loader') ?>

            <div class="form-group <?= form_error('status_danger') == '' ?: 'has-error'; ?>">
                <label for="status_danger">Change Danger To</label>
                <select class="form-control select2" name="status_danger" id="status_danger"
                        data-placeholder="Danger status" required style="width: 100%" <?= $isHidden ? 'disabled' : ''?>>
                    <option value="NOT DANGER"<?= set_select('status_danger', 'NOT DANGER') ?>>NOT DANGER</option>
                    <option value="DANGER TYPE 1"<?= set_select('status_danger', 'DANGER TYPE 1') ?>>DANGER TYPE 1</option>
                    <option value="DANGER TYPE 2"<?= set_select('status_danger', 'DANGER TYPE 2') ?>>DANGER TYPE 2</option>
                </select>
                <?= form_error('status_danger', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Replace description"
                          maxlength="1000"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Danger Replacement</button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/danger_replacement.js?v=2') ?>" defer></script>