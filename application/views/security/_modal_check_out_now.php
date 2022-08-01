<div class="modal fade" tabindex="-1" role="dialog" id="modal-security-check-out-now">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" class="need-validation">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="label" id="label">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Check Out</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check out
                        <strong id="check-out-title"></strong>?</p>
                    <p class="text-warning">
                        Security check in automatic set to present time the form is submitted.
                    </p>
                    <?php if ($tep['tep_category'] == 'OUTBOUND' && $tep['armada_owner'] == 'TCI'): ?>
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Chassis Handling</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="chassis_handling_type" class="control-label">Pick Up Chassis Only?</label>
                                    <select class="form-control select2" name="chassis_handling_type" id="chassis_handling_type"
                                            data-placeholder="Chassis handling type" style="width: 100%" required>
                                        <option value=""></option>
                                        <option value="delivery">No, Pick Up Goods / Container / Leave Chassis</option>
                                        <option value="pickup-chassis">Yes, Pick Up Existing Chassis</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="chassis" class="control-label">Existing Chassis</label>
                                    <select class="form-control select2" name="chassis" id="chassis" disabled
                                            data-placeholder="Pick existing chassis to picked up" style="width: 100%" required>
                                        <option value=""></option>
                                        <?php foreach ($outstandingChassis as $chassis): ?>
                                            <option value="<?= $chassis['id'] ?>">
                                                <?= $chassis['no_chassis'] ?> - Dropped by <?= $chassis['tep_code'] ?> (<?= $chassis['receiver_no_police'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="form-group">
                        <label for="description" class="control-label">Check Out Description</label>
                        <textarea name="description" id="description" cols="30" rows="3"
                                  class="form-control" placeholder="Check out remark and additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-danger">Check Out</button>
                </div>
            </form>
        </div>
    </div>
</div>