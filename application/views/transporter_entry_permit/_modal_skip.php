<div class="modal fade" tabindex="-1" role="dialog" id="modal-skip">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Skip Request</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 2px">Are you sure want to skip this request ?</p>
                    <input type="hidden" name="id" id="id">
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-12">
                            <div class="form-group <?= form_error('skip_reason') == '' ?: 'has-error'; ?>">
                                <label for="skip_reason">Skip Reason</label>
                                <textarea class="form-control" id="skip_reason" name="skip_reason" placeholder="Skip Reason" required
                                        maxlength="500"><?= set_value('skip_reason') ?></textarea>
                                <?= form_error('skip_reason', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Skip Request</button>
                </div>
            </form>
        </div>
    </div>
</div>