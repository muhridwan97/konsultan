<div style="display: none" id="type-custom">
    <div class="panel panel-primary">
        <div class="panel-heading">
            CUSTOM INVOICE ITEMS
        </div>
        <div class="panel-body">
            <div class="form-group <?= form_error('no_reference') == '' ?: 'has-error'; ?>">
                <label for="no_reference">No Reference</label>
                <input type="text" class="form-control" id="no_reference" name="no_reference"
                       placeholder="Put no reference handling, job or booking"
                       required maxlength="50" value="<?= set_value('no_reference') ?>">
                <span class="help-block">Eg. booking or handling number like HR/18/01/000001 or BI/18/03/000012</span>
                <?= form_error('no_reference', '<span class="help-block">', '</span>'); ?>
            </div>

            <table class="table table-striped no-datatable" id="table-invoice-item">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Multiplier</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="9" class="text-center">
                        Click <strong>Add New Invoice Item</strong> to insert new record
                    </td>
                </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-invoice-item">
                ADD NEW INVOICE ITEM
            </button>
        </div>
    </div>
</div>