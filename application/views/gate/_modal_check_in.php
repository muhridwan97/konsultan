<div class="modal fade" tabindex="-1" role="dialog" id="modal-gate-check-in">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Gate Check In : <strong><?= $category ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check in job no
                        <strong id="check-in-title"></strong>?</p>
                    <p class="text-warning">
                        Gate check in automatic set to present time when job is created.
                    </p>
                    <div class="form-group">
                        <label for="description" class="control-label">Check In Description</label>
                        <textarea name="description" id="description" cols="30" rows="3"
                                  class="form-control" placeholder="Check in remark and additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>