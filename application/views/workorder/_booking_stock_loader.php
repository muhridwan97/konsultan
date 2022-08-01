<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Ref stock data</h3>
    </div>
    <div class="box-body" id="stock-data-wrapper">
        <p class="text-muted">Container or goods of related booking</p>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Loading</h3>
        <p class="text-muted mb0">Set item from stock</p>
    </div>
    <div class="box-body">

        <p class="lead mb0">Taken Containers</p>
        <table class="table no-datatable mb20">
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
                <th style="width: 100px">Action</th>
            </tr>
            </thead>
            <tbody id="destination-container-wrapper">
            <tr id="placeholder">
                <td colspan="11" class="text-center">No loading any container</td>
            </tr>
            </tbody>
        </table>

        <p class="lead mb0">Taken Items</p>
        <table class="table no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Goods</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Tonnage (Kg)</th>
                <th>Tonnage Gross (Kg)</th>
                <th>Volume (M<sup>3</sup>)</th>
                <th>Position</th>
                <th>No Pallet</th>
                <th>Is Hold</th>
                <th>Status</th>
                <th>Danger</th>
                <th style="width: 100px">Action</th>
            </tr>
            </thead>
            <tbody id="destination-item-wrapper">
            <tr id="placeholder">
                <td colspan="13" class="text-center">No loading any item</td>
            </tr>
            </tbody>
        </table>

        <div class="form-group mt20">
            <label for="total_items">Total item is handled</label>
            <input type="number" required readonly value="0" min="1" class="form-control" id="total_items" name="total_items">
        </div>
    </div>
</div>