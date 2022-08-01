<div class="box box-primary" id="modal-confirm-check-tally">
    <div class="box-header">
        <h3 class="box-title">Job Component Detail</h3>
    </div>
    <div class="box-body">
        <div class="panel panel-default" style="margin-top: 10px" id="vas-wrapper">
        <div class="panel-heading">
            VALUE ADDITIONAL SERVICES 
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="activity_type">Activity Type VAS</label>
                    <div class="input-group col-xs-12">
                        <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="activity_type[]" id="activity_type" data-placeholder="-" style="width: 100%;" multiple>
                            <option value=""></option>
                            <?php if(!empty($component_data['activity_types'])): ?>
                                <?php foreach ($component_data['activity_types'] as $activity_type): ?>
                                    <option value="<?= $activity_type['id'] ?>" 
                                    <?= set_select('activity_type',$activity_type['id'], isset($component_data['activity_type_jobs'][$activity_type['id']])) ?> data-component="<?= $activity_type['handling_component'] ?>"><?= $activity_type['handling_component'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="description-vas-wrapper" style="display:none">
                <div class="form-group">
                    <label for="description_vas">Description VAS</label>
                    <div class="input-group col-xs-12">
                        <input class="form-control"  name="description_vas" id="description_vas" placeholder="-" style="width: 100%;">
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
                <?php foreach ($component_data['resources_types'] as $resources_type) : ?>
                    <?php
                        $hasResource = false;
                        foreach ($component_data['resources_type_jobs'] as $resources_type_job) {
                            if ($resources_type['id'] == $resources_type_job['id_component']) {
                                $hasResource = true;
                                break;
                            }
                        }
                     ?>
                <div class="col-sm-4">
                    <div class="checkbox" style="margin-top: 0">
                        <label>
                            <input disabled type="checkbox" name="resources[]" id="resources_<?= $resources_type['id'] ?>" value="<?= $resources_type['id'] ?>" data-component="<?= $resources_type['handling_component'] ?>"
                                <?php echo set_checkbox('resources', $resources_type['id'], $hasResource); ?>>
                            &nbsp; <?= ucwords(preg_replace('/(_|\-)/', ' ', $resources_type['handling_component'])) ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="row">
                <div id="forklift-wrapper" >
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="heavy_equipment">Heavy Equipment</label>
                            <div class="input-group col-xs-12">
                            <select disabled class="form-control select2 " data-key-id="id" data-key-label="name"  name="heavy_equipment[]" id="heavy_equipment" data-placeholder="-" style="width: 100%;" multiple>
                                <option value="INTERNAL" <?= set_select('heavy_equipment',$component_data['heavy_equipment'], in_array("INTERNAL",$component_data['heavy_equipment'])) ?>>INTERNAL</option>   
                                <option value="EXTERNAL" <?= set_select('heavy_equipment',$component_data['heavy_equipment'], in_array("EXTERNAL",$component_data['heavy_equipment'])) ?>>EXTERNAL</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="forklift-in-wrapper" >
                        <div class="form-group">
                            <label for="forklift">Forklift</label>
                            <div class="input-group col-xs-12">
                            <select disabled class="form-control select2 " data-key-id="id" data-key-label="forklift"  name="forklift[]" id="forklift" data-placeholder="-" style="width: 100%;" multiple>
                                <option value=""></option>
                                <?php if(!empty($component_data['forklifts'])): ?>
                                    <?php foreach ($component_data['forklifts'] as $forklift): ?>
                                        <option value="<?= $forklift['id'] ?>" <?= set_select('forklifts',$forklift['id'], isset($component_data['forklift_job_ids'][$forklift['id']])) ?>><?= $forklift['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="forklift-ex-wrapper" >
                        <div class="form-group">
                            <label for="forklift_external">Forklift External</label>
                            <div class="input-group col-xs-12">
                            <select disabled class="form-control select2 " data-key-id="id" data-key-label="forklift_external"  name="forklift_external[]" id="forklift_external" data-placeholder="-" style="width: 100%;" multiple>
                                <option value=""></option>
                                <?php if(!empty($component_data['forklifts_external'])): ?>
                                    <?php foreach ($component_data['forklifts_external'] as $forklift): ?>
                                        <option value="<?= $forklift['id'] ?>" <?= set_select('forklifts',$forklift['id'], isset($component_data['forklift_job_ids_ex'][$forklift['id']])) ?>><?= $forklift['heep_code'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="operator_name">Operator Name</label>
                            <div class="input-group col-xs-12">
                                <input disabled type="text" value="<?= $component_data['data_forklift']['operator_name'] ?>" name="operator_name" id="operator_name" class="form-control" placeholder="-" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="is_owned">Forklift Option</label>
                            <div class="input-group col-xs-12">
                                <select disabled class="form-control select2 " data-key-id="id" data-key-label="name"  name="is_owned" id="is_owned" data-placeholder="-" style="width: 100%;">
                                    <option value=""></option>   
                                    <option value="LEASED" <?= set_select('is_owned',$component_data['data_forklift']['is_owned'], $component_data['data_forklift']['is_owned']=='LEASED') ?>>LEASED</option>   
                                    <option value="OWNED" <?= set_select('is_owned',$component_data['data_forklift']['is_owned'], $component_data['data_forklift']['is_owned']=='OWNED') ?>>OWNED</option>
                                    <option value="BOTH" <?= set_select('is_owned',$component_data['data_forklift']['is_owned'], $component_data['data_forklift']['is_owned']=='BOTH') ?>>BOTH</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="capacity">Capacity</label>
                            <div class="input-group col-xs-12">
                                <input disabled type="number" value="<?= $component_data['data_forklift']['capacity'] ?>" min="0" max="100" step="1" name="capacity" id="capacity" class="form-control" placeholder="-">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="labours-wrapper">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="labours">Labours</label>
                            <div class="input-group col-xs-12">
                            <select disabled class="form-control select2 " data-key-id="id" data-key-label="labours"  name="labours[]" id="labours" data-placeholder="-" style="width: 100%;" multiple>
                                <option value=""></option>
                                <?php if(!empty($component_data['labours'])): ?>
                                    <?php foreach ($component_data['labours'] as $labour): ?>
                                        <option value="<?= $labour['id'] ?>" <?= set_select('labours',$labour['id'], isset($component_data['labour_job_ids'][$labour['id']])) ?>><?= $labour['nama_visitor'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div id="pallet-wrapper" >
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pallet">Stock Pallet</label>
                            <div class="input-group col-xs-12">
                                <input disabled type="number" value="<?= set_value('space', abs($workOrder['stock_pallet'])) ?>" min="0" step="1" name="pallet" id="pallet" class="form-control" placeholder="-">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>