<div class="modal fade" tabindex="-1" role="dialog" id="modal-upload-faktur">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Upload Faktur</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachment_faktur">Faktur File</label>
                        <p id="uploaded_faktur"></p>
                        <input type="file" name="attachment_faktur" id="attachment_faktur" required
                               accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                               placeholder="Attachment invoice">
                        <?= form_error('attachment_faktur', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="no_faktur" class="control-label">Faktur Number</label>
                        <input type="text" name="no_faktur" id="no_faktur" class="form-control" placeholder="Tax faktur number" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Faktur</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->