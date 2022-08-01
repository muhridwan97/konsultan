<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Checklist Type</h3>
    </div>
    <form action="<?= site_url('checklist/update/' . $checklist['id']) ?>" class="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('checklist') == '' ?: 'has-error'; ?>">
                <label for="checklist">Checklist Name</label>
                <input type="text" class="form-control" id="checklist" name="checklist"
                       placeholder="Enter checklist name"
                       value="<?= set_value('checklist', $checklist['description']) ?>">
                <?= form_error('checklist', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('checklist_type') == '' ?: 'has-error'; ?>">
                <label for="checklist_type">Checklist Type</label>
                <select class="form-control select2" name="checklist_type" id="checklist_type" data-placeholder="Select checklist type" required>
                    <option value=""></option>
                    <?php foreach ($checklist_types as $checklist_type): ?>
                        <option value="<?= $checklist_type['id'] ?>" <?= set_value('checklist_type', $checklist['id_checklist_type']) == $checklist_type['id'] ? 'selected' : '' ?>>
                            <?= $checklist_type['checklist_type'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('checklist_type', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update checklist</button>
        </div>
    </form>
</div>