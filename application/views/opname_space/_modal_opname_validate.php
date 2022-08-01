
<div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-opname">
    <div class="modal-dialog" role="upload">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Validating Opname</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Validate opname
                        <strong id="opname-title"></strong>?
                    </p>
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="2" placeholder="Validation message"></textarea>
                        <span class="help-block">This message will be included in email to customer</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="status" value="VALIDATE">Validate</button>
                </div>
            </form>
        </div>
    </div>
</div>