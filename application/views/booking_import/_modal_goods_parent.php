<div class="modal fade" role="dialog" id="modal-goods-parent">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Select Parent Goods</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="goods_parent">Goods Parent</label>
                        <select class="form-control select2" style="width: 100%" id="goods_parent">
                            <option value="0">-- SET NO PARENT --</option>
                            <?php foreach ($goods as $item): ?>
                                <option value="<?= $item['no_goods'] ?>" data-goods-name="<?= $item['goods_name'] ?>">
                                    <?= $item['goods_name'] ?> - <?= $item['no_goods'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block">Select parent goods of the item</span>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="select_quantity">Select Quantity</label>
                                <select class="form-control select2" style="width: 100%" id="select_quantity">
                                    <option value="1 ITEM">1 TO 1</option>
                                    <option value="TOTAL ITEM">ALL ITEM</option>
                                </select>
                                <span class="help-block">Select source quantity</span>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="quantity_child_goods">Quantity Goods</label>
                                <input type="text" class="form-control numeric" id="quantity_child_goods" placeholder="Quantity item">
                                <span class="help-block">Select quantity of child item</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save-parent">Set Parent</button>
                </div>
            </form>
        </div>
    </div>
</div>