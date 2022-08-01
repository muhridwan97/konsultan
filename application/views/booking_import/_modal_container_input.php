<div class="modal fade responsive" role="dialog" id="modal-container-input">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Input Container</h4>
                </div>
                <div class="modal-body">
                    <div id="container-create-wrapper">
                        <div class="form-group">
                            <label for="shipping_line">Shipping Line</label>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_SHIPPING_LINE ?>"
                                    name="shipping_line" id="shipping_line"
                                    data-placeholder="Select shipping line" style="width: 100%">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="no_container_label">New Container</label>
                                <input type="text" class="form-control" id="no_container_label" name="no_container_label"
                                       placeholder="No container" readonly>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Container Type</label>
                                    <select class="form-control select2" id="type" name="type" data-placeholder="Select Type" style="width: 100%">
                                        <option value=""></option>
                                        <option value="STD">STD</option>
                                        <option value="HC">HC</option>
                                        <option value="OT">OT</option>
                                        <option value="FR">FR</option>
                                        <option value="TANK">Tank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="size">Container Size</label>
                                    <select class="form-control select2" id="size" name="size" data-placeholder="Select Size" style="width: 100%">
                                        <option value=""></option>
                                        <option value="20">20 Feet</option>
                                        <option value="40">40 Feet</option>
                                        <option value="45">45 Feet</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr class="mt10">
                    </div>
                    <div class="form-group" id="container-list-field">
                        <label for="no_container">Container</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                data-key-id="id" data-key-label="no_container" data-key-sublabel="size"
                                name="no_container" id="no_container"
                                data-placeholder="Select container" required disabled style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="seal">Seal Number</label>
                                <input type="text" class="form-control" id="seal" name="seal"
                                       placeholder="Seal container"
                                       maxlength="50">
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
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_hold">Is Hold</label>
                                <select class="form-control select2" name="is_hold" id="is_hold"
                                        data-placeholder="Hold the cargo" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_empty">Is Empty</label>
                                <select class="form-control select2" name="is_empty" id="is_empty"
                                        data-placeholder="Cargo loading" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="0">FULL</option>
                                    <option value="1">EMPTY</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status" 
                                        data-placeholder="Cargo condition" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="GOOD">GOOD</option>
                                    <option value="DAMAGE">DAMAGE</option>
                                    <option value="USED">USED</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="length">Length (M)</label>
                                <input type="text" class="form-control numeric" id="length" name="length"
                                       placeholder="Length of payload" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="width">Width (M)</label>
                                <input type="text" class="form-control numeric" id="width" name="width"
                                       placeholder="Width of payload" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="height">Height (M)</label>
                                <input type="text" class="form-control numeric" id="height" name="height"
                                       placeholder="Height of payload" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="volume">Payload (M<sup>3</sup>)</label>
                        <input type="text" class="form-control numeric" id="volume" name="volume"
                               placeholder="Volume of Payload" maxlength="50" readonly>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Container description" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-container">Save Container</button>
                </div>
            </form>
        </div>
    </div>
</div>
