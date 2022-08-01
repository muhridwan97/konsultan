<div class="modal fade" role="dialog" id="modal-stock-goods" data-customer-id="<?= UserModel::authenticatedUserData('id_person', '-1') ?>">
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
                    
                </div>

                <hr class="mt0 hidden-xs">

                <div class="form-group">
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-stock-goods', 1)"
                           class="form-control" placeholder="Search no reference data...">
                </div>

                <div class="table-editor-wrapper">
                    <div class="table-editor-scroller">
                        <table class="table no-datatable responsive no-wrap" id="table-stock-goods">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>No Reference</th>
                                <th>No Invoice</th>
                                <th>No BL</th>
                                <th>No Goods</th>
                                <th>Goods Name</th>
                                <th>Whey Number</th>
                                <th>Unit</th>
                                <th>Stock Quantity</th>
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
                <!-- <button type="button" class="btn btn-danger pull-left" id="btn-take-all-stock">Take All</button> -->
                <button type="button" class="btn btn-default" id="btn-reload-stock">Reload</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>