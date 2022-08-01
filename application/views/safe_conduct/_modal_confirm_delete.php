<div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-safe-conduct">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Delete Safe Conduct</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to delete safe conduct
                        <strong id="safe-conduct-title"></strong>?
                    </p>
                    <p class="text-danger">
                        This action will perform soft delete, actual data still exist on database.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete Safe Conduct</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->