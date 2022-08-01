<script id="row-photo-template" type="text/x-custom-template">
    <tr class="row-photo">
        <td></td>
        <td>
            <select class="form-control select2" name="attachmentPhotos[]" id="detail_photo"
                    data-placeholder="Select attachment photo" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($attachmentPhotos as $attachmentPhoto): ?>
                    <option value="<?= $attachmentPhoto['id'] ?>">
                        <?= $attachmentPhoto['photo_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-control select2" name="conditions[]" id="condition"
                    data-placeholder="Select condition" style="width: 100%">
                <option value="TAKE">TAKE</option>
                <option value="COMPLETED">COMPLETED</option>
                <option value="CHECK">CHECK</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="descriptionPhotos[]" id="detail_description_photo"
                   placeholder="Description">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-photo">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>