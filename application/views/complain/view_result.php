<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Result</h3>
    </div>
    <form action="#" class="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Complain Number</label>
                <input type="text" class="form-control" disabled value="<?= $complain['no_complain'] ?>">
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group">
                <label for="complain">Conclusion</label>
                 <textarea class="form-control" id="conclusion" disabled name="conclusion" placeholder="Enter Conclusion" maxlength="500"><?= set_value('conclusion', $complain['conclusion']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="complain">FTKP</label>
                 <textarea class="form-control" id="ftkp" disabled name="ftkp" placeholder="Enter FTKP" maxlength="500"><?= set_value('ftkp', $complain['ftkp']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="complain">Attachment</label>
                <p>
                    <a href="<?= asset_url(urlencode($complain['attachment'])) ?>" target="_blank">
                        <?php $fileName = explode('/',$complain['attachment']);
                              $fileName = end($fileName);
                         ?>
                         <?= $fileName ?>
                    </a>
                </p>
            </div>
        </div>

         <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>