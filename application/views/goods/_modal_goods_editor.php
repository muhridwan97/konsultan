<div class="modal fade" role="dialog" id="modal-goods-editor">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="form-goods">
                <input type="hidden" name="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create Goods</h4>
                </div>
                <div class="modal-body">
                    <div class="alert" role="alert" style="display: none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="messages"></div>
                    </div>

                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax" style="width: 100%" data-url="<?= site_url('people/ajax_get_people') ?>" data-key-id="id" data-key-label="name" data-params="type=CUSTOMER" name="customer" id="customer" data-placeholder="Select customer" required>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('no_hs') == '' ?: 'has-error'; ?>">
                                <label for="no_hs">HS Code</label>
                                <input type="text" class="form-control" id="no_hs" name="no_hs" placeholder="Enter HS Code" required maxlength="50" value="<?= set_value('no_hs') ?>">
                                <?= form_error('no_hs', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('no_goods') == '' ?: 'has-error'; ?>">
                                <label for="no_goods">Item Code</label>
                                <input type="text" class="form-control" id="no_goods" name="no_goods" placeholder="Enter Item Code" required maxlength="50" value="<?= set_value('no_goods') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whey_number') == '' ?: 'has-error'; ?>">
                                <label for="whey_number">Whey Number</label>
                                <input type="text" class="form-control" id="whey_number" name="whey_number" placeholder="Enter whey number" maxlength="50" value="<?= set_value('whey_number') ?>">
                                <?= form_error('whey_number', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('type_goods') == '' ?: 'has-error'; ?>">
                                <label for="type_goods">Goods Type</label>
                                <input type="text" class="form-control" id="type_goods" name="type_goods" placeholder="Enter type of goods" maxlength="50" value="<?= set_value('type_goods') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Item Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="shrink_tolerance">Shrink Tolerance</label>
                        <input type="number" min="0" step="1" class="form-control" id="shrink_tolerance" name="shrink_tolerance" placeholder="Enter Shrink Tolerance in percent" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_weight') == '' ?: 'has-error'; ?>">
                                <label for="unit_weight">Unit Weight (KG)</label>
                                <input type="text" class="form-control numeric" id="unit_weight" name="unit_weight" placeholder="Type of weight" maxlength="10" value="<?= set_value('unit_weight') ?>">
                                <?= form_error('unit_weight', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_gross_weight') == '' ?: 'has-error'; ?>">
                                <label for="unit_gross_weight">Unit Gross Weight (KG)</label>
                                <input type="text" class="form-control numeric" id="unit_gross_weight" name="unit_gross_weight" placeholder="Type of gross weight" maxlength="10" value="<?= set_value('unit_gross_weight') ?>">
                                <?= form_error('unit_gross_weight', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_volume') == '' ?: 'has-error'; ?>">
                                <label for="unit_volume">Unit Volume (M<sup>3</sup>)</label>
                                <input type="text" class="form-control numeric" id="unit_volume" name="unit_volume" value="<?= set_value('unit_volume') ?>" placeholder="Volume of item" maxlength="50" readonly>
                                <?= form_error('unit_volume', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_length">Unit Length (M)</label>
                                <input type="text" class="form-control numeric" id="unit_length" name="unit_length" placeholder="Length of item" value="<?= set_value('unit_length') ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_length', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_width">Unit Width (M)</label>
                                <input type="text" class="form-control numeric" id="unit_width" name="unit_width" placeholder="Width of item" value="<?= set_value('unit_width') ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_width', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_height">Unit Height (M)</label>
                                <input type="text" class="form-control numeric" id="unit_height" name="unit_height" placeholder="Height of item" value="<?= set_value('unit_height') ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_height', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Goods Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Item description" required maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Create New Goods</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/goods-editor.js?v=2') ?>" defer></script>
<script src="<?= base_url('assets/app/js/goods.js') ?>" defer></script>