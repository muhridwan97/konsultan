<div class="modal fade" tabindex="-1" role="dialog" id="modal-approved">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Approved</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="no" id="no">
                    <p class="lead mb10">
                        Are you sure want to <strong>Approve</strong> job <strong class="approved-label"></strong>?
                    </p>
                    <div class="form-group">
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="1" placeholder="Approve message"></textarea>
                        <span class="help-block">This message may be included to histories.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-submit-approve" data-toggle="one-touch">Approved</button>
                </div>
            </form>
        </div>
    </div>
</div>