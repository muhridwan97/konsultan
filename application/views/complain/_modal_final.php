<div class="modal fade" tabindex="-1" role="dialog" id="modal-set-final">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('complain/set-final/' . $complain['id'])?>" method="post" class="need-validation" enctype="multipart/form-data" id="set-final-form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Set Final</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" rows="5" required name="description"
                                  placeholder="Enter description"><?= set_value('description', $lastConclusion['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" required data-max-size="3000000" >
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>