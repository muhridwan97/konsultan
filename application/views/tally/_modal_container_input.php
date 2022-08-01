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
                    <input type="hidden" id="id_booking_reference" name="id_booking_reference" value="">
                    <div class="form-group">
                        <label for="no_container">Container</label>

                            <?php
                            $allowCreate = AuthorizationModel::isAuthorized(PERMISSION_CONTAINER_CREATE);
                            $allowEdit = AuthorizationModel::isAuthorized(PERMISSION_CONTAINER_EDIT);
                            ?>
                            <?php if ($allowCreate || $allowEdit): ?>
                                <?php if(isset($workOrder['multiplier_container']) && $workOrder['multiplier_container'] < 0): ?>
                                    <select class="form-control select2 select2-ajax" disabled
                                            data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                            data-key-id="id" data-key-label="no_container" data-key-sublabel="size"
                                            name="no_container" id="no_container"
                                            data-placeholder="Select container" required style="width: 100%">
                                        <option value=""></option>
                                    </select>
                                <?php elseif(isset($inputSource) && $inputSource == 'STOCK'): ?>
                                    <select class="form-control select2 select2-ajax"
                                            data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                            data-key-id="id" data-key-label="no_container" data-key-sublabel="size"
                                            name="no_container" id="no_container"
                                            data-placeholder="Select container" required style="width: 100%">
                                        <option value=""></option>
                                    </select>
                                <?php else: ?>
                                    <div class="input-group">
                                        <select class="form-control select2 select2-ajax"
                                                data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                                data-key-id="id" data-key-label="no_container" data-key-sublabel="size"
                                                name="no_container" id="no_container"
                                                data-placeholder="Select container" required style="width: 100%">
                                            <option value=""></option>
                                        </select>
                                        <span class="input-group-btn">
                                            <?php if ($allowEdit): ?>
                                                <button type="button" class="btn btn-default btn-edit-container">
                                                    <i class="ion-compose"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($allowEdit): ?>
                                                <button type="button" class="btn btn-success btn-create-container">
                                                    <i class="ion-plus"></i> NEW
                                                </button>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <select class="form-control select2 select2-ajax" <?= (isset($workOrder['multiplier_container']) && $workOrder['multiplier_container'] < 0)?'disabled' : '' ?>
                                        data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                        data-key-id="id" data-key-label="no_container" data-key-sublabel="size"
                                        name="no_container" id="no_container"
                                        data-placeholder="Select container" required style="width: 100%">
                                    <option value=""></option>
                                </select>
                            <?php endif; ?>
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
                        <div class="col-xs-6 col-md-4">
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
                        <div class="col-xs-6 col-md-4">
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
                    <?php if(isset($workOrderId)): ?>
                     <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="volume">Payload (M<sup>3</sup>)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control numeric" id="volume_payload" name="volume_payload"
                                           placeholder="Volume of Payload" maxlength="50" readonly>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-calculate-volume-payload btn-edit-volume"
                                                data-placement="right"
                                                data-toggle="tooltip">
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
                                <label for="length">Length (M)</label>
                                <input type="text" class="form-control numeric" id="length_payload" name="length_payload"
                                       placeholder="Length of payload" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="width">Width (M)</label>
                                <input type="text" class="form-control numeric" id="width_payload" name="width_payload"
                                       placeholder="Width of payload" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="height">Height (M)</label>
                                <input type="text" class="form-control numeric" id="height_payload" name="height_payload"
                                       placeholder="Height of payload" required maxlength="50">
                            </div>
                        </div>
                    </div>
                    
                    <?php endif; ?>
                    <?php if(isset($workOrderId)): ?>
                        <?php if(isset($JobPage) && ($JobPage != 'edit_job')): ?>
                            <div class="form-group" style="display: none;">
                                <label for="overtime_status">Overtime Status</label>
                                <select class="form-control select2" name="overtime_status" id="overtime_status" data-placeholder="Overtime Status" required style="width: 100%">
                                        <option value="0" id="no_set">NOT SET</option>
                                        <option value="NORMAL" id="normal">NORMAL</option>
                                        <option value="OVERTIME 1" id="overtime1">OVERTIME 1</option>
                                        <option value="OVERTIME 2" id="overtime2">OVERTIME 2</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <?php if(isset($JobPage) && ($JobPage == 'edit_job')): ?>
                            <div class="form-group responsive" id="overtimeDate">
                                <label for="overtime_date">Overtime Datetime</label>
                                <input type="text" class="form-control" id="overtime_date" name="overtime_date" value="<?= date('Y-m-d H:i:s') ?>">
                            </div>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT)): ?>
                                <input type="hidden" id="permission" value="PERMISSION_WORKORDER_VALIDATED_EDIT">
                            <?php else: ?>  
                                <input type="hidden" id="permission" value="PERMISSION_WORKORDER_EDIT">
                            <?php endif; ?>
                        <?php endif; ?>  
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Container description" maxlength="500"></textarea>
                    </div>
                    <input type="hidden" name="handling_type" id="handlingtype">
                    <input type="hidden" name="multiplier_goods" id="multiplierGoods">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success pull-left" id="btn-stock-container" data-dismiss="modal"><?= isset($stockLabel) ? $stockLabel : 'Stock' ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-remove-container">Remove</button>
                    <?php if(isset($workOrder['multiplier_container']) && $workOrder['multiplier_container'] < 0): ?>
                        <button type="submit" class="btn btn-primary" id="btn-save-container" disabled>Save Container</button>
                    <?php else: ?>
                    <button type="submit" class="btn btn-primary" id="btn-save-container">Save Container</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('container/_modal_container_editor') ?>
<?php $this->load->view('tally/_modal_container_stock'); ?>
