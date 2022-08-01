<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Handling Component</h3>
    </div>
    <form action="<?= site_url('component/update/' . $component['id']) ?>" class="need-validation" role="form" method="post"
          id="form-handling-component">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_component') == '' ?: 'has-error'; ?>">
                        <label for="handling_component">Handling Component</label>
                        <input type="text" class="form-control" id="handling_component" name="handling_component"
                               placeholder="Enter handling component name"
                               required maxlength="100" value="<?= set_value('handling_component', $component['handling_component']) ?>">
                        <?= form_error('handling_component', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('component_category') == '' ?: 'has-error'; ?>">
                        <label for="component_category">Component Category</label>
                        <input type="text" class="form-control" id="component_category" name="component_category"
                               placeholder="Handling component category"
                               required maxlength="50" value="<?= set_value('component_category', $component['component_category']) ?>">
                        <?= form_error('component_category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Handling Component Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Handling component description"
                          required maxlength="500"><?= set_value('description', $component['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-handling-component">
                Update Handling Component
            </button>
        </div>
    </form>
</div>
