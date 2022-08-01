<div class="panel panel-default" style="margin-top: 10px" id="vas-wrapper">
    <div class="panel-heading">
        VALUE ADDITIONAL SERVICES 
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="activity_type">Activity Type VAS</label>
                    <div class="input-group col-xs-12">
                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="activity_type[]" id="activity_type" data-placeholder="Select activity type" style="width: 100%;" multiple>
                            <option value=""></option>
                            <?php if(!empty($activity_types)): ?>
                                <?php foreach ($activity_types as $activity_type): ?>
                                    <option value="<?= $activity_type['id'] ?>" data-component="<?= $activity_type['handling_component'] ?>"><?= $activity_type['handling_component'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3" id="description-vas-wrapper" style="display:none">
                <div class="form-group">
                    <label for="description_vas">Description VAS</label>
                    <div class="input-group col-xs-12">
                        <input class="form-control"  name="description_vas" id="description_vas" placeholder="Select Description" style="width: 100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default" id="resources-wrapper">
    <div class="panel-heading">
        Resources
    </div>
    <div class="panel-body">
        <div class="row" id="checkbox-resources">
            <!-- sebenarnya pake javacript -->
            <div class="col-sm-4">
                <div class="checkbox" style="margin-top: 0">
                    <label>
                        <input type="checkbox" name="resources[]" id="resources_1 ?>" value="1"
                            <?php echo set_checkbox('resources', 1); ?>>
                        &nbsp; <?= ucwords(preg_replace('/(_|\-)/', ' ', 'Forklift')) ?>
                    </label>
                </div>
            </div>
            <?php foreach ($resources_types as $resources_type) : ?>
            <div class="col-sm-4">
                <div class="checkbox" style="margin-top: 0">
                    <label>
                        <input type="checkbox" name="resources[]" id="resources_<?= $resources_type['id'] ?>" value="<?= $resources_type['id'] ?>"
                            <?php echo set_checkbox('resources', $resources_type['id']); ?>>
                        &nbsp; <?= ucwords(preg_replace('/(_|\-)/', ' ', $resources_type['handling_component'])) ?>
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <!-- <div class="col-md-3">
                <div class="form-group">
                    <label for="resources">Resources</label>
                    <div class="input-group col-xs-12">
                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="resources" id="resources" data-placeholder="Select resources" style="width: 100%;">
                            <option value=""></option>   
                            <option value="0">Forklift & Labours</option>   
                            <option value="1">Only Forklift</option>
                            <option value="2">Only Labours</option>
                        </select>
                    </div>
                </div>
            </div> -->
            <div id="forklift-wrapper" style="display:none">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="heavy_equipment">Heavy Equipment</label>
                        <div class="input-group col-xs-12">
                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="heavy_equipment[]" id="heavy_equipment" data-placeholder="Select Heavy Equipment" style="width: 100%;" multiple>
                            <option value="INTERNAL">INTERNAL</option>   
                            <option value="EXTERNAL">EXTERNAL</option>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="forklift-in-wrapper" style="display:none">
                    <div class="form-group">
                        <label for="forklift">Heavy Equipment Type</label>
                        <div class="input-group col-xs-12">
                            <select class="form-control select2 " data-key-id="id" data-key-label="forklift"  name="forklift[]" id="forklift" data-placeholder="Select forklift" style="width: 100%;" multiple>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="forklift-ex-wrapper" style="display:none">
                    <div class="form-group">
                        <label for="forklift_external">Heavy Equipment Type External</label>
                        <div class="input-group col-xs-12">
                            <select class="form-control select2 " data-key-id="id" data-key-label="forklift_external"  name="forklift_external[]" id="forklift_external" data-placeholder="Select forklift external" style="width: 100%;" multiple>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="operator_name">Operator Name</label>
                        <div class="input-group col-xs-12">
                            <input type="text" value="" name="operator_name" id="operator_name" class="form-control" placeholder="Operator name" maxlength="50">
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="form-group">
                        <label for="is_owned">Forklift Option</label>
                        <div class="input-group col-xs-12">
                            <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="is_owned" id="is_owned" data-placeholder="Select option" style="width: 100%;">
                                <option value=""></option>   
                                <option value="LEASED">LEASED</option>   
                                <option value="OWNED">OWNED</option>
                                <option value="BOTH">BOTH</option>
                            </select>
                        </div>
                    </div>
                </div> -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <div class="input-group col-xs-12">
                            <input type="number" value="0" min="0" max="100" step="1" name="capacity" id="capacity" class="form-control" placeholder="Capacity">
                        </div>
                    </div>
                </div>
            </div>
            <div id="labours-wrapper" style="display:none">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="labours">Labours</label>
                        <div class="input-group col-xs-12">
                        <select class="form-control select2 " data-key-id="id" data-key-label="labours"  name="labours[]" id="labours" data-placeholder="Select labours" style="width: 100%;" multiple>
                            <option value=""></option>
                            <?php if(!empty($activity_types)): ?>
                                <?php foreach ($activity_types as $activity_type): ?>
                                    <option value="<?= $activity_type['id'] ?>" data-component="<?= $activity_type['handling_component'] ?>"><?= $activity_type['handling_component'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    </div>
                </div>
            </div>
            <div id="pallet-wrapper" style="display:none">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pallet">Pallet Need</label>
                        <div class="input-group col-xs-12">
                            <input type="number" value="0" min="0" step="1" name="pallet" id="pallet" class="form-control" placeholder="Stock Pallet">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default" id="space-wrapper">
    <div class="panel-heading">
        Space
    </div>
    <div class="panel-body">
        <div class="form-group <?= form_error('space') == '' ?: 'has-error'; ?>">
            <label for="space">Space (m<sup>2</sup>)</label>
            <input type="number" class="form-control" id="space" name="space" min="0" placeholder="Number of space" value="<?= set_value('space') ?>" step=".001">
            <?= form_error('space', '<span class="help-block">', '</span>'); ?>
        </div>
    </div>
</div>
