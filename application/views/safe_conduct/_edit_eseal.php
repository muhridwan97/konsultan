<?php if (!empty($safeConduct['security_in_date']) && $safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND && $safeConduct['expedition_type'] == 'INTERNAL'): ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Update Safe Conduct <span class="text-danger"><?= $safeConduct['no_safe_conduct'] ?></span></h3>
    </div>
    <form action="<?= site_url('safe-conduct/update/' . $safeConduct['id']) ?>?edit_type=security" role="form" method="post" id="form-eseal" enctype="multipart/form-data">
        <div class="box-body">
            <input type="hidden" name="id_booking" id="id_booking_security" value="<?= set_value('id_booking', $safeConduct['id_booking']) ?>">
            <input type="hidden" name="branch_type" id="branch_type" value="<?= set_value('branch_type', $safeConduct['branch_type']) ?>">
            <div id="field-eseal-security" class="form-group <?= form_error('eseal') == '' ?: 'has-error'; ?>">
                <label for="eseal-security">E-seal</label>
                <?php if(empty($safeConduct['id_eseal'])): ?>
                    <select class="form-control select2" name="eseal" id="eseal-security" required style="width: 100%" data-placeholder="Select available e-seal">
                        <option value=""></option>
                        <?php foreach ($eseals as $eseal): ?>
                            <option value="<?= $eseal['id'] ?>" <?= set_select('eseal', $eseal['id']) ?>>
                                <?= $eseal['no_eseal'] ?> - <?= $eseal['device_name'] ?> (ID <?= if_empty($eseal['id_device'], 'Not Connected') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="help-block">
                        Add more e-seal? <a href="<?= site_url('eseal/create') ?>" target="_blank">click here</a>. If your not found your eseal,
                        it may used in another truck (<a href="<?= site_url('eseal') ?>">find here</a>), try to security or gate out to release the e-seal.
                    </span>
                    <?= form_error('eseal', '<span class="help-block">', '</span>'); ?>
                <?php else: ?>
                    <p class="form-control-static"><?= $safeConduct['no_eseal'] ?> - <?= $safeConduct['device_name'] ?></p>
                    <input type="hidden" name="eseal" id="eseal-security" required value="<?= $safeConduct['id_eseal'] ?>">
                <?php endif; ?>
            </div>
            <?php if(empty($safeConductContainers) && empty($safeConductGoods)): ?>
                <?php $this->load->view('tally/_tally_editor', [
                    'formId' => $safeConduct['id'],
                    'inputSource' => 'STOCK',
                    'stockUrl' => site_url('safe-conduct/ajax_get_booking_data?id_booking=' . $safeConduct['id_booking']),
                    'withDetailContainer' => false,
                    'withDetailGoods' => false,
                    'allowIn' => true,
                ]) ?>
            <?php endif; ?>

            <div class="box-footer clearfix">
                <button type="submit" class="btn btn-primary pull-right" data-label="Security Notification">
                    Update <?= $safeConduct['no_safe_conduct'] ?>
                </button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
