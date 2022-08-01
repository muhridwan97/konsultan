<script id="row-whatsapp-template" type="text/x-custom-template">
    <tr class="row-whatsapp">
        <td></td>
        <td>
            <select class="form-control select2" name="groups[]" id="detail_group"
                    data-placeholder="Select group" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['department'] ?>">
                        <?= $department['department'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-control select2" name="branches[]" id="detail_branch"
                    data-placeholder="Select group" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>">
                        <?= $branch['branch'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-control select2" name="whatsapp_groups[]" id="detail_whatsapp_group"
                    data-placeholder="Select group" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($departmentContacts as $departmentContact): ?>
                    <option value="<?= $departmentContact['id'] ?>">
                        <?= $departmentContact['department'] ?> - <?= $departmentContact['group_name'] ?>  - <?= $departmentContact['contact_group'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-whatsapp">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>