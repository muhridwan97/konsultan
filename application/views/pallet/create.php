<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Generate Pallet Batch</h3>
    </div>
    <form action="<?= site_url('pallet/save') ?>" role="form" method="post" id="form-pallet">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Generate Method</label>
                <select class="form-control select2" name="type" id="type" data-placeholder="Select type" required>
                    <option value=""></option>
                    <option value="BOOKING">GENERATE FROM BOOKING</option>
                    <option value="RAW">GENERATE RAW PALLET</option>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>
            <div id="raw-wrapper" style="display: none">
                <div class="form-group <?= form_error('total') == '' ?: 'has-error'; ?>">
                    <label for="total">Total Pallet</label>
                    <input type="number" class="form-control" id="total" name="total"
                           placeholder="Enter total pallet to be generated"
                           required min="1" max="200" value="<?= set_value('total') ?>">
                    <?= form_error('total', '<span class="help-block">', '</span>'); ?>
                </div>
                <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Pallet description"
                              required maxlength="1000"><?= set_value('description') ?></textarea>
                    <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div id="booking-wrapper" style="display: none">
                <label for="booking">Booking</label>
                <select name="booking" id="booking" class="form-control select2" style="width: 100%" data-placeholder="Select booking">
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>">
                            <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="booking-pallet-form">

                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Generate Batch Pallet</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/pallet.js') ?>" defer></script>