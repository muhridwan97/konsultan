<div class="modal fade" role="dialog" id="modal-take-stock-goods">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Take Goods Stock</h4>
            </div>
            <div class="modal-body">

                <div class="form-horizontal form-view mb0">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Goods</label>
                                <div class="col-sm-9">
                                    <p id="label-goods" class="mb0">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Unit</label>
                                <div class="col-sm-9">
                                    <p id="label-unit" class="mb0">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="text" class="form-control numeric" id="quantity" name="quantity"
                           placeholder="Quantity of item" required maxlength="50" data-default="0">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="weight">Unit Weight (Kg)</label>
                            <input type="text" class="form-control numeric" id="weight" name="weight"
                                   placeholder="Weight of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gross_weight">Unit Gross Weight (Kg)</label>
                            <input type="text" class="form-control numeric" id="gross_weight" name="gross_weight"
                                   placeholder="Gross weight of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="volume">Unit Volume (M<sup>3</sup>)</label>
                            <input type="text" class="form-control numeric" id="volume" name="volume"
                                   placeholder="Volume of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="length">Unit Length (M)</label>
                            <input type="text" class="form-control numeric" id="length" name="length"
                                   placeholder="Length of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="width">Unit Width (M)</label>
                            <input type="text" class="form-control numeric" id="width" name="width"
                                   placeholder="Width of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="height">Unit Height (M)</label>
                            <input type="text" class="form-control numeric" id="height" name="height"
                                   placeholder="Height of item" maxlength="50" data-default="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn-take-goods-stock">Take</button>
            </div>
        </div>
    </div>
</div>