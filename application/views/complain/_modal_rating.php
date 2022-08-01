<div class="modal fade" tabindex="-1" role="dialog" id="modal-rating">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Give Complain Rating</h4>
                </div>
                <div class="modal-body">
                    <?php $this->load->view('complain/_rating_fields') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>