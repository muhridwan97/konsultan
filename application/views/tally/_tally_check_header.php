<input type="hidden" id="tally-validated-edit" value="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">

<div class="row" style="margin-bottom: 20px">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-3">No Job</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= $workOrder['no_work_order'] ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">No Handling</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= $workOrder['no_handling'] ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">Handling Type</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= $workOrder['handling_type'] ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">Customer</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= $workOrder['customer_name'] ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">Branch</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= $workOrder['branch'] ?>
                </p>
            </div>
        </div>
        <?php if (isset($workOrder['transporter_category']) ): ?>
            <?php if ($workOrder['transporter_category'] == "EXTERNAL"): ?>
            <div class="form-group">
                <label class="col-sm-3">Transporter</label>
                <div class="col-sm-9">
                    <select name="transporter" id="transporter" class="form-control select2" data-placeholder="Select TEP">
                        <option value=""></option>
                        <?php foreach($tep_data AS $tep): ?>
                            <option value="<?= $tep['id'] ?>" <?= set_select('transporter', $tep['id'], $workOrder['id_transporter_entry_permit'] == $tep['id']) ?>>
                                <?= $tep['tep_code'] ?> (<?= $tep['receiver_no_police'] ?>) => <?= date('d F Y',strtotime($tep['checked_in_at'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
                <div class="form-group">
                    <label for="chassis" class="col-sm-3">Chassis</label>
                    <div class="col-sm-9">
                        <select class="form-control select2" name="chassis" id="chassis" data-placeholder="Select chassis" style="width: 100%;" required>
                            <option value=""></option>
                            <option value="0">No Chassis Reference</option>
                            <?php foreach ($outstandingChassis as $chassis): ?>
                                <option value="<?= $chassis['id'] ?>"<?= set_select('chassis', $chassis['id'], $chassis['id'] == $workOrder['id_tep_chassis']) ?>>
                                    <?= $chassis['no_chassis'] ?> (<?= $chassis['tep_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php else: ?>
            <div class="form-group">
                <label class="col-sm-3">Transporter</label>
                <div class="col-sm-9">
                    <select name="transporter" id="transporter" class="form-control select2" data-placeholder="Select TEP">
                        <option value=""></option>
                        <?php foreach($vehicle AS $vehicle): ?>
                            <option value="<?= $vehicle['id'] ?>" <?= set_select('transporter', $vehicle['id'], $workOrder['id_vehicle'] == $vehicle['id']) ?>>
                                <?= $vehicle['vehicle_name'] ?> (<?= $vehicle['no_plate'] ?>) 
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
        <input type="text" value="<?=$workOrder['transporter_category']?>" id="transporter_category" name="transporter_category" hidden>
        <?php endif; ?>
        <?php if (isset($workOrder['space']) && ($workOrder['handling_type'] == "LOAD" || $workOrder['handling_type'] == "STRIPPING" || (empty($containers) && $workOrder['handling_type'] == "UNLOAD")) ): ?>
        <div class="form-group">
            <label class="col-sm-3">Space (m<sup>2</sup>)</label>
            <div class="col-sm-9">
                <div class="form-group <?= form_error('space') == '' ?: 'has-error'; ?>">
                    <input type="number" class="form-control" id="space" name="space" min="0" placeholder="Number of space" value="<?= set_value('space', abs($workOrder['space'])) ?>" step=".001">
                    <?= form_error('space', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-3">Queue</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="margin-top: 0">
                    <?= $workOrder['queue'] ?><sup>
                        <?php
                        if ($workOrder['queue'] % 10 == 1) {
                            echo 'st';
                        } else if ($workOrder['queue'] % 10 == 2) {
                            echo 'nd';
                        } else if ($workOrder['queue'] % 10 == 3) {
                            echo 'rd';
                        } else {
                            echo 'th';
                        }
                        ?></sup>
                    at <?= (new DateTime($workOrder['created_at']))->format('d F Y') ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Gate In</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= is_null($workOrder['gate_in_date']) ? '-' : (new DateTime($workOrder['gate_in_date']))->format('d F Y H:i') ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Gate Out</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?= is_null($workOrder['gate_out_date']) ? '-' : (new DateTime($workOrder['gate_out_date']))->format('d F Y H:i') ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">Description</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="padding-top: 0">
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT) && $workOrder['status_validation'] != WorkOrderModel::STATUS_VALIDATION_VALIDATED && ((date('Y-m-d', strtotime($workOrder['completed_at'])) <= date('Y-m-d')) && (date('Y-m-d') <= date('Y-m-d', strtotime('+1 day', strtotime($workOrder['completed_at']))))) ): ?>
                       <textarea class="form-control" id="description" name="description" placeholder="Work order description"
                          required maxlength="500" rows="1"><?= set_value('description', $workOrder['description']) ?></textarea>
                    <?php else: ?>
                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) && $workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_VALIDATED): ?>
                       <textarea class="form-control" id="description" name="description" placeholder="Work order description"
                          required maxlength="500" rows="1"><?= set_value('description', $workOrder['description']) ?></textarea>
                    <?php else: ?>
                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_CREATE)): ?>
                       <textarea class="form-control" id="description" name="description" placeholder="Work order description"
                          required maxlength="500" rows="1"><?= set_value('description', $workOrder['description']) ?></textarea>
                    <?php else: ?>
                            <?= $workOrder['description'] == '' ? '-' : $workOrder['description'] ?>
                        <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3">Warehouse</label>
            <div class="col-sm-9">
                <select name="warehouse" id="warehouse" class="form-control select2" required>
                    <?php foreach ($warehouses as $warehouse): ?>
                        <option value="<?= $warehouse['id'] ?>">
                            <?= $warehouse['warehouse'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>
