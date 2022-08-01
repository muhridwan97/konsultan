<script id="row-target-branch-template" type="text/x-custom-template">
    <tr class="row-target-branch">
        <td></td>
        <td>
            <select class="form-control select2" name="branches[]" id="branch" data-placeholder="Select branch" required>
                <option value=""></option>
                <?php foreach ($branchVmses as $branch): ?>
                    <option value="<?= $branch['id'] ?>">
                        <?= $branch['branch'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" min="0" step="any" class="form-control" name="target_branches[]" id="target_branch"
                    placeholder="Target Branch">
        </td>
        <td>
            <input type="text" class="form-control" name="descriptions[]" id="detail_description"
                    placeholder="Description">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-target-branch">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>