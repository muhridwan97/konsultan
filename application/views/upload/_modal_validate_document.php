<div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-document">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Validating Document</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Validate document
                        <strong id="document-title"></strong>?
                    </p>
                    <p class="text-danger">
                        Uploader will be notified about approving or rejecting document.
                    </p>
                    <div id="document-viewer" class="text-center">

                    </div>
                    <div class="form-group">
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="3" placeholder="Validation message"></textarea>
                        <span class="help-block">This message will be included in email to the Uploader</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="status" value="-1">Reject</button>
                    <button type="submit" class="btn btn-success" name="status" value="1">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>