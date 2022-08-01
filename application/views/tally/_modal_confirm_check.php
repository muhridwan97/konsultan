<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-check-tally">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="source_submission" value="modal">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_customer" id="id_customer">
                <input type="hidden" name="category" id="category">
                <div class="modal-header">
                    <h4 class="modal-title">Finish Check Job</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to finish job order
                        <strong id="job-title"></strong>?</p>
                    <p class="text-danger">
                        This action will persist your tally input into database.
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
                                        <input type="text" name="photo_name[0]" id="photo_name_0" class="form-control" placeholder="Photo Name" required >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tally-wrapper">
                        <?php $this->load->view('tally/_field_vas_and_resources') ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Complete Tally</button>
                </div>
            </form>
        </div>
    </div>
</div>