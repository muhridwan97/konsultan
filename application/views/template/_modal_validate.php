<div class="modal fade" tabindex="-1" role="dialog" id="modal-validation">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <?= _method('put') ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Validation</h4>
                </div>
                <div class="modal-body">
                    <p class="lead mb10">
                        Are you sure want to <strong class="validate-type"></strong> data <strong class="validate-label"></strong>?
                    </p>
                    <div class="form-group" id="field-validation-message">
                        <label for="message" class="control-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="2" placeholder="Additional message"></textarea>
                        <span class="help-block">This message may be included to the email, chat or histories.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Validate</button>
                </div>
            </form>
        </div>
    </div>
</div>
