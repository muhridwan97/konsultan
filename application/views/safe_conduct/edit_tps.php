<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Safe Conduct - <?= $safeConduct['no_safe_conduct'] ?></h3>
    </div>

    <form action="<?= site_url('safe-conduct/update-tps/' . $safeConduct['id']) ?>" role="form" method="post" id="form-safe-conduct" class="edit">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

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

            <div class="form-group">
                <label for="tps_gate_out_date">TPS Gate Out Date</label>
                <input type="text" class="form-control datepicker" id="tps_gate_out_date" name="tps_gate_out_date"
                       placeholder="CY Date" value="<?= set_value('tps_gate_out_date', format_date($safeConduct['tps_gate_out_date'], 'd F Y')) ?>">
                <?= form_error('tps_gate_out_date', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-safe-conduct">
                Update Warehouse Origin
            </button>
        </div>
    </form>
</div>
