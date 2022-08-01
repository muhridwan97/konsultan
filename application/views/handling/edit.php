<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Handling</h3>
    </div>
    <form action="<?= site_url('handling/update/' . $handling['id']) ?>" method="post" id="form-handling" class="edit">
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
                                    name="customer" id="customer"
                                    data-placeholder="Select Customer" required>
                                <option value=""></option>
                                <option value="<?= $customer['id'] ?>" selected><?= $customer['name'] ?></option>
                            </select>
                            <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="customer" id="customer" value="<?= $customer['id'] ?>">
            <?php endif; ?>
            <input type="hidden" id="status_page" value="HANDLING">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2" name="handling_type" id="handling_type"
                                data-placeholder="Select handling type" required>
                            <option value=""></option>
                            <?php foreach ($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>" <?= set_select('handling_type', $handlingType['id'], $handling['id_handling_type'] == $handlingType['id']) ?>>
                                    <?= $handlingType['handling_type'] ?> - <?= $handlingType['category'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('ref_data') == '' ?: 'has-error'; ?>">
                        <label for="ref_data">Reference Data</label>
                        <?php if(!$checkBookingStocksByCustomer): ?>
                            <input class="form-control" type="hidden" name="ref_data" value="<?= $handling['id_booking'] ?>">
                             <select class="form-control select2" name="ref_data" id="ref_data"
                                    data-placeholder="Select reference data" required>
                                <option value=""></option>
                                <?php foreach ($getAllBookings as $getReference): ?>
                                    <option value="<?= $getReference['id'] ?>" <?= set_select('ref_data', $getReference['id'], $handling['id_booking'] == $getReference['id']) ?>>
                                        <?= $getReference['no_booking'] ?> - (<?= $getReference['no_reference'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                        <select class="form-control select2" name="ref_data" id="ref_data"
                                data-placeholder="Select reference data" required>
                            <option value=""></option>
                            <?php foreach ($refData as $reference): ?>
                                <option value="<?= $reference['id'] ?>" <?= set_select('ref_data', $reference['id'], $handling['id_booking'] == $reference['id']) ?>>
                                    <?= $reference['no_booking'] ?> - (<?= $reference['no_reference'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif;?>
                        <?= form_error('ref_data', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('plan_date') == '' ?: 'has-error'; ?>">
                <label for="plan_date">Plan Date</label>
                <input class="form-control daterangepicker2" id="plan_date" name="plan_date"
                       placeholder="Plan date" required
                       value="<?= set_value('plan_date', (new DateTime($handling['handling_date']))->format('d F Y H:i')) ?>">
                <?= form_error('plan_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Handling Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="500"
                          placeholder="Description"><?= set_value('description', $handling['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <input type="hidden" id="booking_stock" value="<?= $checkBookingStocksByCustomer ?>">
        </div>

        <div id="input-detail-wrapper">
            <?php $this->load->view('tally/_tally_editor', [
                'inputSource' => 'STOCK',
                'detailHandling' => true,
                'bookingStocks' => $checkBookingStocksByCustomer,
                'getBookingById' => $getBookingById,
            ]) ?>
        </div>

        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button class="btn btn-success pull-right" type="submit">Update Handling</button>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input', [
    'bookingId' => $handling['id_booking'],
    'customer' => $customer['name']
]) ?>
<?php $this->load->view('tally/_modal_goods_input', [
    'bookingId' => $handling['id_booking'],
    'customer' => $customer['name']
]) ?>
<?php $this->load->view('tally/_modal_select_position') ?>

<script>
    var dateRangePickerSettings = {
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: '<?= (new DateTime($handling['handling_date']))->format('d F Y H:i') ?>',
        startDate: '<?= (new DateTime($handling['handling_date']))->format('d F Y H:i') ?>',
        locale: {
            format: 'DD MMMM YYYY HH:mm'
        }
    };
</script>
<script src="<?= base_url('assets/app/js/handling.js?v=4') ?>" defer></script>