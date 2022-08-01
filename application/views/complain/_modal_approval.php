<div class="modal fade" tabindex="-1" role="dialog" id="modal-approval">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?=base_url('complain/approval')?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" enctype="multipart/form-data">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="approval-title"></span></h4>
                </div>
                <input type="hidden" id="id_complain" name="id_complain">
                <input type="hidden" id="approval" name="approval">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea class="form-control" id="note" required name="note" placeholder="Enter note" maxlength="500"><?= set_value('note') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000" >
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
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>