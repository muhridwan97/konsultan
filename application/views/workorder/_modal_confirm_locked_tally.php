<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-locked-tally">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Locked Tally</h4>
            </div>
            <div class="modal-body">
                <p class="lead" style="margin-bottom: 0">Are you sure want to <strong id="print-lock"></strong> tally
                    <strong id="print-title"></strong>? <br>
                    <small id="print-subtitle">Tally will be <strong id="print-lock"></strong> from 
                        <strong id="date-from"></strong> to
                        <strong id="date-to"></strong>
                    </small>
                </p>
                <p class="small text-danger" id="warning">
                    
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="submit_locked">Locked Now</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->