<div class="modal fade" tabindex="-1" role="dialog" id="modal-attachment">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" class="need-validation">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Handover Attachment</h4>
                </div>
                <div class="modal-body">
                    <p class="lead mb10">
                        Upload an attachment <strong id="handover-title"></strong> and proceed to next step?
                    </p>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" required class="file-upload-default upload-photo" >
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none; color:#AAA; background:#F5F5F5; webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment" required>
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">
                                    Select File
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group" id="field-uploaded-attachment" style="display: none">
                        <label for="attachment">Uploaded File</label>
                        <a href="#" target="_blank" id="uploaded-attachment-link"></a>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Upload description"
                                  maxlength="500"><?= set_value('description') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Upload Attachment</button>
                </div>
            </form>
        </div>
    </div>
</div>