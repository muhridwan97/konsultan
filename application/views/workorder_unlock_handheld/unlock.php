<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Work Order Unlock Handheld</h3>
    </div>

    <form action="<?= site_url('work-order-unlock-handheld/save/' . (empty($workOrder) ? '' : $workOrder['id'])) ?>" class="need-validation" method="post" id="form-work-order-unlock-handheld">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="work_order">Work Order</label>
                <?php if(empty($workOrder)): ?>
                    <select class="form-control select2 select2-ajax"
                            data-url="<?= site_url('work_order/ajax_search_work_order') ?>"
                            data-key-id="id" data-key-label="no_work_order" data-key-sublabel="customer_name"
                            name="work_order" id="work_order"
                            data-placeholder="Select work order">
                        <option value=""></option>
                        <?php if (!empty($workOrder)): ?>
                            <option value="<?= $workOrder['id'] ?>" selected>
                                <?= $workOrder['no_work_order'] ?>
                            </option>
                        <?php endif ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static">
                        <?= $workOrder['no_work_order'] ?> (<?= $workOrder['customer_name'] ?>)
                    </p>
                    <input type="hidden" name="work_order" value="<?= $workOrder['id'] ?>">
                <?php endif; ?>
            </div>

            <div class="form-group <?= form_error('unlocked_until') == '' ?: 'has-error'; ?>">
                <label for="unlocked_until">Unlocked Until</label>
                <input type="text" class="form-control datepicker-today" id="unlocked_until" name="unlocked_until" placeholder="Workorder unlock until" autocomplete="off"
                       required maxlength="20" value="<?= set_value('unlocked_until', format_date(get_if_exist($workOrderUnlockHandheld, 'unlocked_until'), 'd F Y')) ?>">
                <?= form_error('unlocked_until', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Description unlocking"
                          required maxlength="500"><?= set_value('description', get_if_exist($workOrderUnlockHandheld, 'description')) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Unlock Work Order
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/work-order-overtime.js') ?>" defer></script>