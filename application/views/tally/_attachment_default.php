<script id="attachment-default-template" type="text/x-custom-template">
    <div class="row" style="margin-bottom: 2px">
        <div class="form-group">
            <div class="col-sm-3">
                    <label for="attachment_button_0">Attachment 1</label>
                    <input type="file" id="attachment_0" name="attachments_0" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                    <div class="input-group col-xs-12">
                        <input type="text" name="candidates[0][attachment]" id="attachment_info_0" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
    background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment" required>
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
                    <input type="text" name="photo_name[0]" id="photo_name_0" class="form-control" placeholder="Photo Name">
                </div>
            </div>
        </div>
    </div>
</script>