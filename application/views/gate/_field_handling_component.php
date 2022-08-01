<?php if(isset($components) && !empty($components)): ?>
    <?php
    $allHidden = 'hidden';
    foreach ($components as $component){
        if($component['default_value'] <= 0) {
            $allHidden = '';
            break;
        }
    }
    foreach ($components as $component){
        if ($component['component_category']!="VALUE ADDITIONAL SERVICES" && $component['handling_component']!="Forklift" && $component['handling_component']!="Labours" && $component['handling_component']!="Pallet") {
            $allHidden = '';
            break;
        }
        $allHidden = 'hidden';
    } ?>

    <div class="panel panel-default <?= $allHidden ?>">
        <div class="panel-heading">
            Handling Components
        </div>
        <div class="panel-body">
            <?php foreach ($components as $component):?>
                <?php if($component['component_category']!="VALUE ADDITIONAL SERVICES" && $component['handling_component']!="Forklift" && $component['handling_component']!="Labours" && $component['handling_component']!="Pallet") { ?>
                <div class="form-group <?= $component['default_value'] > 0 ? 'hidden' : '' ?>">
                    <label for="quantity_<?= $component['id'] ?>" class="control-label">
                        <?= $component['handling_component'] ?>
                    </label>
                    <div class="row mb10">
                        <div class="col-md-12 mb10">
                            <select class="form-control select2" data-placeholder="Select source lease transaction" style="width: 100%"
                                    name="components[<?= $component['id'] ?>][transaction]" id="transaction_<?= $component['id'] ?>">
                                <option value=""></option>
                                <?php if(key_exists($component['id'], $componentTransactions)): ?>
                                    <?php foreach ($componentTransactions[$component['id']] as $transaction): ?>
                                        <?php
                                        $selected = '';
                                        if(key_exists('id_component_order', $component)) {
                                            if($component['id_component_order'] == $transaction['id']) {
                                                //$selected = 'selected';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $transaction['id'] ?>" <?= $selected ?>>
                                            <?= $transaction['no_transaction'] ?> (<?= numerical($transaction['quantity'], 3, true) ?> - Rp. <?= numerical($transaction['amount'], 0, true) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="components[<?= $component['id'] ?>][quantity]" id="quantity_<?= $component['id'] ?>" required step="any"
                                   class="form-control" value="<?= $component['default_value'] ?>" placeholder="Quantity">
                        </div>
                        <div class="col-md-6">
                            <select class="form-control select2" data-placeholder="Select unit" style="width: 100%"
                                    name="components[<?= $component['id'] ?>][unit]" id="unit_<?= $component['id'] ?>">
                                <option value=""></option>
                                <?php foreach ($units as $unit): ?>
                                    <?php
                                    $unitId = key_exists('handling_component_unit', $component) ? $component['handling_component_unit'] : ''
                                    ?>
                                    <option value="<?= $unit['id'] ?>" <?= set_select("components[{$component['id']}][unit]", $unit['id']/*, $unitId == $unit['id']*/) ?>>
                                        <?= $unit['unit'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <textarea name="components[<?= $component['id'] ?>][description]" id="description_<?= $component['id'] ?>" rows="1"
                              class="form-control" placeholder="Description"><?= key_exists('handling_component_desc', $component) ? ''/*$component['handling_component_desc']*/ : '' ?></textarea>
                    <p class="help-block"><?= if_empty($component['description'] == '-' ? '' : $component['description'], 'Component field of handling') ?></p>
                </div>
                <?php } ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>