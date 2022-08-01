<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Complain</h3>
    </div>
    <form role="form" class="form-horizontal form-view" id="form-view-complain">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Complain No</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $complain['no_complain'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $complain['customer_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $complain['category'] ?>(<?= $complain['value_type'] ?>)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Via</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $complain['via'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Department</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($complain['department'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Branch</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($complain['branch'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Complain</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(nl2br($complain['complain']), '-') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="col-sm-3">Attachment</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(!empty($complain['complaint_attachment'])): ?>
                                    <a href="<?= asset_url(urlencode($complain['complaint_attachment'])) ?>" target="_blank">
                                        <?php $fileName = explode('/',$complain['complaint_attachment']);
                                        $fileName = end($fileName);
                                        ?>
                                        <?= $fileName ?>
                                    </a>
                                <?php else: ?>
                                    <?= 'No Complaint Attachment' ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Setting PIC Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($complain['pic_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Close Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($complain['close_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($complain['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($complain['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="rating">Rating</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="rating">
                                <?php $totalRating = round(if_empty($complain['rating'], 0)) ?>
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <?php if ($i < $totalRating): ?>
                                        <i class="fa fa-star"></i>
                                    <?php else: ?>
                                        <i class="fa fa-star-o"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                (<?= $complain['rating'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Rating reason</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($complain['rating_reason'], '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('complain/_complain_history'); ?>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <div class="pull-right">
                <?php if($complain['status'] == ComplainModel::STATUS_PROCESSED && $complain['status_investigation'] == ComplainModel::STATUS_APPROVE && AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                    <button class="btn btn-success mr-1 btn-conclusion"
                     data-id="<?= $complain['id'] ?>">Conclusion</button>
                <?php endif; ?>
                <?php if(UserModel::authenticatedUserData('id_person') == $complain['id_customer']): ?>
                    <?php if($complain['status'] == ComplainModel::STATUS_CONCLUSION): ?>
                        <button class="btn btn-warning mr-1 btn-disprove"
                         data-id="<?= $complain['id'] ?>" data-disprove="DISPROVE" data-label="Disprove">Disprove</button>
                        <button class="btn btn-success mr-1 btn-disprove"
                         data-id="<?= $complain['id'] ?>" data-disprove="ACCEPTED" data-label="Accept">Accept</button>
                    <?php endif; ?>
                    <?php if($complain['status'] == ComplainModel::STATUS_FINAL): ?>
                        <button class="btn btn-success mr-1 btn-final-response"
                                data-url="<?= site_url('complain/final-response/' . $complain['id']) ?>">Add Response</button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                    <?php if ($allowSetFinal): ?>
                        <button class="btn btn-danger btn-final" data-url="<?= site_url('complain/set-final/' . $complain['id']) ?>">Set Final</button>
                    <?php endif; ?>
                    <?php if ($complain['status'] == ComplainModel::STATUS_FINAL): ?>
                        <?php if ($allowSetFinalConclusion): ?>
                            <a href="<?= site_url('complain/edit-conclusion-category/' . $complain['id']) ?>" class="btn btn-warning">
                                Edit Conclusion Category
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-warning disabled" data-toggle="tooltip" data-title="Waiting response until <?= $maxDateWaiting ?? 'indefinitely' ?>">
                                Edit Conclusion Category
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<?php $this->load->view('complain/_modal_conclusion'); ?>
<?php $this->load->view('complain/_modal_disprove'); ?>
<?php $this->load->view('complain/_modal_final'); ?>
<?php $this->load->view('complain/_modal_final_response'); ?>
<script src="<?= base_url('assets/app/js/complain.js?v=3') ?>" defer></script>