<div class="modal fade" tabindex="-1" role="dialog" id="modal-generate-token">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('token/save') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Generate Token</h4>
                </div>
                <div class="modal-body">
                    <p class="lead mb10">
                        Generate token by type?
                    </p>
                    <div class="form-group">
                        <label for="type">Token Type</label>
                        <select name="type" id="type" class="form-control select2" style="width: 100%" required data-placeholder="Select token type">
                            <option value=""></option>
                            <option value="OVERRIDE AUTHORIZATION" selected>OVERRIDE AUTHORIZATION</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="max_activation">Max Activation</label>
                        <input type="number" class="form-control" name="max_activation" id="max_activation" placeholder="Counter that we use privilege overriding" value="1">
                    </div>
                    <div class="form-group">
                        <label for="expired_at">Expire Date</label>
                        <input type="text" class="form-control datepicker" name="expired_at" id="expired_at"
                               placeholder="Maximum token available to be used" value="<?= format_date('tomorrow', 'd F Y') ?>">
                        <span class="help-block">Token cannot be used after the expiration date (the day after the date is set).</span>
                    </div>
                    <p class="text-danger">
                        <strong>Caution:</strong> This action will replace old token if already exist.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Generate Token</button>
                </div>
            </form>
        </div>
    </div>
</div>