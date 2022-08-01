<div class="modal fade" tabindex="-1" role="dialog" id="modal-checked">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Checked</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="no" id="no">
                    <p class="lead mb10">
                        Are you sure want to <strong>Checked</strong> job <strong class="checked-label"></strong>?
                    </p>
                    <div class="row" style="margin-bottom: 2px">
                    <div class="col-sm-3" id="photo-add">
                    <button class="btn btn-primary btn-sm" type="button" id="btn-add-photo">Add Photo</button>
                    </div>
                    </div>
                    <div id="photo-wrapper">
                        <div class="row" style="margin-bottom: 2px">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label for="attachment_button_0">Attachment 1</label>
                                    <input type="file" id="attachment_0" name="attachments_0" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera">
                                    <div class="input-group col-xs-12">
                                        <input type="text" name="candidates[0][attachment]" id="attachment_info_0" class="form-control file-upload-info" placeholder="Upload attachment" style="pointer-events: none; color:#AAA;
    background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false">
                                        <span class="input-group-btn">
                                            <button class="file-upload-browse btn btn-default btn-photo-picker button-file" id="attachment_button_0" type="button">Upload</button>
                                        </span>
                                    </div>
                                    <div class="upload-input-wrapper"></div>
                                </div>
                                <div class="col-sm-3">
                                    <div id="progress" class="progress progress-upload">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>
                                    <div class="uploaded-file"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="photo_name_0">Photo Name</label>
                                    <div class="input-group col-xs-12">
                                        <input type="text" name="photo_name[]" id="photo_name_0" class="form-control" placeholder="Photo Name" required >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="2" placeholder="Checked message"></textarea>
                        <span class="help-block">This message may be included to histories.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-submit-checked" data-toggle="one-touch">Checked</button>
                </div>
            </form>
        </div>
    </div>
</div>