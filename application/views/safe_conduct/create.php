<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Safe Conduct</h3>
    </div>
    <form action="<?= site_url('safe-conduct/save') ?>" role="form" method="post" id="form-safe-conduct" class="need-validation" enctype="multipart/form-data">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                        <label for="category">Category</label>
                        <select class="form-control select2" name="category" id="category"
                                data-placeholder="Select category" style="width: 100%" required>
                            <option value=""></option>
                            <option value="INBOUND" <?= set_select('category', 'INBOUND', (isset($_GET['category']) ? $_GET['category'] : '') == 'INBOUND') ?>>INBOUND</option>
                            <option value="OUTBOUND" <?= set_select('category', 'OUTBOUND', (isset($_GET['category']) ? $_GET['category'] : '') == 'OUTBOUND') ?>>OUTBOUND</option>
                        </select>
                        <?= form_error('category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('warehouse') == '' ?: 'has-error'; ?>">
                        <label for="warehouse">Warehouse of Origin</label>
                        <select class="form-control select2" name="warehouse" id="warehouse"
                                data-placeholder="Select warehouse of origin" style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" <?= set_select('warehouse', $warehouse['id']) ?>>
                                    <?= $warehouse['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('warehouse', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="field-work-order" style="display: none">
                <div class="form-group <?= form_error('work_order') == '' ?: 'has-error'; ?>">
                    <label for="work_order">Job Order</label>
                    <select class="form-control select2" name="work_order" id="work_order"
                            data-placeholder="Select job order data" required style="width: 100%;">
                        <option value=""></option>
                        <?php foreach ($workOrders as $workOrder): ?>
                            <option value="<?= $workOrder['id'] ?>" data-required-tep="<?= $workOrder['id_handling_type'] == get_setting('default_outbound_handling') ?>">
                                <?= $workOrder['no_work_order'] ?> - <?= $workOrder['no_chassis'] ?> <?= $workOrder['handling_type'] ?> (<?= $workOrder['no_reference'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('work_order', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Completed job and approved (CASH AND CARRY) booking order only</span>
                </div>
            </div>
            <div id="expedition-type-text-wrapper" class="form-group <?= form_error('expedition_type') == '' ?: 'has-error'; ?>" style="display: none">
                <label for="expedition_type">Expedition Type</label>
                <input class="form-control" name="expedition_type_text" id="expedition_type_text"
                        placeholder="Select expedition type" style="width: 100%" readonly>
            </div>
            <div id="expedition-type-wrapper" class="form-group <?= form_error('expedition_type') == '' ?: 'has-error'; ?>">
                <label for="expedition_type">Expedition Type</label>
                <select class="form-control select2" name="expedition_type" id="expedition_type"
                        data-placeholder="Select expedition type" style="width: 100%" >
                    <option value=""></option>
                    <option value="INTERNAL" <?= set_select('expedition_type', 'INTERNAL') ?>>
                        INTERNAL (PT. Transcon Indonesia)
                    </option>
                    <option value="EXTERNAL" <?= set_select('expedition_type', 'EXTERNAL') ?>>
                        EXTERNAL (Other expedition company)
                    </option>
                </select>
                <?= form_error('expedition_type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group cy-wrapper" style="display: none">
                <label for="cy_date">CY Date</label>
                <input type="text" class="form-control daterangepicker2" id="cy_date" value="<?= set_value('cy_date') ?>" name="cy_date" placeholder="CY Date" autocomplete="off">
                <?= form_error('cy_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row" id="internal-expedition-wrapper" style="<?= set_value('expedition') == 'INTERNAL' ? 'display: none' : '' ?>">
                <div class="col-md-4" data-toggle="tooltip">
                    <div class="form-group <?= form_error('vehicle') == '' ?: 'has-error'; ?>">
                        <label for="vehicle">Vehicle</label>
                        <select class="form-control select2" name="vehicle" id="vehicle" data-placeholder="Select vehicle" style="width: 100%" required>
                            <option value=""></option>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['vehicle_name'] ?>" data-no-police="<?= $vehicle['no_plate'] ?>" data-id="<?= $vehicle['id'] ?>"
                                    <?= set_select('vehicle', $vehicle['vehicle_name']) ?>>
                                    <?= $vehicle['vehicle_type'] ?> - <?= $vehicle['vehicle_name'] ?> (<?= $vehicle['no_plate'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block">
                            Add more vehicle? <a href="<?= site_url('vehicle/create') ?>" target="_blank">click here</a>.
                        </span>
                        <input type="hidden" name="no_police" id="no_police" value="<?= set_value('no_police', 0) ?>">
                        <?= form_error('vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('driver') == '' ?: 'has-error'; ?>">
                        <label for="driver">Driver</label>
                        <select class="form-control select2" name="driver" id="driver" style="width: 100%" data-placeholder="Select driver" required>
                            <option value=""></option>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?= $driver['name'] ?>" <?= set_select('driver', $driver['name']) ?>>
                                    <?= $driver['name'] ?> (<?= $driver['no_person'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block">
                            Add more driver? <a href="<?= site_url('people/create?type=DRIVER') ?>" target="_blank">click here</a>.
                        </span>
                        <?= form_error('driver', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('expedition') == '' ?: 'has-error'; ?>">
                        <label for="expedition">Expedition</label>
                        <p class="form-control-static">PT. Transcon Indonesia</p>
                        <input type="hidden" class="form-control" id="expedition" name="expedition" required value="PT. Transcon Indonesia">
                        <?= form_error('expedition', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div id="field-eseal" class="form-group <?= form_error('eseal') == '' ?: 'has-error'; ?>" style="display: none">
                <label for="eseal">E-seal</label>
                <select class="form-control select2" name="eseal" id="eseal" style="width: 100%" data-placeholder="Select available e-seal">
                    <option value=""></option>
                    <?php foreach ($eseals as $eseal): ?>
                        <option value="<?= $eseal['id'] ?>" <?= set_select('eseal', $eseal['id']) ?>>
                            <?= $eseal['no_eseal'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">
                    Add more e-seal? <a href="<?= site_url('eseal/create') ?>" target="_blank">click here</a>. If your not found your eseal,
                    it may used in another truck (<a href="<?= site_url('eseal') ?>">find here</a>), try to security or gate out to release the e-seal.
                </span>
                <?= form_error('eseal', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row" id="external-expedition-wrapper" style="<?= set_value('expedition') == 'EXTERNAL' ? 'display: none' : '' ?>">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" id="attachment"
                               accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                               placeholder="Select safe conduct document">
                        <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group <?= form_error('expedition') == '' ?: 'has-error'; ?>">
                        <label for="expedition">Expedition</label>
                        <select class="form-control select2" name="expedition" id="expedition"
                                data-placeholder="Select expedition" <?= set_value('expedition') == 'EXTERNAL' ? 'required' : '' ?> style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($expeditions as $expedition): ?>
                                <option value="<?= $expedition['name'] ?>"
                                    <?= set_select('expedition', $expedition['name']) ?>>
                                    <?= $expedition['name'] ?> (<?= $expedition['no_person'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block">
                            Need new expedition? <a href="<?= site_url('people/create?type=EXPEDITION') ?>" target="_blank">click here</a> if you do not find your company.
                        </span>
                        <?= form_error('expedition', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Safe conduct description"
                          rows="1" maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group">
                <label for="related_safe_conduct">Related Safe Conduct (Group)</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('safe-conduct/ajax-get-by-keyword?check_in=0') ?>"
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
                <span class="help-block mb0">Group safe conduct into group, some create may be in-sync later.</span>
                <span class="text-danger">All selected safe conduct will be REPLACED with current safe conduct data (NO POLICE, VEHICLE and DRIVER).</span>
                <?= form_error('related_safe_conducts[]', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="field-booking" style="display: none">
                <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                    <label for="booking">Booking</label>
                    <select class="form-control select2" name="booking" id="booking" style="width: 100%"
                            data-placeholder="Select booking data" required>
                        <option value=""></option>
                        <?php if(!empty($booking)): ?>
                            <option value="<?= $booking['id'] ?>" selected>
                                <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>) - <?= $booking['customer_name'] ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Available for approved and generated DO booking only</span>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Existing safe conduct</h3>
                    </div>
                    <div class="box-body" id="existing-safe-conduct-wrapper" style="max-height: 250px; overflow-y: auto">
                        <p class="text-muted">Safe conduct list for related booking</p>
                    </div>
                </div>
            </div>

            <div id="field-work-order" class="field-work-order" style="display: none">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Job data</h3>
                    </div>
                    <div class="box-body" id="job-data-wrapper">
                        <p class="text-muted">Container or goods of related booking</p>
                    </div>
                </div>
            </div>

            <div id="field-tep" class="form-group <?= form_error('tep') == '' ?: 'has-error'; ?>">
                <div id="tep-text-wrapper" class="form-group <?= form_error('expedition_type') == '' ?: 'has-error'; ?>" style="display: none">
                    <label for="tep_text">Transporter Entry Permit</label>
                    <input class="form-control" name="tep_text" id="tep_text"
                        placeholder="Select Transporter Entry Permit" style="width: 100%" readonly>
                </div>
                <div id="tep-wrapper" class="form-group <?= form_error('tep') == '' ?: 'has-error'; ?>">
                    <label for="tep">Transporter Entry Permit</label>
                    <select class="form-control select2" name="tep" id="tep" data-placeholder="Select TEP" style="width: 100%">
                        <option value=""></option>
                        <?php foreach ($transporterEntryPermits as $tep): ?>
                            <option value="<?= $tep['id'] ?>"<?= set_select('tep', $tep['id']) ?>>
                                <?= $tep['tep_code'] ?> - <?= $tep['customer_name'] ?> (<?= $tep['no_reference'] ?>) - <?= $tep['receiver_no_police'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('tep', '<span class="help-block">', '</span>'); ?>
                </div>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Transporter Entry Permit Data</h3>
                    </div>
                    <div class="box-body" id="tep-data-wrapper">
                        <p class="text-muted">Container or goods of related booking</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right" id="btn-save-safe-conduct">
                Save Safe Conduct
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/safe_conduct.js?v=22') ?>" defer></script>
