<div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-payout">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="status">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Validating Payout</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 10px">
                        Validate payout <strong id="booking-title"></strong>
                        to allowing booking to next step (<strong>Approve Job</strong> allowed to create job in gate but need second validation in safe conduct)?
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
                        <textarea class="form-control" name="description" id="description" rows="2"
                                  required minlength="6" maxlength="200" placeholder="Validation message"></textarea>
                        <span class="help-block">This message will be included in email to customer</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" value="REJECTED">Reject</button>
                    <button type="submit" class="btn btn-success" value="APPROVED">Approve All</button>
                    <button type="submit" class="btn btn-warning" value="PARTIAL APPROVED">Approve Job Only</button>
                </div>
            </form>
        </div>
    </div>
</div>