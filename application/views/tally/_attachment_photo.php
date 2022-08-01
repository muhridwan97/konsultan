<script id="attachment-photo-template" type="text/x-custom-template">
    <div class="row card-photo" style="margin-bottom: 2px">
        <div class="form-group">
            <div class="col-sm-3">
                <label for="attachment_button_{{index}}">{{photo_name}}</label>
                <input type="file" id="attachment_{{index}}" name="attachments_{{index}}" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera"  >
                <div class="input-group col-xs-12">
                <input type="text" name="candidates[{{index}}][attachment]" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment" required>
                    <span class="input-group-btn">
                        <button class="file-upload-browse btn btn-default btn-photo-picker button-file" id="attachment_button_{{index}}" type="button">Upload</button>
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
            <div class="col-sm-4" style="display:none">
                <label for="photo_name_{{index}}">Photo Name</label>
                <div class="input-group col-xs-12">
                    <input type="text" name="photo_name[{{index}}]" id="photo_name_{{index}}" class="form-control" placeholder="Photo Name" required value="{{photo_name}}">
                </div>
            </div>
        </div>
    </div>
</script>