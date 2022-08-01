<div class="modal fade" tabindex="-1" role="dialog" id="modal-security-check-in">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Check In : <strong><?= $category ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check in
                        <strong id="check-in-title"></strong>?</p>
                    <p class="text-warning">
                        Security check in automatic set to present time when the form is submitted.
                    </p>
                    <div class="form-group">
                        <label for="driver" class="control-label">Driver</label>
                        <input type="text" class="form-control" name="driver" id="driver" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="no_police" class="control-label">No Police</label>
                        <input type="text" class="form-control" name="no_police" id="no_police" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="expedition" class="control-label">Expedition</label>
                        <input type="text" class="form-control" name="expedition" id="expedition" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Check In Description</label>
                        <textarea name="description" id="description" cols="30" rows="2"
                                  class="form-control" placeholder="Check in remark and additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-success">Check In</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->