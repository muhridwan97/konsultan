<div class="modal fade" role="dialog" id="modal-take-goods">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" id="form-take-goods" class="need-validation">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Hold Goods</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal form-view mb0">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="col-sm-3 mb0">Goods</label>
                                    <div class="col-sm-9">
                                        <p id="label-goods" class="mb0">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 mb0">Unit</label>
                                    <div class="col-sm-9">
                                        <p id="label-unit" class="mb0">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="hold_description">Description</label>
                        <textarea class="form-control" id="hold_description" name="hold_description" required
                                  placeholder="Hold item reason" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="btn-confirm-take-goods">Take</button>
                </div>
            </form>
        </div>
    </div>
</div>