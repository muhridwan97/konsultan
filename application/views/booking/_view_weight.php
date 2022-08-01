<div class="form-horizontal form-view">
    <div class="box box-default">
        <div class="box-header">
            <h3 class="box-title">Total Weight</h3>
        </div>
        <div class="box-body" id="weight-wrapper">
            <div class="col-md-6">
                <div class="form-group <?= form_error('netto') == '' ?: 'has-error'; ?>">
                    <label for="netto">Total Weight</label>
                    <input type="text" class="form-control numeric" id="netto" name="netto"
                        placeholder="netto" readonly
                        value="<?= set_value('netto', numerical($booking['total_netto'], 3, true)) ?>">
                    <?= form_error('netto', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group <?= form_error('bruto') == '' ?: 'has-error'; ?>">
                    <label for="bruto">Total Gross Weight</label>
                    <input type="text" class="form-control numeric" id="bruto" name="bruto"
                        placeholder="bruto" readonly
                        value="<?= set_value('bruto', numerical($booking['total_bruto'], 3, true)) ?>">
                    <?= form_error('bruto', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
        </div>
    </div>
</div>