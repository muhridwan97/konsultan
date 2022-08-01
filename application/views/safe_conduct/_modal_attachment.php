<div class="modal fade" tabindex="-1" role="dialog" id="modal-attachment">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Safe Conduct Attachment</h4>
                </div>
                <div class="modal-body">
                    <p class="lead mb10">
                        Upload an attachment <strong id="safe-conduct-title"></strong>?
                    </p>
                    <div class="form-group">
                        <div class="mb10">
                            <label for="attachment">Attachment</label>
                            <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" >
                            <div class="input-group col-xs-12">
                                <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment" required>
                                <span class="input-group-btn">
                                    <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">
                                        Select File
                                    </button>
                                </span>
                            </div>
                            <div class="upload-input-wrapper"></div>
                        </div>
                        <div>
                            <div id="progress" class="progress progress-upload mb10">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                            <label>Uploaded File</label>
                            <div class="uploaded-file mt0">
                                <p class="text-muted placeholder">Click upload button above</p>
                            </div>
                        </div>
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