<div style="display:none;" id="type-handling">
    <div class="form-group <?= form_error('handling') == '' ?: 'has-error'; ?>">
        <label for="handling">Handling</label>
        <select class="form-control select2" id="handling" name="handling"
                style="width: 100%" data-placeholder="Select related handling">
            <option value=""></option>
            <?php if(isset($handlings)): ?>
                <?php foreach ($handlings as $handling): ?>
                    <option value="<?= $handling['id'] ?>" <?= set_select('handling', $handling['id']) ?>>
                        <?= $handling['no_handling'] ?>
                    </option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
        <?= form_error('handling', '<span class="help-block">', '</span>'); ?>
    </div>
</div>