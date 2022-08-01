<div style="display:none;" id="type-work-order">
    <div class="form-group <?= form_error('work_order') == '' ?: 'has-error'; ?>">
        <label for="work_order">Job Order</label>
        <select class="form-control select2" id="work_order" name="work_order"
                style="width: 100%" data-placeholder="Select related job">
            <option value=""></option>
            <?php if(isset($workOrders)): ?>
                <?php foreach ($workOrders as $workOrder): ?>
                    <option value="<?= $workOrder['id'] ?>" <?= set_select('work_order', $workOrder['id']) ?>>
                        <?= $workOrder['no_work_order'] ?>
                    </option>
                <?php endforeach ?>
            <?php endif; ?>
        </select>
        <?= form_error('work_order', '<span class="help-block">', '</span>'); ?>
    </div>
</div>