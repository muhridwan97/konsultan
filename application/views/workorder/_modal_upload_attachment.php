<div class="modal fade" tabindex="-1" role="dialog" id="modal-upload-attachment">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Job Attachment</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Upload an attachment for
                        <strong id="job-title"></strong>?
                    </p>
                    <p class="text-warning">
                        Re upload file will remove existing job attachment.
                    </p>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <p class="text-danger" id="job-attachment"></p>
                        <input type="file" name="attachment" id="attachment" required
                               accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                               placeholder="Attachment job">
                        <?= form_error('photo', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Upload Attachment</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->