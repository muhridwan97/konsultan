<div class="modal fade" tabindex="-1" role="dialog" id="modal-heep-check-out">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" id="form-heep-check">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Check Out : <strong>HEEP</strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check out
                        <strong id="check-in-title"></strong>?</p>
                    <p class="text-warning">
                        Security check out automatic set to present time when the form is submitted.
                    </p>
                    <div class="form-group">
                        <div>
                                <label for="attachment">Capture Photo</label>
                                <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                                <div class="input-group col-xs-12">
                                    <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Capture Photo" required>
                                    <span class="input-group-btn">
                                        <button class="file-upload-browse btn btn-default btn-photo-picker button-file" type="button">Capture</button>
                                    </span>
                                </div>
                            <div class="upload-input-wrapper"></div>
                        </div>
                        <div>
                            <div id="progress" class="progress progress-upload">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                            <div class="uploaded-file"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Check Out Description</label>
                        <textarea name="description" id="description" cols="30" rows="2" required
                                  class="form-control" placeholder="Check out remark and additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-success btn-check">Check Out</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->