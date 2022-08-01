<div class="modal fade" tabindex="-1" role="dialog" id="modal-pay-invoice">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Pay Invoice</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" name="payment_date" id="payment_date" class="form-control datepicker" placeholder="Payment date" required>
                    </div>
                    <div class="form-group">
                        <label for="transfer_bank" class="control-label">Transfer Bank</label>
                        <select name="transfer_bank" id="transfer_bank" class="form-control">
                            <option value="">-- SELECT BANK --</option>
                            <option value="BANK MANDIRI">BANK MANDIRI</option>
                            <option value="BANK BCA">BANK BCA</option>
                            <option value="BANK BNI">BANK BNI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transfer_amount" class="control-label">Transfer Amount</label>
                        <input type="text" name="transfer_amount" id="transfer_amount" class="form-control currency" placeholder="Amount of bank transfer">
                    </div>
                    <div class="form-group">
                        <label for="cash_amount" class="control-label">Cash Amount</label>
                        <input type="text" name="cash_amount" id="cash_amount" class="form-control currency" placeholder="Amount of cash payment">
                    </div>
                    <div class="form-group">
                        <label for="over_payment_amount" class="control-label">Over Payment Amount</label>
                        <input type="text" name="over_payment_amount" id="over_payment_amount" class="form-control currency" placeholder="Amount of over payment">
                    </div>
                    <div class="form-group">
                        <label for="payment_description" class="control-label">Description</label>
                        <textarea name="payment_description" id="payment_description" class="form-control" placeholder="Payment description" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->