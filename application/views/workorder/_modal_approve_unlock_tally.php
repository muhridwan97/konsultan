<div class="modal fade" tabindex="-1" role="dialog" id="modal-approve-unlock-tally">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url()?>work_order/approve_unlock_tally" method="post">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_work_order" id="id_work_order">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Approve Unlock Tally</h4>
                </div>
                <div class="modal-body">
                <p class="lead" style="margin-bottom: 0">Request by <strong id="name-request"></strong><br>
                    Reason Unlock <strong id="reason-unlock"></strong><br>
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="text" class="form-control datepicker" id="date_from_approve" name="date_from_approve"
                                        placeholder="Date from"
                                        maxlength="50" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Until</label> 
                            <input type="text" class="form-control datepicker" id="date_to_approve" name="date_to_approve"
                                        placeholder="Date to"
                                        maxlength="50" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group">
                        <label >Approve Reason</label>
                        <textarea class="form-control" name="approve_reason" id="approve_reason" rows="3" placeholder="Approve Reason" required></textarea>
                    </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Approve Now</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->