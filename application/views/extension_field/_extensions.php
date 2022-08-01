<div class="row">
    <?php foreach ($extensionFields as $field): ?>
        <?php
        $value = '';
        if (isset($bookingExtensions)) {
            foreach ($bookingExtensions as $bookingExtension) {
                if ($field['id'] == $bookingExtension['id_extension_field']) {
                    if (in_array($field['type'], ['CHECKBOX', '...', '...'])) {
                        $value = json_decode($bookingExtension['value']);
                    } else {
                        $value = $bookingExtension['value'];
                    }
                    break;
                }
            }
        }
        ?>
        <div class="col-md-4">
            <div class="form-group">
                <?php $this->load->view('extension_field/_field', [
                    'extensionField' => $field,
                    'value' => $value
                ]) ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($extensionFields)): ?>
    <div class="col-md-12">
        <p>No extension field available.</p>
    </div>
    <?php endif; ?>
</div>