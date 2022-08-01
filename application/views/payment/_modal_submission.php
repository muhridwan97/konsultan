<div class="modal fade" tabindex="-1" role="dialog" id="modal-payment-submission">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">Submit payment</h4>
                </div>
                <div class="modal-body">
                    <p class="lead mb10">
                        Are you sure want to submit payment
                        <strong id="label-submit-no-payment"></strong>?
                    </p>
                    <div class="form-group">
                        <label for="attachment">Attachment (Optional)</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" >
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info"
                                   style="pointer-events: none; background: white; webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">
                                    Select File
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="2" placeholder="Additional message"></textarea>
                        <span class="help-block">This message may be included to the email, chat or histories.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
