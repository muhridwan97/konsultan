<div class="modal fade" tabindex="-1" role="dialog" id="modal-check-token">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Token Override Authorization</h4>
            </div>
            <div class="modal-body">
                <p class="lead mb10">Input user token to override authorization?</p>
                <div class="form-group token-field-group">
                    <input type="text" class="form-control" name="token" id="token" placeholder="Token value">
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-check">Check Token</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?= base_url('assets/app/js/token.js') ?>" defer></script>