<div class="modal fade" tabindex="-1" role="dialog" id="modal-payout-revert">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="status">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Revert Payout</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 5px">
                        Revert payout <strong id="booking-title"></strong>
                        to allowing booking to have previous state?
                    </p>
                    <div class="form-group <?= form_error('payout_until_date') == '' ?: 'has-error'; ?>">
                        <label for="payout_until_date">Payout Until Date</label>
                        <input type="text" class="form-control datepicker" id="payout_until_date" name="payout_until_date" autocomplete="off"
                               placeholder="Payout until date" required maxlength="20">
                        <span class="help-block">
                            Date the payment effective and unlock all job & safe conduct creation
                            (even the status approved but payment date overdue then all transaction cannot be proceed).
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="2" required minlength="6" placeholder="Revert message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" value="REJECTED">Reject</button>
                    <button type="submit" class="btn btn-warning" value="PENDING">Set to Pending</button>
                    <button type="submit" class="btn btn-info" value="PARTIAL APPROVED">Set to Partial Approve</button>
                    <button type="submit" class="btn btn-success" value="DATE">Update Date Only</button>
                </div>
            </form>
        </div>
    </div>
</div>