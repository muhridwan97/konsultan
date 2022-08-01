<div class="modal fade" tabindex="-1" role="dialog" id="modal-security-check-out">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Check Out : <strong><?= $category ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check out
                        <strong id="check-out-title"></strong>?</p>
                    <p class="text-warning">
                        Security check in automatic set to present time the form is submitted.
                    </p>
                    <div class="form-group">
                        <label for="description" class="control-label">Check Out Description</label>
                        <textarea name="description" id="description" cols="30" rows="3"
                                  class="form-control" placeholder="Check out remark and additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-danger">Check Out</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->