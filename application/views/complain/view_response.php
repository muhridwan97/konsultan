<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Response</h3>
    </div>
    <form action="#" class="form" method="post" id="form-view-response">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Complain Number</label>
                <input type="text" class="form-control" disabled value="<?= $complain['no_complain'] ?>">
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            
            <div class="form-group <?= form_error('disprove') == '' ?: 'has-error'; ?>">
                <label for="disprove">Disprove</label>
                 <textarea class="form-control" id="disprove" disabled name="disprove" placeholder="Enter Disprove" maxlength="500"><?= set_value('disprove', if_empty($lastDisprove['description'], '-')) ?></textarea>
                <?= form_error('disprove', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <label>Disprove Attachment</label>
                <p class="form-control-static">
                <?php if(!empty($lastDisprove['attachment'])): ?>
                <a href="<?= asset_url(urlencode($lastDisprove['attachment'])) ?>" target="_blank">
                    <?php $fileName = explode('/',$lastDisprove['attachment']);
                              $fileName = end($fileName);
                         ?>
                         <?= $fileName ?>
                </a> 
                <?php else: ?> 
                <?= 'No Disprove Attachment' ?>
                 <?php endif; ?> 
                </p>
            </div>
            <div class="form-group <?= form_error('complain') == '' ?: 'has-error'; ?> complain-class">
                <label for="complain">Response</label>
                 <textarea class="form-control" id="response" disabled name="response" placeholder="Enter Response" maxlength="500"><?= set_value('response', $lastResponse['description']) ?></textarea>
                <?= form_error('response', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <label>Response Attachment</label>
                <p class="form-control-static">
                <?php if(!empty($lastResponse['attachment'])): ?>
                <a href="<?= asset_url(urlencode($lastResponse['attachment'])) ?>" target="_blank">
                    <?php $fileName = explode('/',$lastResponse['attachment']);
                              $fileName = end($fileName);
                         ?>
                         <?= $fileName ?>
                </a> 
                <?php else: ?> 
                <?= 'No Response Attachment' ?>
                 <?php endif; ?> 
                </p>
            </div>
        </div>

         <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <?php if($complain['status_investigation'] == ComplainModel::STATUS_PENDING && UserModel::authenticatedUserData('position_level') == 'MANAGER' && $complain['department'] == $profil['department'] && !empty($complain['conclusion'])): ?>
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