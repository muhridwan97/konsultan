<div class="modal fade" role="dialog" id="modal-goods-input-extended">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Input Goods</h4>
                </div>
                <div class="modal-body">
                    <div id="goods-create-wrapper">
                        <div class="form-group">
                            <label for="goods_name">Goods Name</label>
                            <input type="text" class="form-control" id="goods_name" name="goods_name"
                                   placeholder="No goods" maxlength="100" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="no_goods">No Goods</label>
                                <input type="text" class="form-control" id="no_goods" name="no_goods"
                                       placeholder="Goods number ID" maxlength="50" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="no_hs">No HS</label>
                                <input type="text" class="form-control" id="no_hs" name="no_hs"
                                       placeholder="HS number" maxlength="50">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_goods">Goods Type</label>
                                    <input type="text" class="form-control" id="type_goods" name="type_goods"
                                           placeholder="Goods type" maxlength="50">
                                </div>
                            </div>
                        </div>
                        <hr class="mt10">
                    </div>
                    <div class="form-group" id="goods-list-field">
                        <label for="goods">Goods</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                data-key-id="id" data-key-label="name" data-key-sublabel="no_goods"
                                name="goods" id="goods"
                                data-placeholder="Select goods" required disabled style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="text" class="form-control numeric" id="quantity" name="quantity" readonly
                                       placeholder="Quantity of item" required maxlength="50" data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <select class="form-control select2" name="unit" id="unit"
                                        data-placeholder="Select unit" required disabled style="width: 100%">
                                    <option value=""></option>
                                    <?php $units = isset($units) ? $units : [] ?>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>">
                                            <?= $unit['unit'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="position">Position</label>
                                <div class="input-group">
                                    <input type="hidden" name="position_blocks" id="position_blocks">
                                    <select class="form-control select2 select2-ajax multi-position"
                                            data-url="<?= site_url('position/ajax_get_all') ?>"
                                            data-key-id="id" data-key-label="position" data-add-empty-value="NO POSITION"
                                            name="position" id="position"
                                            data-placeholder="Location" style="width: 100%">
                                        <option value=""></option>
                                    </select>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-edit-block">
                                            <i class="ion-compose"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight">Unit Weight (Kg)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control numeric" id="weight" name="weight"
                                           placeholder="Weight of item" maxlength="50" data-default="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="gross_weight">Unit Gross Weight (Kg)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control numeric" id="gross_weight" name="gross_weight"
                                           placeholder="Gross weight of item" maxlength="50" data-default="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="volume">Unit Volume (M<sup>3</sup>)</label>
                                <input type="text" class="form-control numeric" id="volume" name="volume"
                                       placeholder="Volume of item" maxlength="50" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="length">Unit Length (M)</label>
                                <input type="text" class="form-control numeric" id="length" name="length"
                                       placeholder="Length of item" maxlength="50" data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="width">Unit Width (M)</label>
                                <input type="text" class="form-control numeric" id="width" name="width"
                                       placeholder="Width of item" maxlength="50" data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="height">Unit Height (M)</label>
                                <input type="text" class="form-control numeric" id="height" name="height"
                                       placeholder="Height of item" maxlength="50" data-default="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_danger">Danger</label>
                                <select class="form-control select2" name="status_danger" id="status_danger"
                                        data-placeholder="Danger status" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="NOT DANGER">NOT DANGER</option>
                                    <option value="DANGER TYPE 1">DANGER TYPE 1</option>
                                    <option value="DANGER TYPE 2">DANGER TYPE 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_hold">Is Hold</label>
                                <select class="form-control select2" name="is_hold" id="is_hold"
                                        data-placeholder="Hold the item" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status"
                                        data-placeholder="Item condition" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="GOOD">GOOD</option>
                                    <option value="DAMAGE">DAMAGE</option>
                                    <option value="USED">USED</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_pallet">Pallet Number</label>
                                <input type="text" class="form-control" id="no_pallet" name="no_pallet"
                                       placeholder="Pallet number" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ex_no_container">Ex Container</label>
                                <input type="text" class="form-control" id="ex_no_container" name="ex_no_container"
                                       placeholder="Ex container number" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Item description" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-remove-goods">Remove</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-item">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>