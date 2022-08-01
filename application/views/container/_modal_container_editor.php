<div class="modal fade" role="dialog" id="modal-container-editor">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="form-container">
                <input type="hidden" name="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create Container</h4>
                </div>
                <div class="modal-body">
                    <div class="alert" role="alert" style="display: none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="messages"></div>
                    </div>

                    <div class="form-group">
                        <label for="shipping_line">Shipping Line</label>
                        <select class="form-control select2 select2-ajax" style="width: 100%"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name"
                                name="shipping_line" id="shipping_line" data-placeholder="Select shipping line" required>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="form-group <?= form_error('no_container') == '' ?: 'has-error'; ?>">
                        <label for="no_container">Container Number</label>
                        <input type="text" class="form-control" id="no_container" name="no_container"
                               placeholder="Enter Container Number" required
                               maxlength="15">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                                <label for="type">Container Type</label>
                                <select class="form-control select2" id="type" name="type" required="true" data-placeholder="Select Type" style="width: 100%">
                                    <option value=""></option>
                                    <option value="STD">STD</option>
                                    <option value="HC">HC</option>
                                    <option value="OT">OT</option>
                                    <option value="FR">Flat Rack</option>
                                    <option value="TANK">Tank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('size') == '' ?: 'has-error'; ?>">
                                <label for="size">Container Size</label>
                                <select class="form-control select2" id="size" name="size" data-placeholder="Select Size" required="true" style="width: 100%">
                                    <option value=""></option>
                                    <option value="20">20 Feet</option>
                                    <option value="40">40 Feet</option>
                                    <option value="45">45 Feet</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Container Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Container description"
                                  required maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Create New Container</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/container-editor.js?v=2') ?>" defer></script>