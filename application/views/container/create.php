<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Container</h3>
    </div>

    <form action="<?= site_url('container/save') ?>" role="form" method="post" class="need-validation">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('shipping_line') == '' ?: 'has-error'; ?>">
                <label for="shipping_line">Shipping Line</label>
                <select class="form-control select2" name="shipping_line" id="shipping_line" data-placeholder="Select shipping line" required>
                    <option value=""></option>
                    <?php foreach ($shippingLines as $shippingLine): ?>
                        <option value="<?= $shippingLine['id'] ?>" <?= set_select('shipping_line', $shippingLine['id']) ?>>
                            <?= $shippingLine['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('shipping_line', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('no_container') == '' ?: 'has-error'; ?>">
                <label for="no_container">Container Number</label>
                <input type="text" class="form-control" id="no_container" name="no_container"
                       placeholder="Enter Container Number"
                       required maxlength="15" value="<?= set_value('no_container') ?>">
                <?= form_error('no_container', '<span class="help-block">', '</span>'); ?>
            </div>
			<div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Container Type</label>
				<select class="form-control select2" id="type" name="type" required="true" data-placeholder="Select Type" style="width: 100%">
					<option value=""></option>
					<option value="STD" <?php echo set_select('type', 'STD'); ?> >STD</option>
					<option value="HC" <?php echo set_select('type', 'HC'); ?> >HC</option>
					<option value="OT" <?php echo set_select('type', 'OT'); ?> >OT</option>
					<option value="FR" <?php echo set_select('type', 'FR'); ?>>FR</option>
					<option value="TANK" <?php echo set_select('type', 'TANK'); ?>>Tank</option>
				</select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>
			<div class="form-group <?= form_error('size') == '' ?: 'has-error'; ?>">
                <label for="size">Container Size</label>
				<select class="form-control select2" id="size" name="size" data-placeholder="Select Size" required="true" style="width: 100%">
					<option value=""></option>
					<option value="20" <?php echo set_select('size', '20'); ?> >20 Feet</option>
					<option value="40" <?php echo set_select('size', '40'); ?> >40 Feet</option>
					<option value="45" <?php echo set_select('size', '45'); ?> >45 Feet</option>
				</select>
                <?= form_error('size', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Container Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Container description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Container</button>
        </div>
    </form>
</div>