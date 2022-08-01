<div class="modal fade" tabindex="-1" role="dialog" id="modal-ask-approval">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm ask approval</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">
                        Are you sure want to ask approval for
                        <strong id="total-payment-records"></strong> records payment data?
                    </p>
                    <p class="text-danger">
                        Only <strong>REGULAR BANK</strong> type data will be asked for approval.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Ask Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>
