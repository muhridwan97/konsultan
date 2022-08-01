<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Vehicle</h3>
    </div>
    <form action="<?= site_url('vehicle/update/'.$vehicle['id']) ?>" role="form" class="need-validation" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                <label for="branch">Branch</label>
                <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                    <option value=""></option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= set_value('branch', $vehicle['id_branch']) == $branch['id'] ? 'selected' : '' ?>>
                            <?= $branch['branch'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('vehicle_name') == '' ?: 'has-error'; ?>">
                <label for="vehicle_name">Vehicle Name</label>
                <input type="text" class="form-control" id="vehicle_name" name="vehicle_name"
                       placeholder="Enter vehicle name"
                       required maxlength="50" value="<?= set_value('vehicle_name', $vehicle['vehicle_name']) ?>">
                <?= form_error('vehicle_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="col-md-12">
                <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                    <label for="status">Vehicle Status</label>
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="active">
                                <input type="radio" id="status" name="status" value="ACTIVE" <?= set_value('branch', $vehicle['status']) == "ACTIVE" ? 'checked' : '' ?>> Active
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <label for="inactive">
                                <input type="radio" id="status" name="status" value="INACTIVE" <?= set_value('branch', $vehicle['status']) == "ACTIVE" ? '' : 'checked' ?>> Inactive
                            </label>
                        </div>
                    </div>
                    <?= form_error('status', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('vehicle_type') == '' ?: 'has-error'; ?>">
                        <label for="vehicle_type">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                               placeholder="Enter vehicle type"
                               required maxlength="50" value="<?= set_value('vehicle_type', $vehicle['vehicle_type']) ?>">
                        <?= form_error('vehicle_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('no_plate') == '' ?: 'has-error'; ?>">
                        <label for="no_plate">Plate Number</label>
                        <input type="text" class="form-control" id="no_plate" name="no_plate"
                               placeholder="Vehicle plate police"
                               required maxlength="50" value="<?= set_value('no_plate', $vehicle['no_plate']) ?>">
                        <?= form_error('no_plate', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Vehicle description"
                          required maxlength="500"><?= set_value('description', $vehicle['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Vehicle</button>
        </div>
    </form>
</div>