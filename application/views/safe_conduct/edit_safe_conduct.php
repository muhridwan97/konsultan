<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Safe Conduct</h3>
    </div>
    <form action="<?= site_url('safe-conduct/update-safe-conduct/' . $safeConduct['id']) ?>" role="form" method="post"
          id="form-safe-conduct" class="edit" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" name="id" id="id" value="<?= $safeConduct['id'] ?>">

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
</div>
<script src="<?= base_url('assets/app/js/safe_conduct.js?v=21') ?>" defer></script>
