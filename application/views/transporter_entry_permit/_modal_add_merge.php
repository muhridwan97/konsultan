<div class="modal fade" role="dialog" id="modal-add-merge" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Merge Request</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_customer" id="id_customer">
                <input type="hidden" name="id_upload" id="id_upload">
                <input type="hidden" name="slot" id="slot">
                <input type="hidden" name="slot_created" id="slot_created">
                <input type="hidden" name="armada" id="armada">
                <input type="hidden" name="category" id="category">
                <div class="form-horizontal form-view mb0 hidden-xs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Customer</label>
                                <div class="col-sm-9">
                                    <p id="label-customer">
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Ref</label>
                                <div class="col-sm-9">
                                    <p id="label-reference">
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Slot</label>
                                <div class="col-sm-9">
                                    <p id="label-slot">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt0 hidden-xs">

                <div class="form-group">
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-tep-queue', 2)"
                           class="form-control" placeholder="Search TEP code...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered no-datatable responsive" id="table-tep-queue">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Customer Name</th>
                            <th>TEP Code</th>
                            <th>REF</th>
                            <th>Description</th>
                            <th>Queue Time</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="placeholder">
                            <td colspan="12" class="text-center">
                                No TEP Available
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btn-reload-tep">Reload</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('transporter_entry_permit/_modal_merge_validate') ?>