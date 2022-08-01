<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Investigation</h3>
    </div>
    <form action="#" class="form" method="post" id="form-view-investigation">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Complain Number</label>
                <input type="text" class="form-control" disabled value="<?= $complain['no_complain'] ?>">
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('complain') == '' ?: 'has-error'; ?> complain-class">
                <label for="complain">Investigation</label>
                 <textarea class="form-control" id="investigation" disabled name="investigation" placeholder="Enter Investigation" maxlength="500"><?= set_value('investigation', $complain['investigation_result']) ?></textarea>
                <?= form_error('investigation', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('corrective') == '' ?: 'has-error'; ?>">
                <label for="corrective">Corrective</label>
                 <textarea class="form-control" id="corrective" disabled name="corrective" placeholder="Enter Corrective" maxlength="500"><?= set_value('corrective', if_empty($complain['corrective'], '-')) ?></textarea>
                <?= form_error('corrective', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('prevention') == '' ?: 'has-error'; ?>">
                <label for="prevention">Prevention</label>
                 <textarea class="form-control" id="prevention" disabled name="prevention" placeholder="Enter Prevention" maxlength="500"><?= set_value('prevention', if_empty($complain['prevention'], '-')) ?></textarea>
                <?= form_error('prevention', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <label>Investigation Attachment</label>
                <p class="form-control-static">
                <?php if(!empty($complain['investigation_attachment'])): ?>
                <a href="<?= asset_url(urlencode($complain['investigation_attachment'])) ?>" target="_blank">
                <?php $fileName = explode('/',$complain['investigation_attachment']);
                              $fileName = end($fileName);
                         ?>
                         <?= $fileName ?>
                </a> 
                <?php else: ?> 
                <?= 'No Investigation Attachment' ?>
                 <?php endif; ?> 
                </p>
            </div>
        </div>

         <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <?php if($complain['status_investigation'] == ComplainModel::STATUS_PENDING && UserModel::authenticatedUserData('position_level') == 'MANAGER' && $complain['department'] == $profil['department'] && empty($complain['conclusion'])): ?>
            <div class="pull-right">
                <button class="btn btn-danger mr-1 btn-approval" 
                data-approval="REJECT" data-id="<?= $complain['id'] ?>" data-label="Reject">Reject</button>
                <button class="btn btn-success mr-1 btn-approval"
                data-approval="APPROVE" data-id="<?= $complain['id'] ?>" data-label="Approve">Approve</button>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php $this->load->view('complain/_modal_approval'); ?>

<script src="<?= base_url('assets/app/js/complain.js?v=1') ?>" defer></script>