<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Safe Conduct</h3>
    </div>
    <?php if(!get_url_param('allowgoods')): ?>
    <form action="<?= site_url('safe-conduct/update/' . $safeConduct['id']) ?>" role="form" method="post"
          id="form-safe-conduct" class="edit" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" name="id" id="id" value="<?= $safeConduct['id'] ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                        <label for="category">Category</label>
                        <select class="form-control select2" name="category" id="category" data-placeholder="Select category"
                                style="width: 100%" required>
                            <option value=""></option>
                            <option value="INBOUND" <?= set_select('category', 'INBOUND', $safeConduct['type'] == 'INBOUND') ?>>
                                INBOUND
                            </option>
                            <option value="OUTBOUND" <?= set_select('category', 'OUTBOUND', $safeConduct['type'] == 'OUTBOUND') ?>>
                                OUTBOUND
                            </option>
                        </select>
                        <?= form_error('category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('warehouse') == '' ?: 'has-error'; ?>">
                        <label for="warehouse">Warehouse of Origin</label>
                        <select class="form-control select2" name="warehouse" id="warehouse" style="width: 100%" data-placeholder="Select warehouse of origin">
                            <option value=""></option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" <?= set_select('warehouse', $warehouse['id'], $warehouse['id'] == $safeConduct['id_source_warehouse']) ?>>
                                    <?= $warehouse['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('warehouse', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('expedition_type') == '' ?: 'has-error'; ?>">
                <label for="expedition_type">Expedition Type</label>
                <select class="form-control select2" name="expedition_type" id="expedition_type"
                        data-placeholder="Select expedition type" style="width: 100%" required>
                    <option value=""></option>
                    <option value="INTERNAL" <?= set_select('expedition_type', 'INTERNAL', $safeConduct['expedition_type'] == 'INTERNAL') ?>>
                        INTERNAL (PT. Transcon Indonesia)
                    </option>
                    <option value="EXTERNAL" <?= set_select('expedition_type', 'EXTERNAL', $safeConduct['expedition_type'] == 'EXTERNAL') ?>>
                        EXTERNAL (Other expedition company)
                    </option>
                </select>
                <?= form_error('expedition_type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group cy-wrapper" style="display: none">
                <label for="cy_date">CY Date</label>
                <input type="text" class="form-control daterangepicker2" id="cy_date" name="cy_date" placeholder="CY Date" value="<?= set_value('cy_date', !empty($safeConduct['cy_date']) ? (new DateTime($safeConduct['cy_date']))->format('d F Y H:i:s') : '' ) ?>" autocomplete="off">
                <?= form_error('cy_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row" id="internal-expedition-wrapper" <?= $safeConduct['expedition_type'] == 'INTERNAL' ? '' : 'style="display: none"' ?>>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('vehicle') == '' ?: 'has-error'; ?>">
                        <label for="vehicle">Vehicle</label>
                        <select class="form-control select2" name="vehicle" id="vehicle"
                                data-placeholder="Select vehicle" style="width: 100%" <?= $safeConduct['expedition_type'] == 'INTERNAL' ? 'required' : '' ?>>
                            <option value=""></option>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['vehicle_name'] ?>"
                                        data-no-police="<?= $vehicle['no_plate'] ?>"
                                    <?= set_select('vehicle', $vehicle['vehicle_name'], $safeConduct['vehicle_type'] == $vehicle['vehicle_name']) ?>>
                                    <?= $vehicle['vehicle_type'] ?> - <?= $vehicle['vehicle_name'] ?>
                                    (<?= $vehicle['no_plate'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="no_police" id="no_police" value="<?= $safeConduct['no_police'] ?>">
                        <?= form_error('vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('driver') == '' ?: 'has-error'; ?>">
                        <label for="driver">Driver</label>
                        <select class="form-control select2" name="driver" id="driver" data-placeholder="Select driver"
                                style="width: 100%" <?= $safeConduct['expedition_type'] == 'INTERNAL' ? 'required' : '' ?>>
                            <option value=""></option>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?= $driver['name'] ?>"
                                    <?= set_select('driver', $driver['name'], $safeConduct['driver'] == $driver['name']) ?>>
                                    <?= $driver['name'] ?> (<?= $driver['no_person'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('driver', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('expedition') == '' ?: 'has-error'; ?>">
                        <label for="expedition">Expedition</label>
                        <p class="form-control-static">PT. Transcon Indonesia</p>
                        <input type="hidden" class="form-control" id="expedition" name="expedition"  <?= $safeConduct['expedition_type'] == 'INTERNAL' ? 'required' : '' ?>
                               value="<?= $safeConduct['expedition'] ?>">
                        <?= form_error('expedition', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div id="field-eseal" class="form-group <?= form_error('eseal') == '' ?: 'has-error'; ?>">
                <label for="driver">E-seal</label>
                <select class="form-control select2" name="eseal" id="eseal" data-placeholder="Select available e-seal">
                    <option value=""></option>
                    <?php foreach ($eseals as $eseal): ?>
                        <option value="<?= $eseal['id'] ?>" <?= set_select('eseal', $eseal['id'], $safeConduct['id_eseal'] == $eseal['id']) ?>>
                            <?= $eseal['no_eseal'] ?> <?php if($safeConduct['id_eseal'] == $eseal['id']): ?> (current)<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">
                    Add more e-seal? <a href="<?= site_url('eseal/create') ?>" target="_blank">click here</a>. If your not found your eseal,
                    it may used in another truck (<a href="<?= site_url('eseal') ?>">find here</a>), try to security or gate out to release the e-seal.
                </span>
                <?= form_error('eseal', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row" id="external-expedition-wrapper" <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? '' : 'style="display: none"' ?>>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('vehicle') == '' ?: 'has-error'; ?>">
                        <label for="vehicle">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle" name="vehicle"
                               placeholder="Vehicle type or name" <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? 'required' : 'disabled' ?>
                               value="<?= set_value('vehicle', $safeConduct['vehicle_type']) ?>">
                        <?= form_error('vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('no_police') == '' ?: 'has-error'; ?>">
                        <label for="no_police">Police Number</label>
                        <input type="text" class="form-control" id="no_police" name="no_police"
                               placeholder="Police plat number"  <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? 'required' : 'disabled' ?>
                               value="<?= set_value('no_police', $safeConduct['no_police']) ?>">
                        <?= form_error('no_police', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('driver') == '' ?: 'has-error'; ?>">
                        <label for="driver">Driver Name</label>
                        <input type="text" class="form-control" id="driver" name="driver"
                               placeholder="Driver name" <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? 'required' : 'disabled' ?>
                               value="<?= set_value('driver', $safeConduct['driver']) ?>">
                        <?= form_error('driver', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('expedition') == '' ?: 'has-error'; ?>">
                        <label for="expedition">Expedition</label>
                        <select class="form-control select2" name="expedition" id="expedition"
                                data-placeholder="Select expedition" style="width: 100%" <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? 'required' : ' disabled' ?>>
                            <option value=""></option>
                            <?php foreach ($expeditions as $expedition): ?>
                                <option value="<?= $expedition['name'] ?>"
                                    <?= set_select('expedition', $expedition['name'], $safeConduct['expedition'] == $expedition['name']) ?>>
                                    <?= $expedition['name'] ?> (<?= $expedition['no_person'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('expedition', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Attachment</label>
                <p class="form-control-static">
                    <?php if (empty($safeConduct['attachment'])): ?>
                        No uploaded file
                    <?php else: ?>
                        <a href="<?= base_url('uploads/safe_conducts/' . $safeConduct['attachment']) ?>">
                            <?= $safeConduct['attachment'] ?>
                        </a>
                    <?php endif; ?>
                </p>
                <input type="file" name="attachment" id="attachment"
                       accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                       placeholder="Select safe conduct document">
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Safe conduct description"
                          rows="1" maxlength="500"><?= set_value('description', $safeConduct['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group">
                <label for="related_safe_conduct">Related Safe Conduct (Group<?= if_empty($safeConduct['no_safe_conduct_group'], '', ' ') ?>)</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('safe-conduct/ajax-get-by-keyword?except=' . $safeConduct['id'] . '&check_in=0') ?>"
                        data-key-id="id" data-key-label="no_safe_conduct" data-key-sublabel="no_safe_conduct_group" data-key-sublabel2="customer_name"
                        name="related_safe_conducts[]" id="related_safe_conduct" multiple
                        data-placeholder="Select related safe conduct">
                    <option value="0">NO RELATED ANOTHER SAFE CONDUCT</option>
                    <?php foreach($relatedSafeConducts as $relatedSafeConduct): ?>
                        <option value="<?= $relatedSafeConduct['id'] ?>" selected>
                            <?= $relatedSafeConduct['no_safe_conduct'] ?>
                            <?= if_empty($relatedSafeConduct['no_safe_conduct_group'], '', ' - ') ?>
                            <?= if_empty($relatedSafeConduct['customer_name'], '', ' - ') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block mb0">Group safe conduct into group, some data may be in-sync later.</span>
                <span class="text-danger">All selected related safe conduct will be REPLACED with current safe conduct data (NO POLICE, VEHICLE and DRIVER).</span>
                <?= form_error('related_safe_conducts[]', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="field-booking">
                <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                    <label for="booking">Booking</label>
                    <select class="form-control select2" name="booking" id="booking" data-placeholder="Select booking data"
                        <?= $safeConduct['type'] == 'INBOUND' ? 'required' : '' ?>>
                        <option value=""></option>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?= $booking['id'] ?>" <?= set_select('booking', $booking['id'], $safeConduct['id_booking'] == $booking['id']) ?>>
                                <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>) - <?= $booking['customer_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Available for approved and generated DO booking only</span>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Existing safe conduct</h3>
                    </div>
                    <div class="box-body" id="existing-safe-conduct-wrapper">
                        <p class="text-muted">Safe conduct list for related booking</p>
                    </div>
                </div>

                <?php if (!empty($safeConduct['security_in_date'])): ?>
                    <?php $this->load->view('tally/_tally_editor', [
                        'inputSource' => 'STOCK',
                        'stockUrl' => site_url("safe-conduct/ajax_get_booking_data?id_booking={$safeConduct['id_booking']}&id_except_safe_conduct={$safeConduct['id']}"),
                        'withDetailContainer' => false,
                        'withDetailGoods' => false,
                    ]) ?>
                <?php elseif($safeConduct['type'] == 'INBOUND'): ?>
                    <div class="alert alert-warning">
                        You can update the container or goods after check in.
                    </div>
                <?php endif; ?>

            </div>

            <div id="field-work-order" style="<?= $safeConduct['type'] == 'OUTBOUND' ? '' : 'display: none' ?>">
                <div class="form-group <?= form_error('work_order') == '' ?: 'has-error'; ?>">
                    <label for="work_order">Job Order</label>
                    <select class="form-control select2" name="work_order" id="work_order" data-placeholder="Select job order data"
                        <?= $safeConduct['type'] == 'INBOUND' ? '' : 'required' ?> style="width: 100%">
                        <option value=""></option>
                        <?php foreach ($workOrders as $workOrder): ?>
                            <option value="<?= $workOrder['id'] ?>" <?= set_select('work_order', $workOrder['id'], $workOrder['id'] == $safeConduct['id_work_order']) ?>>
                                <?= $workOrder['no_work_order'] ?> - <?= $workOrder['handling_type'] ?> (<?= $workOrder['no_reference'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('work_order', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Completed job order only</span>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Job data</h3>
                    </div>
                    <div class="box-body" id="job-data-wrapper">
                        <p class="text-muted">Container or goods of related booking</p>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('tep') == '' ?: 'has-error'; ?>">
                <label for="tep">Transporter Entry Permit</label>
                <select class="form-control select2" name="tep" id="tep" <?= $safeConduct['expedition_type'] == 'EXTERNAL' ? 'required' : '' ?>
                        data-placeholder="Select TEP" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($transporterEntryPermits as $tep): ?>
                        <option value="<?= $tep['id'] ?>"<?= set_select('tep', $tep['id'], $tep['id'] == $safeConduct['id_transporter_entry_permit']) ?>>
                            <?php if($tep['category'] == "INBOUND"): ?>
                                <?= $tep['tep_code'] ?> - <?= $tep['customer_name'] ?> (<?= $tep['no_reference'] ?>)
                            <?php else: ?>
                                <?= $tep['tep_code'] ?> - <?= $tep['customer_name_out'] ?> (<?= $tep['receiver_no_police'] ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('expedition', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-safe-conduct">
                Update Safe Conduct
            </button>
        </div>
    </form>

    <?php else: ?>
        <form action="<?= site_url('safe-conduct/update/' . $safeConduct['id']) ?>?edit_type=update_data" role="form" method="post" class="edit" enctype="multipart/form-data">
            <div class="box-body">
            <?php $this->load->view('tally/_tally_editor', [
                'inputSource' => 'STOCK',
                'stockUrl' => site_url("safe-conduct/ajax_get_booking_data?id_booking={$safeConduct['id_booking']}&id_except_safe_conduct={$safeConduct['id']}"),
                'withDetailContainer' => false,
                'withDetailGoods' => false,
            ]) ?>
            </div>
            <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-safe-conduct">
                Update Safe Conduct
            </button>
        </div>
        </form>
    <?php endif; ?>
</div>
<?php $this->load->view('tally/_modal_container_input', [
    'bookingId' => $safeConduct['id_booking'],
    'customer' => $safeConduct['customer_name']
]) ?>
<?php $this->load->view('tally/_modal_goods_input', [
    'bookingId' => $safeConduct['id_booking'],
    'customer' => $safeConduct['customer_name']
]) ?>
<?php $this->load->view('tally/_modal_select_position') ?>
<script src="<?= base_url('assets/app/js/safe_conduct.js?v=22') ?>" defer></script>
