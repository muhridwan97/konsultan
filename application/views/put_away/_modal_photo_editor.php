<div class="modal fade" tabindex="-1" role="dialog" id="modal-photo-editor">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close btn-defer-upload" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Photo Editor</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-success" id="document-uploader">
                        <div class="box-header form-group">
                            <h3 class="box-title">Add Files</h3>
                            <input type="file" id="input-file" multiple name="input_files" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                            <div class="fileinput-button pull-right">
                                <!-- <span class="button-file btn-photo-picker"><i class="ion-upload mr10"></i>Capture</span> -->
                                <!-- <input id="input-file" type="file" multiple name="input_files" accept="image/*"> -->
                                
                                <button class="file-upload-browse btn btn-primary btn-photo-picker button-file" type="button">Capture Photo</button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div id="uploaded-input-wrapper"></div>

                                    <div class="row" id="uploaded-file"></div>
                                </div>
                                <div class="box-footer">
                                    <i class="ion-information-circled mr10"></i>Each individual image should not be greater than 3MB
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box box-danger" id="uploaded-old-file">
                        <div class="box-header">
                            <h3 class="box-title">Uploaded Files</h3>
                        </div>
                        <div class="box-body">
                        </div>
                        <div class="box-footer">
                            <i class="ion-alert-circled mr10"></i>Delete these file will be affecting immediately
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-defer-upload btn-save-photo">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="upload-item-template" type="text/x-custom-template">
    <div class="col-md-6 uploaded-item">
        <div style="display: flex">
            <div>
                <a href="#" class="upload-file-preview-link" target="_blank">
                    <img src="" class="upload-file-preview img-responsive" style="margin: auto; height: 100px;">
                </a>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; flex-grow: 1; margin: 10px;">
                <div id="progress" class="progress progress-upload" style="width: 100%">
                    <div class="progress-bar progress-bar-danger progress-bar-success progress-bar-striped"></div>
                </div>
                <textarea name="photo_description[]" rows="1" class="form-control uploaded_descriptions" placeholder="Photo description"></textarea>
            </div>
            <div style="display: flex; min-height: 100px; align-items: center;">
                <a href="#" data-file="" class="btn btn-warning btn-sm btn-delete-file mr20">
                    CANCEL
                </a>
            </div>
        </div>
        <p class="text-ellipsis mb0 upload-file-name" style="padding: 5px 0">
            Uploading...
        </p>
    </div>
</script>
<script id="uploaded-view-template" type="text/x-custom-template">
    <div class="uploaded-item col-sm-6 col-md-4 text-center" style="padding-bottom: 10px; margin: 10px 0 10px; border-bottom: 1px solid #dfdfdf;">
        <div class="mb10">
            <a href="{{src}}" target="_blank">
                <img src="{{src}}" class="img-responsive" style="margin: auto; height: 100px;">
            </a>
            <p class="mt10 mb0">{{file}}</p>
            <p class="text-muted">{{description}}</p>
        </div>
        <a href="<?= site_url('work-order-goods-photo/delete/{{id}}') ?>" data-file="{{file}}" class="btn btn-delete-uploaded-file btn-danger btn-sm">
            DELETE
        </a>
    </div>
</script>
<script id="only-view-template" type="text/x-custom-template">
    <div class="uploaded-item col-sm-6 col-md-4 text-center" style="padding-bottom: 10px; margin: 10px 0 10px; border-bottom: 1px solid #dfdfdf;">
        <div class="mb10">
            <a href="{{src}}" target="_blank">
                <img src="{{src}}" class="img-responsive" style="margin: auto; height: 100px;">
            </a>
            <p class="mt10 mb0">{{file}}</p>
            <p class="text-muted">{{description}}</p>
        </div>
    </div>
</script>