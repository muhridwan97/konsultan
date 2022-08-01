<script id="row-component-template" type="text/x-custom-template">
    <tr class="row-component">
        <td></td>
        <td>
            <select class="form-control select2" name="components[]" id="detail_component"
                    data-placeholder="Select component" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($components as $component): ?>
                    <option value="<?= $component['id'] ?>">
                        <?= $component['handling_component'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" min="0" step="any" class="form-control" name="default_values[]" id="default_value"
                   placeholder="Default value">
        </td>
        <td>
            <input type="text" class="form-control" name="descriptions[]" id="detail_description"
                   placeholder="Description">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-component">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>