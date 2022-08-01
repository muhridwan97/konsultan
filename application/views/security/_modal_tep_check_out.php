<div class="modal fade" tabindex="-1" role="dialog" id="modal-tep-check-out">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">TEP Check Out : <strong><?= $tep['category'] ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check out
                        <strong id="check-out-title"></strong>?
                    </p>
                    <p class="text-warning">
                        Security check in automatic set to present time when the form is submitted.
                    </p>
                    <div class="form-group">
                        <label for="description" class="control-label">Check Out Description</label>
                        <textarea name="description" id="description" cols="30" rows="2" maxlength="500"
                                  class="form-control" placeholder="Check in remark"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-success" data-toggle="one-touch">Check Out</button>
                </div>
            </form>
        </div>
    </div>
</div>