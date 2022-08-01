<div class="modal fade" tabindex="-1" role="dialog" id="modal-update-max-print">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Total Max Print</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to change total print for Job<br>
                            <strong id="work-order-title"></strong>?</p>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-md-3">
                            <div class="form-group">
                                <label class="control-label">Current Job Print</label>
                                <p class="form-control-static" id="work-order-print"></p>
                            </div>
                        </div>
                        <div class="col-xs-8 col-md-9">
                            <div class="form-group">
                                <label for="print_max" class="control-label">Change Max Print To</label>
                                <input type="number" step="1" class="form-control" placeholder="Total Print" name="print_max"
                                       id="print_max" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Update Total Max Print</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->