<div class="modal fade" role="dialog" id="modal-stock-container" data-booking-id="<?= isset($bookingId) ? $bookingId : '' ?>">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Stock Container</h4>
            </div>
            <div class="modal-body">

                <div class="form-horizontal form-view mb0 hidden-xs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Customer</label>
                                <div class="col-sm-9">
                                    <p id="label-customer">
                                        <?= if_empty(isset($customer) ? $customer : '', 'No customer') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Reference</label>
                                <div class="col-sm-9">
                                    <p id="label-reference">
                                        <?= if_empty(isset($noReference) ? $noReference : '', 'No reference') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt0 hidden-xs">

                <div class="form-group">
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-stock-container', 1)"
                           class="form-control" placeholder="Search container data...">
                </div>

                <table class="table table-bordered no-datatable responsive" id="table-stock-container">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Container</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Seal</th>
                        <th>Position</th>
                        <th>Is Empty</th>
                        <th>Is Hold</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="placeholder">
                        <td colspan="16" class="text-center">
                            All goods are taken
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btn-reload-stock">Reload</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>