<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit component price</h3>
    </div>
    <form action="<?= site_url('component_price/update/' . $componentPrice['id']) ?>" class="form" method="post" id="form-component-price-edit">
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
                                    style="width: 100%" required disabled>
                                <option value=""></option>
                                <?php foreach ($branches as $key => $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $componentPrice['id_branch'] == $branch['id']) ?>>
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
                        <select class="form-control select2" name="customer" id="customer"
                                data-placeholder="Select customer" style="width: 100%" disabled>
                            <option value=""></option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>" <?= set_select('customer', $customer['id'], $componentPrice['id_customer'] == $customer['id']) ?>>
                                    <?= $customer['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('price_type') == '' ?: 'has-error'; ?>">
                        <label for="effective_date">Effective Date</label>
                        <input type="text" class="form-control datepicker" id="effective_date"
                               name="effective_date"
                               placeholder="Effective date" required disabled
                               value="<?= set_value('effective_date', (new DateTime($componentPrice['effective_date']))->format('d F Y')) ?>">
                        <?= form_error('effective_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('price_type') == '' ?: 'has-error'; ?>">
                        <label for="price_type">Price Type</label>
                        <select class="form-control select2" name="price_type" id="price_type"
                                data-placeholder="Select price type" style="width: 100%" required disabled>
                            <option value=""></option>
                            <option value="STORAGE" <?= set_select('price_type', 'STORAGE', $componentPrice['price_type'] == 'STORAGE') ?>>
                                STORAGE
                            </option>
                            <option value="HANDLING" <?= set_select('price_type', 'HANDLING', $componentPrice['price_type'] == 'HANDLING') ?>>
                                HANDLING
                            </option>
                            <option value="COMPONENT" <?= set_select('price_type', 'COMPONENT', $componentPrice['price_type'] == 'COMPONENT') ?>>
                                COMPONENT
                            </option>
                            <option value="CONTAINER" <?= set_select('price_type', 'CONTAINER', $componentPrice['price_type'] == 'CONTAINER') ?>>
                                INVOICE
                            </option>
                        </select>
                        <?= form_error('price_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('price_subtype') == '' ?: 'has-error'; ?>">
                        <label for="price_subtype">Price Subtype</label>
                        <select class="form-control select2" name="price_subtype" id="price_subtype"
                                data-placeholder="Select price subtype" style="width: 100%" disabled>
                            <option value=""></option>
                            <option value="ACTIVITY" <?= set_select('price_subtype', 'ACTIVITY', $componentPrice['price_subtype'] == 'ACTIVITY') ?>>
                                ACTIVITY
                            </option>
                            <option value="CONTAINER" <?= set_select('price_subtype', 'CONTAINER', $componentPrice['price_subtype'] == 'CONTAINER') ?>>
                                CONTAINER
                            </option>
                            <option value="GOODS" <?= set_select('price_subtype', 'GOODS', $componentPrice['price_subtype'] == 'GOODS') ?>>
                                GOODS
                            </option>
                        </select>
                        <?= form_error('price_subtype', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2" name="handling_type" id="handling_type"
                                data-placeholder="Select handling type" style="width: 100%" disabled>
                            <option value=""></option>
                            <?php foreach ($handlingTypes as $key => $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>"
                                    <?= set_select('handling_type', $handlingType['id'], $componentPrice['id_handling_type'] == $handlingType['id']) ?>>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('component') == '' ?: 'has-error'; ?>">
                        <label for="component">Component</label>
                        <select class="form-control select2" name="component" id="component"
                                data-placeholder="Select component"
                                style="width: 100%" disabled>
                            <option value=""></option>
                            <?php foreach ($components as $component): ?>
                                <option value="<?= $component['id'] ?>"
                                    <?= set_select('component', $component['id'], $componentPrice['id_component'] == $component['id']) ?>>
                                    <?= $component['handling_component'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('component', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Order description"
                          maxlength="500" disabled><?= set_value('description', $componentPrice['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div id="rules-field">
                <div class="form-group <?= form_error('rules') == '' ?: 'has-error'; ?>">
                    <label>Rules</label>
                    <div class="row" id="activity-rules">
                        <?php
                        $priceRules = explode(',', $componentPrice['rule']);
                        foreach ($priceRules as $priceRule) :
                            ?>
                            <div class="col-sm-3">
                                <div class="checkbox icheck" style="margin-top: 0">
                                    <label for="<?= $priceRule ?>">
                                        <input type="checkbox" name="rules[]" id="<?= $priceRule ?>"
                                               value="<?= $priceRule ?>" checked disabled><?= $priceRule ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?= form_error('rules', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div id="price-field">
                <div class="form-group <?= form_error('prices') == '' ?: 'has-error'; ?>">
                    <label for="prices">Price</label>
                    <input type="text" class="form-control currency" id="prices" name="prices"
                           value="<?= 'Rp. ' . numerical($componentPrice['price'], 3, true) ?>"
                           required>
                    <?= form_error('prices', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">Update component price</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/component_price.js?v=1') ?>" defer></script>