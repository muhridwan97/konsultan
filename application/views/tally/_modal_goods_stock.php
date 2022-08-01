<div class="modal fade" role="dialog" id="modal-stock-goods" data-booking-id="<?= isset($bookingId) ? $bookingId : '-1' ?>"
data-booking-id-out ="<?= isset($bookingIdOut) ? $bookingIdOut : '-1' ?>">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Stock Goods</h4>
            </div>
            <div class="modal-body">

                <div class="form-horizontal form-view mb0 hidden-xs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Customer</label>
                                <div class="col-sm-9">
                                    <p id="label-customer">
                                        <?= if_empty(isset($customer) ? (is_array($customer) ? get_if_exist($customer, 'name') : '') : '', 'No customer') ?>
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
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-stock-goods', 1)"
                           class="form-control" placeholder="Search goods data...">
                </div>

                <div class="table-editor-wrapper">
                    <div class="table-editor-scroller">
                        <table class="table no-datatable responsive no-wrap" id="table-stock-goods">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>No Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Whey Number</th>
                                <th>Ex Container</th>
                                <th>Unit Weight (Kg)</th>
                                <th>Unit Gross (Kg)</th>
                                <th>Unit Length (M)</th>
                                <th>Unit Width (M)</th>
                                <th>Unit Height (M)</th>
                                <th>Unit Volume (M<sup>3</sup>)</th>
                                <th>Position</th>
                                <th>No Pallet</th>
                                <th>Is Hold</th>
                                <th>Status</th>
                                <th>Danger</th>
                                <th class="text-center sticky-col-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr id="placeholder">
                                <td colspan="18" class="text-center">
                                    All goods are taken
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" id="btn-take-all-stock">Take All</button>
                <button type="button" class="btn btn-default" id="btn-reload-stock">Reload</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>