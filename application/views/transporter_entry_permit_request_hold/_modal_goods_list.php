<div class="modal fade" role="dialog" id="modal-goods-list" data-customer-id="<?= $customer['id'] ?? UserModel::authenticatedUserData('id_person', '-1') ?>">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Requested Goods</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <input type="text" id="search-data" onkeyup="searchTable(this, 'table-goods-list', 2)"
                           class="form-control" placeholder="Search goods name..." aria-label="Search">
                </div>

                <div class="table-editor-wrapper">
                    <div class="table-editor-scroller">
                        <table class="table no-datatable responsive no-wrap mb10" id="table-goods-list">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>No Reference</th>
                                <th>Goods Name</th>
                                <th>Unit</th>
                                <th>Ex No Container</th>
                                <th>Qty Requested</th>
                                <th>Qty Realized</th>
                                <th>Related Request</th>
                                <th class="text-center sticky-col-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr id="placeholder">
                                <td colspan="8" class="text-center">
                                    All goods are taken
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" id="btn-take-all-goods">Take All</button>
                <button type="button" class="btn btn-default" id="btn-reload-goods">Reload</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>