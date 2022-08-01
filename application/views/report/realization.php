<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Realization Containers</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-realization-container">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Owner</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>Booking Date</th>
                <th>Category</th>
                <th>No Container</th>
                <th>Type</th>
                <th>Size</th>
                <th>Position</th>
                <th>Since</th>
                <th>Realization</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Realization Goods</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-realization-goods">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Owner</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>Booking Date</th>
                <th>Category</th>
                <th>No Goods</th>
                <th>Goods Name</th>
                <th>Unit</th>
                <th class="type-quantity">Quantity</th>
                <th>Position</th>
                <th>No Pallet</th>
                <th>Since</th>
                <th>Realization</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
