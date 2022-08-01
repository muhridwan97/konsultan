<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit TEP</h3>
    </div>
    <form action="<?= site_url('transporter-entry-permit/update-tep/' . $teps['id']) ?>" role="form" method="post" id="form-edit-tep">
        <input type="hidden" name="id" id="id" value="<?= $teps['id'] ?>">
        
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Security Check In</h3>
                </div>
                <div class="box-body">
                <div class="form-group">
                        <label for="receiver_name" class="control-label">Receiver Name</label>
                        <input type="text" class="form-control" name="receiver_name" id="receiver_name"
                               placeholder="Carrier name" required maxlength="50" value="<?= set_value('receiver_name',$teps['receiver_name']) ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_vehicle" class="control-label">Vehicle</label>
                                <input type="text" class="form-control" name="receiver_vehicle"
                                       placeholder="Vehicle type" id="receiver_vehicle" required maxlength="50"
                                       value="<?= set_value('receiver_vehicle',$teps['receiver_vehicle']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_no_police" class="control-label">No Police</label>
                                <input type="text" class="form-control" name="receiver_no_police"
                                       placeholder="No police" id="receiver_no_police" required maxlength="50" pattern="[A-Za-z]{1,2}[0-9]{1,4}[A-Za-z]{0,3}" title="Input Plate Number Format Without Space"
                                       value="<?= set_value('receiver_no_police',$teps['receiver_no_police']) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_contact" class="control-label">Receiver Contact</label>
                                <input type="text" class="form-control" name="receiver_contact"
                                       placeholder="Carrier contact" id="receiver_contact" maxlength="50"
                                       value="<?= set_value('receiver_contact',$teps['receiver_contact']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_email" class="control-label">Receiver Email</label>
                                <input type="email" class="form-control" name="receiver_email"
                                       placeholder="Carrier email" id="receiver_email" maxlength="50"
                                       value="<?= set_value('receiver_email',$teps['receiver_email']) ?>">
                            </div>
                        </div>
                    </div>
                    <?php if ($teps['chassis_delivery'] && !empty($tepChassis)): ?>
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Chassis Handling</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="no_chassis" class="control-label">Chassis Number</label>
                                    <input type="text" class="form-control" name="no_chassis" required
                                           value="<?= $tepChassis['no_chassis'] ?? '' ?>"
                                           placeholder="Type the chassis number" id="no_chassis" maxlength="100">
                                    <input type="hidden" name="id_tep_chassis" value="<?= $tepChassis['id'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update Data</h3>
                    </div>
                    <form action="<?= site_url('transporter-entry-permit/update/' . $teps['id']) ?>?edit_type=security_tep" role="form" method="post" id="form-tep" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="id_booking" id="id_booking_security" value="<?= set_value('id_booking', $teps['id_booking']) ?>">
                                <div id="field-eseal-tep" class="form-group <?= form_error('eseal') == '' ?: 'has-error'; ?>">
                                <label for="eseal">E-seal</label>
                                <select class="form-control select2" name="eseal" id="eseal-tep" style="width: 100%" data-placeholder="Select available e-seal">
                                    <option value=""></option>
                                    <?php foreach ($eseals as $eseal): ?>
                                        <option value="<?= $eseal['id'] ?>" <?= set_select('eseal', $eseal['id'], $eseal['id']==$e_seal['id']) ?>>
                                            <?= $eseal['no_eseal'] ?> - <?= $eseal['device_name'] ?> (ID <?= if_empty($eseal['id_device'], 'Not Connected') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block">
                                    Add more e-seal? <a href="<?= site_url('eseal/create') ?>" target="_blank">click here</a>. If your not found your eseal,
                                    it may used in another truck (<a href="<?= site_url('eseal') ?>">find here</a>), try to security or gate out to release the e-seal.
                                </span>
                                <?= form_error('eseal', '<span class="help-block">', '</span>'); ?>
                            </div>
                            <?php $this->load->view('tally/_tally_editor', [
                                'inputSource' => 'STOCK',
                                'withDetailContainer' => false,
                                'withDetailGoods' => false,
                                'allowIn' => true,
                                'containers' => $containers,
                            ]) ?>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <button type="submit" data-no="<?= $teps['tep_code'] ?>" class="btn btn-primary pull-right" id="btn-update-tep">
                Update TEP
            </button>
        </div>
    </form>
</div>
<script id="row-additional-guest-template" type="text/x-custom-template">
    <tr class="row-additional-guest-template">
        <td></td>
        <td>
            <input type="text" class="form-control" required name="additional_guest_name[]" id="additional_guest_name"
                   placeholder="Additional Guest Name">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-guest">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>
<script src="<?= base_url('assets/app/js/tep_edit.js?v=1') ?>" defer></script>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('template/_modal_confirm'); ?>
<!-- <script src="<?= base_url('assets/app/js/tep.js?v=5') ?>" defer></script> -->