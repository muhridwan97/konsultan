<div class="modal fade" role="dialog" id="modal-create-item">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="form-item">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create Item Compliance</h4>
                </div>
                <div class="modal-body">
                    <div class="alert" role="alert" style="display: none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="messages"></div>
                    </div>

                    <div class="form-group <?= form_error('item_name') == '' ?: 'has-error'; ?>">
                        <label for="item_name">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="item_name"
                            placeholder="Enter item name"
                            required value="<?= set_value('item_name') ?>">
                        <?= form_error('item_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <!-- <div class="form-group <?= form_error('no_hs') == '' ?: 'has-error'; ?>">
                        <label for="no_hs">No HS</label>
                        <input type="text" class="form-control" id="no_hs" name="no_hs"
                            placeholder="Enter HS number"
                            required value="<?= set_value('no_hs') ?>">
                        <?= form_error('no_hs', '<span class="help-block">', '</span>'); ?>
                    </div> -->
                    <div class="form-group <?= form_error('unit') == '' ?: 'has-error'; ?>">
                        <label for="unit">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit"
                            placeholder="Enter unit"
                            required value="<?= set_value('unit') ?>">
                        <?= form_error('unit', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <!-- <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                        <label for="customer">Customer</label> -->
                        <input type="hidden" name="customer" id="customer">
                        <!-- <select class="form-control select2 select2-ajax" style="width: 100%"
                                data-url="<?= site_url('people/ajax_get_people_all_branch') ?>"
                                data-key-id="id" data-key-label="name" data-key-sublabel="no_person" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer"
                                data-placeholder="Select customer" required>
                            <option value=""></option>
                        </select> -->
                        <!-- <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                    </div> -->
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Item Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Container description"
                                  required maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Create New Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/container-editor.js?v=2') ?>" defer></script>