<div class="modal fade" role="dialog" id="modal-stock-goods">
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
                                        -
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3 mb0">Reference</label>
                                <div class="col-sm-9">
                                    <p id="label-reference">
                                        -
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt0 hidden-xs">

                <div class="form-group">
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-stock-goods', 1)"
                           class="form-control" placeholder="Search goods data..." aria-label="search-goods">
                </div>

                <div class="table-editor-wrapper">
                    <div class="table-editor-scroller">
                        <table class="table no-datatable responsive no-wrap" id="table-stock-goods">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods Name</th>
                                <th>Quantity</th>
                                <th>Weight</th>
                                <th>Gross</th>
                                <th>Volume</th>
                                <th>Price</th>
                                <th>Total Price</th>
                                <th>Item Value</th>
                                <th class="text-center sticky-col-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr id="placeholder">
                                <td colspan="10" class="text-center">
                                    All goods are taken
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>