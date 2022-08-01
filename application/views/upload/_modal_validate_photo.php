<div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-photo">
    <div class="modal-dialog modal-lg" role="photo">
        <div class="modal-content">
            <form action="#" method="post" id="form-validate-photo">
                <input type="hidden" name="id" id="id">
                <input type="hidden" id="id_upload" name="id_upload" value="">
                <input type="hidden" id="id_customer" name="id_customer" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Validating Photo</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Validate photo
                        <strong id="photo-title"></strong>?
                    </p>
                    <p class="text-danger">
                        Uploader will be notified about approving or rejecting photo.
                    </p>
                    <div id="photo-viewer" class="text-center">

                    </div>
                    <input type="hidden" id="id_upload" name="id_upload" value="">
                    <input type="hidden" id="id_customer" name="id_customer" value="">
                    <div class="form-group">
                        <label for="item_name">Item</label>
                        <div class="input-group">
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('item_compliance/ajax_get_item') ?>"
                                    data-key-id="id" data-key-label="item_name" data-key-sublabel="no_hs"
                                    name="item_name" id="item_name" data-params=""
                                    data-placeholder="Select Item" style="width: 100%">
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
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="3" placeholder="Validation message"></textarea>
                        <span class="help-block">This message will be included in email to the Uploader</span>
                        <input type="hidden" name="status" id="status" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger status-cek" value="-1">Reject</button>
                    <button type="submit" class="btn btn-success status-cek" value="1">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>