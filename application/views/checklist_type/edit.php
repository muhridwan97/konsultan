<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Checklist Type</h3>
    </div>
    <form action="<?= site_url('checklist-type/update/' . $checklist_type['id']) ?>" class="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('checklist_type') == '' ?: 'has-error'; ?>">
                <label for="checklist_type">Checklist Type Name</label>
                <input type="text" class="form-control" id="checklist_type" name="checklist_type"
                       placeholder="Enter checklist type name"
                       value="<?= set_value('checklist_type', $checklist_type['checklist_type']) ?>">
                <?= form_error('unit', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('subtype') == '' ?: 'has-error'; ?>">
                <label for="subtype">Checklist Subtype</label>
                <select class="form-control select2" name="subtype" id="price_subtype"
                        data-placeholder="Select checklist subtype" style="width: 100%" >
                    <option value=""></option>
                    <option value="CONTAINER" <?= set_select('subtype', 'CONTAINER', $checklist_type['subtype'] == 'CONTAINER') ?>>
                        CONTAINER
                    </option>
                    <option value="GOODS" <?= set_select('subtype', 'GOODS', $checklist_type['subtype'] == 'GOODS') ?>>
                        GOODS
                    </option>
                </select>
                <?= form_error('price_subtype', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Unit</button>
        </div>
    </form>
</div>