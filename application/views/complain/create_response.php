<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Response</h3>
    </div>
    <form action="<?= site_url('complain/save_response/' . $complain['id']) ?>" class="form" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <?php if(!empty($complain['note']) && $complain['status_investigation'] == ComplainModel::STATUS_REJECT):?>
            <div class="alert alert-warning mt10">
                <strong>Note:</strong></br>
                <?= $complain['note'] ?>
            </div>
            <?php endif; ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer"
                        data-placeholder="Select customer" disabled style="width: 100%">
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
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
            <div class="form-group <?= form_error('response') == '' ?: 'has-error'; ?> response-class">
                <label for="response">Response</label>
                 <textarea class="form-control" id="response" name="response" placeholder="Enter response" maxlength="500"><?= set_value('response') ?></textarea>
                <?= form_error('response', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Response Attachment</label>
                <input type="file" name="attachment" id="attachment"
                       accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                       placeholder="Select document">
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>