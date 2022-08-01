<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Component Price</h3>
    </div>
    <form action="<?= site_url('component_price/save/') ?>" class="form" method="post" id="form-component-price">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if ($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                            <label for="branch">Branch</label>
                            <select class="form-control select2" name="branch" id="branch"
                                    data-placeholder="Select branch"
                                    style="width: 100%" required>
                                <option value=""></option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= set_value('branch') == $branch['id'] ? 'selected' : '' ?>>
                                        <?= $branch['branch'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-add-all-customer="true"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer" style="width: 100%">
                            <option value="">ALL CUSTOMER</option>
                        </select>
                        <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('effective_date') == '' ?: 'has-error'; ?>">
                        <label for="effective_date">Effective Date</label>
                        <input type="text" class="form-control datepicker" id="effective_date"
                               name="effective_date"
                               placeholder="Effective date" required
                               value="<?= set_value('effective_date', date('d F Y')) ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('price_type') == '' ?: 'has-error'; ?>">
                        <label for="price_type">Price Type</label>
                        <select class="form-control select2" name="price_type" id="price_type" data-placeholder="Select price type" style="width: 100%" required>
                            <option value=""></option>
                            <option value="STORAGE">STORAGE</option>
                            <option value="HANDLING">HANDLING</option>
                            <option value="COMPONENT">COMPONENT</option>
                            <option value="INVOICE">INVOICE</option>
                        </select>
                        <?= form_error('price_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('price_subtype') == '' ?: 'has-error'; ?>">
                        <label for="price_subtype">Price Subtype</label>
                        <select class="form-control select2" name="price_subtype" id="price_subtype"
                                data-placeholder="Select price subtype" style="width: 100%">
                            <option value=""></option>
                            <option value="ACTIVITY">ACTIVITY</option>
                            <option value="CONTAINER">CONTAINER</option>
                            <option value="GOODS">GOODS</option>
                        </select>
                        <?= form_error('price_subtype', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6" id="handling-type-wrapper" style="display: none">
                    <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2" name="handling_type" id="handling_type"
                                data-placeholder="Select handling type" style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>" <?= set_value('handling_type') == $handlingType['id'] ? 'selected' : '' ?>>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6" id="handling-component-wrapper" style="display: none">
                    <div class="form-group <?= form_error('component') == '' ?: 'has-error'; ?>">
                        <label for="component">Component</label>
                        <select class="form-control select2" name="component" id="component"
                                data-placeholder="Select component"
                                style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($components as $component): ?>
                                <option value="<?= $component['id'] ?>" <?= set_value('component') == $component['id'] ? 'selected' : '' ?>>
                                    <?= $component['handling_component'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('component', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>" style="display: none" id="description-wrapper">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Title description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="rules-field" class="form-group <?= form_error('rules') == '' ?: 'has-error'; ?>" style="display: none">
                <label id="rules-label">Rules</label>
                <div class="row">
                    <div class="col-sm-2 per-day-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_day_rule">
                                <input type="checkbox" name="rules[]" id="per_day_rule" value="PER_DAY"> PER DAY
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-size-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_size_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_size_rule" value="PER_SIZE"> PER SIZE
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-type-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_type_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_type_rule" value="PER_TYPE"> PER TYPE
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-empty-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_empty_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_empty_rule" value="PER_EMPTY"> PER EMPTY
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-danger-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_danger_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_danger_rule" value="PER_DANGER"> PER DANGER
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-condition-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_condition_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_condition_rule" value="PER_CONDITION"> PER CONDITION
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-volume-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_volume_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_volume_rule" value="PER_VOLUME"> PER VOLUME
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-tonnage-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_tonnage_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_tonnage_rule" value="PER_TONNAGE"> PER TONNAGE
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2 per-unit-wrapper">
                        <div class="checkbox icheck mt0">
                            <label for="per_unit_rule">
                                <input type="checkbox" class="rule-check" name="rules[]" id="per_unit_rule" value="PER_UNIT"> PER UNIT
                            </label>
                        </div>
                    </div>
                </div>
                <?= form_error('rules', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="alert alert-warning">
                Set price field blank if you DO NOT WANT to set the price (it may use last price)
            </div>

            <div id="price-field">

                <!-- generated table price should be lied here see: <code>component_price.js</code> -->

                <div id="activity-price" style="display: none">
                    <div class="form-group <?= form_error('price') == '' ?: 'has-error'; ?>">
                        <label for="price">Price</label>
                        <input type="text" class="form-control currency" id="price" name="prices[0][PRICE]" placeholder="Price value" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Component Price</button>
        </div>
    </form>
</div>

<script id="row-unit-template" type="text/x-custom-template">
    <select name="prices[][PER_UNIT]" class="form-control select2 goods_unit" data-placeholder="Select unit" style="width: 100%" title="Unit">
        <option value=""></option>
        <?php foreach ($units as $unit): ?>
            <option value="<?= $unit['id'] ?>">
                <?= $unit['unit'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</script>

<script src="<?= base_url('assets/app/js/component_price.js?v=1') ?>" defer></script>