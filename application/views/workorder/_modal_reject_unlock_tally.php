<div class="modal fade" tabindex="-1" role="dialog" id="modal-reject-unlock-tally">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url()?>work_order/reject_unlock_tally" method="post">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_work_order" id="id_work_order">
                <input type="hidden" name="date_from_reject" id="date_from_reject">
                <input type="hidden" name="date_to_reject" id="date_to_reject">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Reject Unlock Tally</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to <strong>REJECT</strong> Unlock tally
                        <strong id="no-work-order-title"></strong>? <br><br>
                    </p>
                    <div class="row">
                        <div class="col-md-12">
                        <div class="form-group">
                            <label >Reject Reason</label>
                            <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3" placeholder="Reject Reason" required></textarea>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Reject Now</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->