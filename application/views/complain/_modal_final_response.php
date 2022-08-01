<div class="modal fade" tabindex="-1" role="dialog" id="modal-final-response">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?=base_url('complain/save-final-response/' . $complain['id'] ?? '')?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Send Final Response</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="note-wrapper">
                        <label for="response" id="label-note">Response</label>
                        <textarea class="form-control" id="response" name="response" placeholder="Add final response"
                                  maxlength="500" required><?= set_value('response') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000">
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                    <?php if(!AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT)): ?>
                        <hr>
                        <?php $this->load->view('complain/_rating_fields') ?>
                        <p><i class="fa fa-info-circle mr5"></i>You can edit rating value later after final conclusion</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn btn-success">Add Final Response</button>
                </div>
            </form>
        </div>
    </div>
</div>