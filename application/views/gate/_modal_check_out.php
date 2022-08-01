<div class="modal fade" tabindex="-1" role="dialog" id="modal-gate-check-out">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Gate Check Out : <strong><?= $category ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check out job no
                        <strong id="check-out-title"></strong>?</p>
                    <p class="text-warning mb20">
                        We will notify customer <strong id="check-out-customer"></strong>
                        with email address <strong id="check-out-email"></strong>.
                    </p>

                    <?php $this->load->view('gate/_field_handling_component') ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Job Remarks
                        </div>
                        <div class="panel-body">
                            <?php if($category != HandlingTypeModel::CATEGORY_NON_WAREHOUSE): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="overtime_yes" class="control-label mr10">Overtime</label>
                                            <input type="radio" value="1" name="overtime" id="overtime_yes" class="form-control"> Yes &nbsp;
                                            <input type="radio" value="0" name="overtime" id="overtime_no" class="form-control" checked> No
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="staple_yes" class="control-label mr10">Staple</label>
                                            <input type="radio" value="1" name="staple" id="staple_yes" class="form-control"> Yes &nbsp;
                                            <input type="radio" value="0" name="staple" id="staple_no" class="form-control" checked> No
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group">
                                            <label for="man_power" class="control-label">Man Power</label>
                                            <input type="number" value="" min="0" max="100" step="1" name="man_power" id="man_power"
                                                   class="form-control" placeholder="Total labour involved">
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group">
                                            <label for="forklift" class="control-label">Forklift (minutes)</label>
                                            <input type="number" value="" min="0" max="100" step="1" name="forklift" id="forklift"
                                                   class="form-control" placeholder="Forklift operational in hours">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tools" class="control-label">Tools</label>
                                        <textarea name="tools" id="tools" cols="30" rows="1"
                                                  class="form-control" placeholder="Additional tools that used"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="materials" class="control-label">Materials</label>
                                        <textarea name="materials" id="materials" cols="30" rows="1"
                                                  class="form-control" placeholder="Stuff that used in handling process"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="control-label">Check Out Description</label>
                        <textarea name="description" id="description" cols="30" rows="2"
                                  class="form-control" placeholder="Check out remark and additional information"></textarea>
                        <span class="help-block">The description will be included in email that sent to customer.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" data-toggle="one-touch">Check Out & Notify Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>