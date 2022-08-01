<form role="form" method="get" class="form-filter"
      id="form-filter-container" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : '' ?>>
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Shipping Line</label>
                                <select class="form-control select2" id="shipping_line" name="shipping_line[]"
                                        data-placeholder="Select shipping line" multiple>
                                    <option value=""></option>
                                    <?php foreach ($shippingLines as $shippingLine): ?>
                                        <option value="<?= $shippingLine['id'] ?>" <?= in_array($shippingLine['id'], get_url_param('shipping_line', [])) ? 'selected' : '' ?>>
                                            <?= $shippingLine['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Stock Date</label>
                                <input type="text" class="form-control datepicker" id="stock_date" name="stock_date"
                                       placeholder="Stock Date"
                                       maxlength="50"
                                       value="<?= set_value('stock_date', get_url_param('stock_date')) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
