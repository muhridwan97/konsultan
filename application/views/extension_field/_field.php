<?php
$fieldValue = isset($value) ? $value : '';

$typeDate = ['DATE', 'DATE TIME'];
$typeText = ['SHORT TEXT', 'EMAIL', 'NUMBER'];
$typeTextInput = ['SHORT TEXT' => 'text', 'EMAIL' => 'email', 'NUMBER' => 'number'];
$options = json_decode($extensionField['option'], true);
$optionText = '';
if(!empty($options)) {
    foreach ($options as $attribute => $value) {
        if ($attribute != 'pattern' || ($attribute == 'pattern' && !empty($value))) {
            $optionText .= $attribute . '="' . $value . '" ';
        }
    }
}
?>
    <label class="control-label" for="<?= $extensionField['field_name'] ?>">
        <?= $extensionField['field_title'] ?>
    </label>
<?php if (in_array($extensionField['type'], $typeText)): ?>
    <input type="<?= $typeTextInput[$extensionField['type']] ?>" class="form-control"
           name="extensions[<?= $extensionField['field_name'] ?>]"
           id="<?= $extensionField['field_name'] ?>"
           placeholder="<?= if_empty($extensionField['description'], $extensionField['field_title']) ?>"
           value="<?= $fieldValue ?>"
        <?= $optionText ?>>
<?php endif; ?>

<?php if (in_array($extensionField['type'], $typeDate)): ?>
    <input type="text" class="form-control <?= ($extensionField['type'] == 'DATE') ? 'datepicker' : 'daterangepicker2' ?>"
           name="extensions[<?= $extensionField['field_name'] ?>]"
           id="<?= $extensionField['field_name'] ?>" <?= $extensionField['field_name'] == 'ETA' ? 'required' : '' ?>
           placeholder="<?= if_empty($extensionField['description'], $extensionField['field_title']) ?>"
           value="<?= readable_date($fieldValue, $extensionField['type'] == 'DATE TIME', '') ?>"
        <?= $optionText ?>>
<?php endif; ?>

<?php if ($extensionField['type'] == 'LONG TEXT'): ?>
    <textarea class="form-control" name="extensions[<?= $extensionField['field_name'] ?>]" <?= $optionText ?>
              placeholder="<?= if_empty($extensionField['description'], $extensionField['field_title']) ?>"><?= $fieldValue ?></textarea>
<?php endif; ?>

<?php if ($extensionField['type'] == 'CHECKBOX'): ?>
    <div>
        <?php for ($i = 0; $i < count($options['value']); $i++): ?>
            <?php
            $isChecked = '';
            if (is_array($fieldValue)) {
                foreach ($fieldValue as $val) {
                    if ($options['value'][$i] == $val) {
                        $isChecked = 'checked';
                        break;
                    }
                }
            }
            ?>
            <div class="checkbox-inline">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" name="extensions[<?= $extensionField['field_name'] ?>][]"
                               value="<?= $options['value'][$i] ?>" <?= $isChecked ?>>
                        <?= $options['label'][$i] ?>
                    </label>
                </div>
            </div>
        <?php endfor; ?>
    </div>
    <span class="help-block"><?= $extensionField['description'] ?></span>
<?php endif; ?>

<?php if ($extensionField['type'] == 'RADIO'): ?>
    <div>
        <?php for ($i = 0; $i < count($options['value']); $i++): ?>
            <label class="radio-inline">
                <input type="radio" name="extensions[<?= $extensionField['field_name'] ?>]"
                       value="<?= $options['value'][$i] ?>" <?= $fieldValue == $options['value'][$i] ? 'checked' : '' ?>>
                <?= $options['label'][$i] ?>
            </label>
        <?php endfor; ?>
    </div>
    <span class="help-block"><?= $extensionField['description'] ?></span>
<?php endif; ?>

<?php if ($extensionField['type'] == 'SELECT'): ?>
    <select class="form-control select2"
            data-placeholder="<?= if_empty($extensionField['description'], $extensionField['field_title']) ?>" <?= $options['required'] == 'true' ? 'required' : '' ?>
            name="extensions[<?= $extensionField['field_name'] ?>]" id="<?= $extensionField['field_name'] ?>">
        <option value=""></option>
        <?php for ($i = 0; $i < count($options['value']); $i++): ?>
            <option value="<?= $options['value'][$i] ?>" <?= $fieldValue == $options['value'][$i] ? 'selected' : '' ?>>
                <?= $options['label'][$i] ?>
            </option>
        <?php endfor; ?>
    </select>
<?php endif; ?>