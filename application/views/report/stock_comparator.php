<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Stock Comparator</h3>
        <div class="pull-right">
            <a href="#form-filter-comparator" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-comparator">
            <input type="hidden" name="filter_stock_comparator" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="booking">Booking</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
                                data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                name="booking" id="booking"
                                data-placeholder="Select booking">
                            <option value=""></option>
                            <?php if (!empty($booking)): ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="containers">Containers</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                        data-key-id="id" data-key-label="no_container"
                                        id="containers" name="containers[]" data-placeholder="Select container" multiple>
                                    <option value=""></option>
                                    <?php foreach ($containers as $container): ?>
                                        <option value="<?= $container['id'] ?>" selected>
                                            <?= $container['no_container'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="items">Items</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                        data-key-id="id" data-key-label="name" data-key-sublabel="no_goods"
                                        id="items" name="items[]" data-placeholder="Select item" multiple>
                                    <option value=""></option>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item['id'] ?>" selected>
                                            <?= $item['name'] ?> - <?= $item['no_goods'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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

        <?php $this->load->view('booking_control/_data_comparator') ?>

    </div>
</div>