<div class="modal fade" role="dialog" id="modal-goods-input">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" id="form-goods">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Input Goods</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-body" style="border: 1px solid #3c8dbc">
                            <div class="form-group">
                                <input type="hidden" id="no_booking" name="no_booking" value="<?= isset($workOrder)?(!empty($workOrder['no_booking_in'])?$workOrder['no_booking_in']:$workOrder['no_booking']):'' ?>" >
                                <input type="hidden" id="id_booking_reference" name="id_booking_reference" value="">
                                <label for="goods">Goods</label>
                                <div class="input-group col-md-12">
                                    <div id="goods-wrapper" style="display: none">
                                        <select class="form-control select2 select2-ajax"
                                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                                data-key-id="id" data-key-label="name" data-key-sublabel="no_goods"
                                                name="goods" id="goods"
                                                data-placeholder="Select goods" required style="width: 100%">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div id="goods-read-wrapper">
                                        <input type="text" class="form-control" id="good_read" placeholder="Scan pallet" readonly>
                                    </div>
                                    <span class="input-group-btn" style="<?= isset($inputSource) && $inputSource == 'STOCK' ? " display: none" : '' ?>">
                                        <button class="btn btn-primary" type="button" id="btn-scan-code">
                                            <span class="hidden-xs">SCAN </span><i class="fa fa-qrcode"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight">Unit Weight (Kg)</label>
                                        <input type="text" class="form-control numeric" id="weight" name="weight"
                                               placeholder="Weight of item" maxlength="50" required data-default="0">
                                        <small id="source-weight"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gross_weight">Unit Gross Weight (Kg)</label>
                                        <input type="text" class="form-control numeric" id="gross_weight" name="gross_weight"
                                               placeholder="Gross weight of item" maxlength="50" required data-default="0">
                                        <small id="source-gross-weight"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="volume">Unit Volume (M<sup>3</sup>)</label>
                                        <input type="text" class="form-control numeric" id="volume" name="volume"
                                               placeholder="Volume of item" maxlength="50" required readonly>
                                        <small id="source-volume"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="length">Unit Length (M)</label>
                                        <input type="text" class="form-control numeric" id="length" name="length"
                                               placeholder="Length of item" maxlength="50" data-default="0">
                                        <small id="source-length"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="width">Unit Width (M)</label>
                                        <input type="text" class="form-control numeric" id="width" name="width"
                                               placeholder="Width of item" maxlength="50" data-default="0">
                                        <small id="source-width"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="height">Unit Height (M)</label>
                                        <input type="text" class="form-control numeric" id="height" name="height"
                                               placeholder="Height of item" maxlength="50" data-default="0">
                                        <small id="source-height"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="text" class="form-control numeric" id="quantity" name="quantity"
                                       placeholder="Quantity of item" required maxlength="50" data-default="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <select class="form-control select2" name="unit" id="unit"
                                        data-placeholder="Select unit" required style="width: 100%">
                                    <option value=""></option>
                                    <?php $units = isset($units) ? $units : [] ?>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>">
                                            <?= $unit['unit'] ?>
                                        </option>
                                    <?php endforeach ?>
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
                                       placeholder="Pallet number" maxlength="50" readonly>
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
                    <?php if(isset($workOrderId)): ?>
                        <div class="form-group" style="display: none;">
                            <label for="status">Overtime Status</label>
                            <select class="form-control select2" name="overtime_status" id="overtime_status" data-placeholder="Overtime Status" required style="width: 100%">
                                    <option value="0" id="no_set">NOT SET</option>
                                    <option value="NORMAL" id="normal">NORMAL</option>
                                    <option value="OVERTIME 1" id="overtime1">OVERTIME 1</option>
                                    <option value="OVERTIME 2" id="overtime2">OVERTIME 2</option>
                            </select>
                        </div> 
                         <?php if(isset($JobPage) && ($JobPage == 'edit_job')  ): ?>
                        <div class="form-group responsive" id="overtimeDate">
                            <label for="overtime_date">Overtime Datetime</label>
                            <input type="text" class="form-control" id="overtime_date" name="overtime_date" value="<?= date('Y-m-d H:i:s') ?>">
                        </div>
                        <?php endif ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT)): ?>
                                <input type="hidden" id="permission" value="PERMISSION_WORKORDER_VALIDATED_EDIT">
                            <?php else: ?>  
                                <input type="hidden" id="permission" value="PERMISSION_WORKORDER_EDIT">
                            <?php endif ?>
                        <?php endif ?>
                    <?php endif ?>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Item description" maxlength="500"></textarea>
                    </div>
                    <input type="hidden" name="handling_type" id="handlingtype">
                    <input type="hidden" name="whey_number" id="whey_number">
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-success pull-left" id="btn-stock-goods" data-dismiss="modal">Stock</button> -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-remove-goods">Remove</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-item">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-scanner" id="modal-scanner" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="text-center pt10" id="camera-wrapper">
                    <div id="camera-message" style="border: 1px solid #ddd; margin: 20px 0; padding: 25px; border-radius: 5px">
                        <h1 class="fa ion-videocamera" style="font-size: 36px"></h1>
                        <p class="small">No cameras or insufficient permission.</p>
                        <button type="button" class="btn btn-sm" id="btn-try-again">Refresh the Page</button>
                    </div>
                    <canvas id="camera-preview" class="mt20" style="display:none; width: auto; height: auto; border-radius: 5px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('goods/_modal_goods_editor') ?>
<?php $this->load->view('tally/_modal_goods_stock'); ?>
<?php $this->load->view('tally/_modal_goods_take_stock'); ?>
<script src="<?= base_url('assets/plugins/jsQR/jsQR.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/pallet-scanner.js?v=9') ?>" defer></script>