<div class="modal fade" tabindex="-1" role="dialog" id="modal-create-job">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id_booking" id="id_booking">
                <input type="hidden" name="id_handling" id="id_handling">
                <input type="hidden" name="id_safe_conduct" id="id_safe_conduct">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create Job Handling</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to creating job for handling
                        <strong id="handling-title"></strong>?</p>
                    <p class="text-danger">
                        Create job sheet as handling document assignment.
                    </p>

                    <?php if(!isset($handling)): ?>
                        <?php $this->load->view('gate/_field_handling_component') ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="3"
                                  class="form-control" placeholder="Job description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Create Job</button>
                </div>
            </form>
        </div>
    </div>
</div>