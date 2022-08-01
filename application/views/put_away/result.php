<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Put Away Audit</h3>
    </div>
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <form class="form-horizontal form-view">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">Date :</label>
                            <div class="col-sm-9">
                                <p><strong><?= format_date($putAway['put_away_date'], 'd F Y') ?> </strong></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Description :</label>
                            <div class="col-sm-9">
                                <p><strong><?= if_empty($putAway['description'], ' No Description') ?></strong></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Validate Description :</label>
                            <div class="col-sm-9">
                                <p><strong><?= if_empty($putAway['validate_description'], ' No Description') ?></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3"> Status :</label>
                            <div class="col-sm-9">
                                 <p class="<?= $putAway['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><strong><?= $putAway['status'] ?></strong></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3"> Validated At :</label>
                            <div class="col-sm-9">
                                 <p><strong><?= format_date($putAway['validated_at'], 'd F Y H:i:s') ?></strong></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3"> Validated By :</label>
                            <div class="col-sm-9">
                                 <p><strong><?= if_empty($putAway['validate_name'], '-') ?></strong></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
        <div class="box box-primary">
            <div class="box-body" id="result-put-away">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 20px">No</th>
                                <th class="text-center">Owner Name</th>
                                <th class="text-center">No Booking</th>
                                <th class="text-center">No Goods</th>
                                <th class="text-center">Goods Name</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Pallet Marking Check</th>
                                <th class="text-center">Type Check</th>
                                <th class="text-center">Position Check</th>
                                <th class="text-center">Quantity Check</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Description Check</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($putAwayDetails as $stock): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td ><?= $stock['name'] ?></td>
                                    <td class="text-center">
                                        <?= $stock['no_booking'] ?>
                                        <small><?= "(".$stock['no_reference'].")" ?></small> 
                                    </td>
                                    <td class="text-center"><?= $stock['no_goods'] ?></td>
                                    <td class="text-center"><?= $stock['goods_name'] ?></td>
                                    <td class="text-center">
                                        <?= $stock['unit'] ?>
                                    </td>
                                    <td class="text-center" style="<?= $stock['pallet_marking_check'] ? 'background-color:#90EE90' : 'background-color:#F08080'?>">
                                        <?= if_empty($stock['no_pallet'], ' - ') ?></td>
                                    <td class="text-center" style="<?= $stock['type_goods_check'] ? 'background-color:#90EE90' : 'background-color:#F08080'?>">
                                        <?= if_empty($stock['type_goods'],'-') ?></td>
                                    <td class="text-center" style="<?= $stock['position_check'] ? 'background-color:#90EE90' : 'background-color:#F08080'?>">
                                        <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                                        <small class="text-muted" style="display: block">
                                            <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                                        </small></td>
                                    <td class="text-center" style="<?= $stock['quantity_check'] ? 'background-color:#90EE90' : 'background-color:#F08080'?>"><?= $stock['quantity'] ?></td>
                                    <td>
                                        <button class="btn btn-sm <?= ($stock['count_photo']>0) ? 'btn-info' : 'btn-warning' ?> btn-photo-goods" type="button">
                                            <i class="fa ion-image"></i>
                                        </button>
                                        <input type="hidden" name='photo[]' id="temp_photos" value="">
                                        <input type="hidden" name='photo_description[]' id="temp_photo_descriptions" value="">
                                        <input type="hidden" name='workOrderGoodsId[]' id="workOrderGoodsId" value="<?= $stock['id_work_order_goods'] ?>">
                                    </td>
                                    <td class="text-center"><?= if_empty($stock['description_check'],'-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($putAwayDetails)): ?>
                            <tr>
                                <td colspan="11" class="text-center"><?= "No goods" ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <p><i class="fa fa-info-circle mr10"></i> Red means false and green means true</p>
                </div>
                <div class="form-group">
                    <label for="Photo">Photo</label>
                    <p class="form-control-static">
                    <a href="<?= asset_url(urlencode($putAway['photo'])) ?>">
                            <?= $putAway['photo'] ?>
                        </a>
                    </p>
                </div>
                
            </div>
            <div class="box-footer">
                <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_VALIDATE) && !(($putAway['status'] == PutAwayModel::STATUS_NOT_VALIDATED) || ($putAway['status'] == PutAwayModel::STATUS_VALIDATED))): ?>
                    <div class="pull-right">
                    <a href="<?= site_url('put-away/validate/' . $putAway['id']) ?>"
                        class="btn btn-success" id="btn-validate-put-away"
                        data-id="<?= $putAway['id'] ?>"
                        data-label="<?= $putAway['no_put_away'] ?>">
                        Validate
                    </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PUT_AWAY_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-opname">
        <div class="modal-dialog" role="upload">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validating Opname</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate opname
                            <strong id="opname-title"></strong>?
                        </p>
                        <div class="form-group">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2" placeholder="Validation message"></textarea>
                            <span class="help-block">This message will be included in email to customer</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="NOT VALIDATED">Not Valid</button>
                        <button type="submit" class="btn btn-success" name="status" value="VALIDATED">Validate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $this->load->view('put_away/_modal_photo_editor') ?>
<?php $this->load->view('template/_modal_confirm'); ?>
<script src="<?= base_url('assets/app/js/put_away.js?v=1') ?>" defer></script>