<div class="modal fade" role="dialog" id="modal-goods-input">
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
                    <div class="form-group">
                        <label for="goods_name">Goods Name</label>
                        <input type="text" class="form-control" placeholder="Goods name" id="goods_name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="text" class="form-control numeric" id="quantity"
                               placeholder="Quantity of item" required maxlength="50" data-default="0">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight">Weight (Kg)</label>
                                <input type="text" class="form-control numeric" id="weight"
                                       placeholder="Weight of item" maxlength="50" required data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="gross_weight">Gross Weight (Kg)</label>
                                <input type="text" class="form-control numeric" id="gross_weight"
                                       placeholder="Gross weight of item" maxlength="50" required data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="volume">Volume (M<sup>3</sup>)</label>
                                <input type="text" class="form-control numeric" id="volume"
                                       placeholder="Volume of item" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price_type">Price Type</label>
                                <select name="price_type" id="price_type" class="form-control select2" data-placeholder="Select price type" style="width: 100%">
                                    <option value="TOTAL">TOTAL PRICE</option>
                                    <option value="UNIT">UNIT PRICE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" class="form-control numeric" id="price"
                                       placeholder="Price of item" required maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" placeholder="Additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-item">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>