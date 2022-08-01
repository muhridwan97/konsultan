<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Handling</h3>
    </div>
    <form action="<?= site_url('handling/save') ?>" method="post" role="form" id="form-handling">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                            <label for="customer">Customer</label>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                    name="customer" id="customer" style="width: 100%"
                                    data-placeholder="Select Customer" required>
                                <option value=""></option>
                            </select>
                            <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="customer" id="customer" value="<?= UserModel::authenticatedUserData('id_person') ?>">
            <?php endif; ?>
            <input type="hidden" id="status_page" value="HANDLING">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2" name="handling_type" id="handling_type"
                                data-placeholder="Select handling type" style="width: 100%" required>
                            <option value=""></option>
                        </select>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('ref_data') == '' ?: 'has-error'; ?>">
                        <label for="ref_data">Reference Data</label>
                        <select class="form-control select2" name="ref_data" id="ref_data"
                                data-placeholder="Select reference data" style="width: 100%" required>
                            <option value=""></option>
                        </select>
                        <?= form_error('ref_data', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('plan_date') == '' ?: 'has-error'; ?>">
                <label for="plan_date">Plan Date</label>
                <input type="text" class="form-control daterangepicker2" id="plan_date" name="plan_date"
                       placeholder="Plan date" maxlength="500"
                       value="<?= set_value('plan_date', date('d F Y')) ?>">
                <span class="help-block">You have maximum <?= get_setting('max_time_job_after_approved') ?> hours after this plan to proceed, before get penalty.</span>
                <?= form_error('plan_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Handling Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Description"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="input-detail-wrapper">
                <?php $this->load->view('tally/_tally_editor', [
                    'inputSource' => 'STOCK'
                ]) ?>
            </div>

        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-primary pull-right">
                Save handling
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('tally/_modal_select_position') ?>

<script>
    var dateRangePickerSettings = {
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: '<?= (new DateTime())->add(new DateInterval('PT8H'))->format('d F Y H:i') ?>',
        locale: {
            format: 'DD MMMM YYYY HH:mm'
        }
    };
</script>


<script src="<?= base_url('assets/app/js/handling.js?v=4') ?>" defer></script>