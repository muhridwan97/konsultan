<div class="modal fade" tabindex="-1" role="dialog" id="modal-add-slot">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Slot</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 2px">Are you sure want to add slot ?</p>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label>Add Slot:</label>

                            <div class="input-group col-md-12">
                            <input type="number" class="form-control" id="slot" name="slot" min="1" placeholder="Number slot">
                            </div>
                            <div class="input-group col-md-12" style = "display:none">
                                <label for="date_type">Date Outbound</label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                        placeholder="Date" autocomplete="off"
                                        maxlength="50" value="<?= set_value('date',date('d F Y',strtotime("+1 day",time()))) ?>">
                            </div>
                            <!-- /.input group -->
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Add Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>