<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Booking Report</h3>
    </div>
    <form action="<?= site_url('booking_news/save') ?>" role="form" method="post" id="form-booking-news">
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
                <label for="type">Report Type</label>
                <select class="form-control select2" name="type" id="type" data-placeholder="Select report type" required>
                    <option value=""></option>
                    <option value="WITHDRAWAL" <?= set_select('type', 'WITHDRAWAL') ?>>WITHDRAWAL</option>
                    <option value="CANCELING" <?= set_select('type', 'CANCELING') ?>>CANCELING</option>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('no_booking_news') == '' ?: 'has-error'; ?>">
                        <label for="no_booking_news">No BA</label>
                        <input type="text" data-mask="00000" name="no_booking_news" id="no_booking_news" class="form-control" required
                               placeholder="No booking news" value="<?= set_value('no_booking_news') ?>">
                        <?= form_error('no_booking_news', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('booking_news_date') == '' ?: 'has-error'; ?>">
                        <label for="booking_news_date">BA Date</label>
                        <input type="text" class="form-control datepicker" id="booking_news_date" name="booking_news_date"
                               placeholder="BA date" required
                               value="<?= set_value('booking_news_date', date('d F Y')) ?>">
                        <?= form_error('booking_news_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('no_sprint') == '' ?: 'has-error'; ?>">
                        <label for="no_sprint">No SPRINT</label>
                        <input type="text" name="no_sprint" id="no_sprint" class="form-control" required
                               placeholder="S-XXXXX/KPU.XX/BD.XXXX/XXXX" value="<?= set_value('no_sprint') ?>">
                        <?= form_error('no_sprint', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('sprint_date') == '' ?: 'has-error'; ?>">
                        <label for="sprint_date">SPRINT Date</label>
                        <input type="text" class="form-control datepicker" id="sprint_date" name="sprint_date"
                               placeholder="BA date" required
                               value="<?= set_value('sprint_date', date('d F Y')) ?>">
                        <?= form_error('sprint_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('tps') == '' ?: 'has-error'; ?>">
                <label for="tps">TPS</label>
                <select class="form-control select2" name="tps" id="tps" data-placeholder="Select TPS" required>
                    <option value=""></option>
                    <?php foreach ($tps as $row): ?>
                        <option value="<?= $row['name'] ?>" <?= set_select('tps', $row['name']) ?>>
                            <?= $row['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('tps', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">
                    If you don't find any TPS
                    <a href="<?= site_url('people/create?type=TPS') ?>" target="_blank">Click here</a> to create or add one.
                </span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('chief_name') == '' ?: 'has-error'; ?>">
                        <label for="chief_name">Chief Name</label>
                        <input type="text" name="chief_name" id="chief_name" class="form-control" required
                               placeholder="Chief full name" value="<?= set_value('chief_name') ?>">
                        <?= form_error('chief_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('chief_nip') == '' ?: 'has-error'; ?>">
                        <label for="chief_nip">Chief NIP</label>
                        <input type="text" class="form-control" id="chief_nip" name="chief_nip"  required
                               placeholder="Chief NIP" value="<?= set_value('chief_nip') ?>">
                        <?= form_error('chief_nip', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Report Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Report description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php $this->load->view('booking_news/_booking') ?>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right" id="btn-save-booking-news">
                Save Booking Report
            </button>
        </div>
    </form>
</div>

<!-- jQuery Mask 1.7.7 -->
<script src="<?= base_url('assets/plugins/jQueryMask/jquery.mask.js') ?>"></script>
<script src="<?= base_url('assets/app/js/booking_news.js') ?>" defer></script>