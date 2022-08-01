<div class="modal fade" tabindex="-1" role="dialog" id="modal-request-open-tally">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url()?>work_order/set_request_unlock_tally" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Request Open Tally</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_from_request">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from_request" name="date_from_request"
                                            placeholder="Date from"
                                            maxlength="50" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_to_request">Until</label>
                                <input type="text" class="form-control datepicker" id="date_to_request" name="date_to_request"
                                            placeholder="Date to"
                                            maxlength="50" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unlocked_reason">Unlocked Reason</label>
                        <textarea class="form-control" name="unlocked_reason" id="unlocked_reason" rows="3" placeholder="Unlocked Reason" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="unlocked_reason">Request Edit Type</label>
                        <div>
                            <label class="radio">
                                <input type="radio" name="edit_type" value="unit-attribute" checked>
                                Edit Quantity, Weight or Volume
                            </label>
                            <label class="radio">
                                <input type="radio" name="edit_type" value="other-attribute">
                                Edit other (TEP, warehouse, description)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Request Now</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->