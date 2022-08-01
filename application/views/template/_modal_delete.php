<div class="modal fade" tabindex="-1" role="dialog" id="modal-delete">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <?= _method('delete') ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Delete <span class="delete-title"></span></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">
                        Are you sure want to delete <strong class="delete-label"></strong>?
                    </p>
                    <p class="text-danger">
                        All related data will be deleted and this action might be irreversible.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" data-touch-message="Deleting..." class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>