<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Assignment</h3>
    </div>

    <form action="<?= site_url('booking-assignment/save') ?>" role="form" method="post" id="form-booking-assignment">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                <label for="booking">Booking</label>
                <select class="form-control select2" name="booking" id="booking" data-placeholder="Select booking" required>
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>"<?= set_select('bookings', $booking['id'], $booking['id'] == get_url_param('id_booking')) ?>>
                            <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?> - <?= $booking['customer_name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">Bookings which never assigned yet</span>
                <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('user') == '' ?: ' has-error'; ?>">
                <label for="user">User</label>
                <select class="form-control select2" name="user" id="user" data-placeholder="Select user" required>
                    <option value=""></option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"<?= set_select('users', $user['id']) ?>>
                            <?= $user['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">List that users have permission to create payment</span>
                <?= form_error('user', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Assignment note"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Assignment
            </button>
        </div>
    </form>
</div>