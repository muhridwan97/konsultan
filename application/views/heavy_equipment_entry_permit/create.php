<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Entry Permit</h3>
    </div>

    <form action="<?= site_url('heavy-equipment-entry-permit/save') ?>" role="form" method="post" id="form-heep">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="relate">Heavy Equipment Entry Permit Relate</label>
                <select class="form-control select2 select2" required name="relate" id="relate" data-placeholder="Select Relate">
                    <option value=""></option>
                    <option value="PURCHASE">PURCHASE</option>
                    <option value="HEEP">HEEP</option>
                </select>
            </div>

            <div class="form-group order-wrapper">
                <label for="purchase_order">NO Requisition</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('heavy_equipment_entry_permit/ajax_get_heavy_equipment') ?>"
                        data-key-id="id" data-key-label="no_requisition" data-key-sublabel="Requisition"
                        name="purchase_order" id="purchase_order"
                        data-placeholder="Select requisition" >
                    <option value=""></option>
                </select>
            </div>

            <div class="form-group <?= form_error('heep_reference') == '' ?: 'has-error'; ?> heep-wrapper">
                <label for="heep_reference">HEEP Reference</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('heavy_equipment_entry_permit/ajax_get_heep') ?>"
                        data-key-id="id" data-key-label="heep_code"
                        name="heep_reference" id="heep_reference"
                        data-placeholder="Select heep reference" style="width: 100%">
                    <option value=""></option>
                </select>
                <?= form_error('heep_reference', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Generate HEEP
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/heep.js?v=0') ?>" defer></script>
