<div class="modal fade" tabindex="-1" role="dialog" id="modal-request-edit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Request Edit</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">
                        Send email to
                        <strong><?= get_setting('email_bug_report', 'team_it@transcon-indonesia.com') ?></strong>
                        <br>
                        for job <strong id="job-title"></strong>, asking edit data.
                    </p>
                    <p class="text-warning">
                        Please submit you request description and reason of data changes.
                    </p>
                    <div class="form-group">
                        <label for="description" class="control-label">Edit Description</label>
                        <textarea name="description" id="description" cols="30" rows="3"
                                  class="form-control" placeholder="Update data description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="reason" class="control-label">Reason Edit</label>
                        <textarea name="reason" id="reason" cols="30" rows="2"
                                  class="form-control"
                                  placeholder="Reason you need update, and mistake being made"></textarea>
                    </div>
                    <p>Note: We recommend to open ticket and ask your SPV for better tracking support.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Ask Edit Data</button>
                </div>
            </form>
        </div>
    </div>
</div>