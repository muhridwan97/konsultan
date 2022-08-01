<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create New Conversion</h3>
	</div>
	<form action="<?= site_url('conversion/save/') ?>" class="form need-validation" method="post">
		<div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            
			<div class="form-group <?= form_error('goods') == '' ?: 'has-error'; ?>">
				<label for="goods">Goods</label>
				<select class="form-control select2" name="goods" id="goods" data-placeholder="Select goods" required>
                    <option value=""></option>
                    <?php foreach ($goods as $item): ?>
                        <option value="<?= $item['id'] ?>" <?= set_value('goods') == $item['id'] ? 'selected' : '' ?>>
                            <?= $item['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
				<?= form_error('goods', '<span class="help-block">', '</span>'); ?>
			</div>
            
			<div class="form-group <?= form_error('unit_from') == '' ?: 'has-error'; ?>">
				<label for="unit_from">Unit From</label>
				<select class="form-control select2" name="unit_from" id="unit_from" data-placeholder="Select unit converted from" required>
                    <option value=""></option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['id'] ?>" <?= set_value('unit_from') == $unit['id'] ? 'selected' : '' ?>>
                            <?= $unit['unit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
				<?= form_error('unit_from', '<span class="help-block">', '</span>'); ?>
			</div>
            
			<div class="form-group <?= form_error('value') == '' ?: 'has-error'; ?>">
				<label for="value">Value</label>
				<input type="number" min=0 class="form-control" id="value" name="value" placeholder="Enter Value" value="<?= set_value('value') ?>" required>
				<?= form_error('value', '<span class="help-block">', '</span>'); ?>
			</div>
            
			<div class="form-group <?= form_error('unit_to') == '' ?: 'has-error'; ?>">
				<label for="unit_to">Unit To</label>
				<select class="form-control select2" name="unit_to" id="unit_to" data-placeholder="Select unit converted to" required>
                    <option value=""></option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['id'] ?>" <?= set_value('unit_to') == $unit['id'] ? 'selected' : '' ?>>
                            <?= $unit['unit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
				<?= form_error('unit_to', '<span class="help-block">', '</span>'); ?>
			</div>
		</div>
		<div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
			<button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Conversion</button>
		</div>
	</form>
</div>