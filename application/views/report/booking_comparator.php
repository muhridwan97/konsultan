<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Goods Comparator</h3>
        <div class="pull-right">
            <a href="#form-filter-goods-comparator" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_goods_comparator', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-goods-comparator" <?= get_url_param('filter_goods_comparator', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_goods_comparator" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="category">Category</label>
                                <select class="form-control select2" id="category" name="category" data-placeholder="Select category">
                                    <option value="0">ALL CATEGORY</option>
                                    <option value="INBOUND" <?= get_url_param('category') == 'INBOUND' ? 'selected' : '' ?>>
                                        INBOUND
                                    </option>
                                    <option value="OUTBOUND" <?= get_url_param('category') == 'OUTBOUND' ? 'selected' : '' ?>>
                                        OUTBOUND
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select class="form-control select2" id="status" name="status" data-placeholder="Select status">
                                    <option value="0">ALL STATUS</option>
                                    <option value="APPROVED" <?= get_url_param('status') == 'APPROVED' ? 'selected' : '' ?>>
                                        APPROVED
                                    </option>
                                    <option value="COMPLETED" <?= get_url_param('status') == 'COMPLETED' ? 'selected' : '' ?>>
                                        COMPLETED
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="first_gate_in_from">First Gate In From</label>
                                        <input type="text" class="form-control datepicker" id="first_gate_in_from" name="first_gate_in_from"
                                               placeholder="First gate from" maxlength="50"
                                               value="<?= set_value('first_gate_in_from', get_url_param('first_gate_in_from')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="first_gate_in_to">First Gate In To</label>
                                        <input type="text" class="form-control datepicker" id="first_gate_in_to" name="first_gate_in_to"
                                               placeholder="First gate to" maxlength="50"
                                               value="<?= set_value('first_gate_in_to', get_url_param('first_gate_in_to')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-comparator-goods">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Owner</th>
                <th>Category</th>
                <th>Booking Type</th>
                <th>No Reference</th>
                <th class="type-status">Status</th>
                <th>First Gate In</th>
                <th>No Goods</th>
                <th>Goods Name</th>
                <th class="type-numeric success">Booking Quantity</th>
                <th class="type-numeric info">Work Order Quantity</th>
                <th>Booking Unit</th>
                <th>Work Order Unit</th>
                <th>Booking Ex Container</th>
                <th class="type-numeric">Unit Weight</th>
                <th class="type-numeric">Work Order Total Weight</th>
                <th class="type-numeric">Unit Gross Weight</th>
                <th class="type-numeric">Work Order Total Gross Weight</th>
                <th class="type-numeric">Unit Volume</th>
                <th class="type-numeric">Work Order Total Volume</th>
                <th>Work Order Ex Container</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report_comparator.js?v=2') ?>" defer></script>
