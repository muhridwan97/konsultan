<div class="modal fade" role="dialog" id="modal-add-goods">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Goods</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_safe_conduct_goods" id="id_safe_conduct_goods">
                    <div class="form-group">
                        <label for="no_safe_conduct">No Safe Conduct</label>
                        <input type="text" class="form-control" readonly name="no_safe_conduct" id="no_safe_conduct" placeholder="No safe conduct">
                    </div>
                    <div class="form-group">
                        <label for="goods">Goods</label>
                        <input type="text" class="form-control" readonly name="goods" id="goods" placeholder="Goods name">
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="text" class="form-control numeric" id="quantity" name="quantity"
                               placeholder="Quantity of item" required maxlength="50" data-default="0">
                    </div>

                    <div class="form-group">
                        <label for="description">Tracking Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="State description" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-add-item">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>