<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Position</h3>
    </div>
    <form action="<?= site_url('position/save') ?>" role="form" class="need-validation" method="post" id="form-position">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('warehouse') == '' ?: 'has-error'; ?>">
                <label for="warehouse">Warehouse</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('warehouse/ajax_get_all') ?>"
                        data-key-id="id" data-key-label="warehouse" data-key-sublabel="type"
                        name="warehouse" id="warehouse" data-placeholder="Select warehouse" required>
                    <option value=""></option>
                </select>
                <?= form_error('warehouse', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header with-border">
                    <h3 class="box-title">Map Block Positions</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('position_type') == '' ?: 'has-error'; ?>">
                                <label for="position_type">Type</label>
                                <select name="position_type" id="position_type" class="form-control select2" data-placeholder="Select type">
                                    <option value=""></option>
                                    <?php foreach ($positionTypes as $positionType): ?>
                                        <option value="<?= $positionType['id'] ?>" data-is-usable="<?= $positionType['is_usable'] ?>" data-color="<?= $positionType['color'] ?>">
                                            <?= $positionType['position_type'] ?> - <?= $positionType['is_usable'] ? 'USABLE AREA' : 'BLOCKED AREA' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('position_type', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('position') == '' ?: 'has-error'; ?>">
                                <label for="position">Position</label>
                                <input type="text" class="form-control" id="position" name="position"
                                       placeholder="Enter position name"
                                       required maxlength="50" value="<?= set_value('position') ?>">
                                <?= form_error('position', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                                <label for="customer">Customer</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('people/ajax_get_people') ?>"
                                        data-key-id="id" data-key-label="name"
                                        name="customer" id="customer" data-placeholder="Select customer" required>
                                    <option value=""></option>
                                </select>
                                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="map-wrapper" id="map-wrapper">
                        <span class="text-danger">Please select warehouse first</span>
                    </div>

                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Position Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Position description"
                                  maxlength="500"><?= set_value('description') ?></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save Position</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/position.js?v=2') ?>" defer></script>