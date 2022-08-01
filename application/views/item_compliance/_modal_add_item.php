<div class="modal fade responsive" role="dialog" id="modal-add-item">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Item</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_upload" name="id_upload" value="">
                    <input type="hidden" id="id_customer" name="id_customer" value="">
                    <div class="form-group">
                        <label for="item_name">Item</label>
                        <div class="input-group">
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('item_compliance/ajax_get_item') ?>"
                                    data-key-id="id" data-key-label="item_name" data-key-sublabel="no_hs"
                                    name="item_name" id="item_name" data-params=""
                                    data-placeholder="Select Item" required style="width: 100%">
                                <option value=""></option>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-success btn-create-item">
                                    <i class="ion-plus"></i> NEW
                                </button>
                            </span>
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
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('item_compliance/_modal_create_item') ?>
