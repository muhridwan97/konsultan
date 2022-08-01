<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Extension Field</h3>
    </div>

    <form action="<?= site_url('extension_field/save') ?>" role="form" method="post" id="extension-create">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('field_title') == '' ?: 'has-error'; ?>">
                        <label for="field_title">Field Title</label>
                        <input type="text" class="form-control" id="field_title" name="field_title"
                               placeholder="Enter field type"
                               required maxlength="50" value="<?= set_value('field_title') ?>">
                        <?= form_error('field_title', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('field_name') == '' ?: 'has-error'; ?>">
                        <label for="field_name">Field Name</label>
                        <input type="text" class="form-control" id="field_name" name="field_name"
                               placeholder="Unique field name (alphanumeric)"
                               required maxlength="50" value="<?= set_value('field_name') ?>">
                        <?= form_error('field_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Type</label>
                <select class="form-control select2 select-extension-type" id="type" name="type"
                       data-placeholder="Enter type field" required>
                    <option value=""></option>
                    <?php foreach (ExtensionFieldModel::EXTENSION_TYPE as $type): ?>
                        <option value="<?= $type ?>" <?= set_select('type', $type) ?>><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box box-primary" id="option-wrapper" style="display: none">
                <div class="box-header with-border">
                    Field Option
                </div>
                <div class="box-body">

                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Field Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Field help text"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Save Extension Field
            </button>
        </div>
    </form>
</div>

<script id="option-text-template" type="text/x-custom-template">
    <div class="row">
        <div class="col-md-3">
            <label for="required">Is Required</label>
            <div class="checkbox icheck">
                <label>
                    <input type="checkbox" name="options[required]" id="required" value="true" checked> Required
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <label for="minlength">Min Length</label>
            <input type="number" min="0" value="0" class="form-control" id="minlength" name="options[minlength]" placeholder="Max length of character">
        </div>
        <div class="col-md-3">
            <label for="maxlength">Max Length</label>
            <input type="number" min="1" value="50" class="form-control" id="maxlength" name="options[maxlength]" placeholder="Min length of character">
        </div>
        <div class="col-md-3">
            <label for="pattern">Pattern</label>
            <input type="text" class="form-control" id="pattern" name="options[pattern]" placeholder="Rule pattern">
        </div>
    </div>
</script>

<script id="option-number-template" type="text/x-custom-template">
    <div class="row">
        <div class="col-md-3">
            <label for="required">Is Required</label>
            <div class="checkbox icheck">
                <label>
                    <input type="checkbox" name="options[required]" id="required" value="true" checked> Required
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <label for="min">Min Value</label>
            <input type="number" min="0" value="0" class="form-control" id="min" name="options[min]" placeholder="Max value of number">
        </div>
        <div class="col-md-3">
            <label for="max">Max Value</label>
            <input type="number" min="1" value="50" class="form-control" id="max" name="options[max]" placeholder="Min value of number">
        </div>
        <div class="col-md-3">
            <label for="step">Step</label>
            <select name="options[step]" id="step" class="form-control select2">
                <option value="any">Any</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="100">100</option>
                <option value="1000">1000</option>
            </select>
        </div>
    </div>
</script>

<script id="option-multi-template" type="text/x-custom-template">
    <div class="form-group">
        <label for="required">Is Required</label>
        <div class="checkbox icheck">
            <div class="checkbox icheck">
                <label>
                    <input type="checkbox" name="options[required]" id="required" value="true" checked> Required
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="value">Value</label>
        </div>
        <div class="col-md-6">
            <label for="label">Label</label>
        </div>
    </div>
    <div class="form-group" id="fields-wrapper">
        <div class="row" style="margin-bottom: 5px">
            <div class="col-md-6">
                <input type="text" class="form-control" id="value" name="options[value][]" placeholder="Value of input">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" id="value" name="options[label][]" placeholder="Label of value">
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger btn-sm btn-remove-field" type="button">
                    <i class="ion-trash-b"></i>
                </button>
            </div>
        </div>
        <div class="row" style="margin-bottom: 5px">
            <div class="col-md-6">
                <input type="text" class="form-control" id="value" name="options[value][]" placeholder="Value of input">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" id="value" name="options[label][]" placeholder="Label of value">
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger btn-sm btn-remove-field" type="button">
                    <i class="ion-trash-b"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-block" id="btn-add-field">
            ADD FIELD ITEM
        </button>
    </div>
</script>

<script id="row-multi-template" type="text/x-custom-template">
    <div class="row" style="margin-bottom: 5px">
        <div class="col-md-6">
            <input type="text" class="form-control" id="value" name="options[value][]" placeholder="Value of input">
        </div>
        <div class="col-md-5">
            <input type="text" class="form-control" id="value" name="options[label][]" placeholder="Label of value">
        </div>
        <div class="col-md-1">
            <button class="btn btn-danger btn-sm btn-remove-field" type="button">
                <i class="ion-trash-b"></i>
            </button>
        </div>
    </div>
</script>

<script src="<?= base_url('assets/app/js/extension_field.js') ?>" defer></script>