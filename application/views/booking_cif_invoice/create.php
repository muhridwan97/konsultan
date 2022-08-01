<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create CIF Invoice</h3>
    </div>

    <form action="<?= site_url('booking-cif-invoice/save') ?>" role="form" method="post" id="form-booking-cif-invoice">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="booking">Booking</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('booking-cif-invoice/ajax-get-available-booking') ?>"
                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                        name="booking" id="booking" data-placeholder="Select booking">
                    <option value=""></option>
                    <?php if (!empty($booking)): ?>
                        <option value="<?= $booking['id'] ?>" selected>
                            <?= $booking['no_reference'] ?> - <?= $booking['customer_name'] ?>
                        </option>
                    <?php endif ?>
                </select>
            </div>

            <div class="box box-primary" id="booking-reference-wrapper" style="display: none">
                <div class="box-header with-border">
                    <h3 class="box-title">Booking In Reference</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal form-view mb0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-3">Booking Type</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-booking-type">-</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">No Booking</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-no-booking">-</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">No Reference</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-no-reference">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-3">Total Item</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-total-item">-</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">Subtotal</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-subtotal">-</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3">Total Price</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static" id="ref-total-price">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="inbound-only">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('currency_from') == '' ?: 'has-error'; ?>">
                            <label for="currency_from">From Currency (Base)</label>
                            <select class="form-control select2" name="currency_from" id="currency_from" data-placeholder="Select base currency" style="width: 100%">
                                <option value=""></option>
                                <?php foreach($currencies as $key => $rates): ?>
                                    <option value="<?= $key ?>"<?= set_select('currency_from', $key) ?>>
                                        <?= $key ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('currency_to') == '' ?: 'has-error'; ?>">
                            <div class="form-group <?= form_error('currency_to') == '' ?: 'has-error'; ?>">
                                <label for="currency_to">To Currency (Rate)</label>
                                <select class="form-control select2" name="currency_to" id="currency_to" data-placeholder="Select rate currency" style="width: 100%">
                                    <option value=""></option>
                                    <?php foreach($currencies as $key => $rates): ?>
                                        <option value="<?= $key ?>"<?= set_select('currency_to', $key) ?>>
                                            <?= $key ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('exchange_date') == '' ?: 'has-error'; ?>">
                            <label for="exchange_date">Rate Date</label>
                            <input type="text" class="form-control datepicker" id="exchange_date" name="exchange_date"
                                   placeholder="Exchange date" required autocomplete="off" maxlength="20"
                                   value="<?= set_value('exchange_date') ?>">
                            <?= form_error('exchange_date', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('exchange_value') == '' ?: 'has-error'; ?>">
                            <label for="exchange_value">Rate Value</label>
                            <input type="text" class="form-control numeric" id="exchange_value" name="exchange_value"
                                   placeholder="Exchange value" required data-touch="0" maxlength="50"
                                   value="<?= set_value('exchange_value') ?>">
                            <?= form_error('exchange_value', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="outbound-only">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('ndpbm') == '' ?: 'has-error'; ?>">
                            <label for="ndpbm">NDPBM (IDR)</label>
                            <input type="text" class="form-control currency" id="ndpbm" name="ndpbm"
                                   placeholder="Nilai Dasar Perhitungan Bea Masuk" autocomplete="off" maxlength="30" required
                                   value="<?= set_value('ndpbm') ?>">
                            <?= form_error('ndpbm', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('import_duty') == '' ?: 'has-error'; ?>">
                            <label for="import_duty">Import Duty / BM (IDR)</label>
                            <input type="text" class="form-control currency" id="import_duty" name="import_duty"
                                   placeholder="Import duty amount" required maxlength="30"
                                   value="<?= set_value('import_duty') ?>">
                            <?= form_error('import_duty', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('vat') == '' ?: 'has-error'; ?>">
                            <label for="vat">VAT / PPN (IDR)</label>
                            <input type="text" class="form-control currency" id="vat" name="vat"
                                   placeholder="VAT amount" autocomplete="off" maxlength="30"
                                   value="<?= set_value('vat') ?>">
                            <?= form_error('vat', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('import_duty') == '' ?: 'has-error'; ?>">
                            <label for="income_tax">Income Tax / PPH (IDR)</label>
                            <input type="text" class="form-control currency" id="income_tax" name="income_tax"
                                   placeholder="Income tax amount" required maxlength="30"
                                   value="<?= set_value('income_tax') ?>">
                            <?= form_error('income_tax', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('party') == '' ?: 'has-error'; ?>">
                <label for="party">Party</label>
                <input type="text" class="form-control" id="party" name="party"
                       placeholder="Party container or LCL" maxlength="100" required
                       value="<?= set_value('party') ?>">
                <?= form_error('party', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">CIF Detail</h3>
                    <button type="button" class="btn btn-primary pull-right" id="btn-add-item">
                        ADD ITEM
                    </button>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered no-datatable responsive" id="table-cif-invoice">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Gross</th>
                            <th>Volume</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th class="item-value-column"<?= !empty($booking) && $booking['category'] == 'INBOUND' ? ' style="display: none"' : '' ?>>Item Value</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $goods = set_value('goods', []) ?>
                        <tr class="row-placeholder"<?= empty($goods) ? '' : ' style="display: none"' ?>>
                            <td colspan="10" class="text-center">
                                Click <strong>Add Item</strong> to insert new record
                            </td>
                        </tr>
                        <?php foreach ($goods as $index => $item): ?>
                            <tr class="<?= !empty($booking) && $booking['category'] == 'OUTBOUND' ? 'from-stock' : '-' ?>">
                                <td><?= $index + 1 ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= numerical($item['quantity'], 3, true) ?></td>
                                <td><?= numerical($item['weight'], 3, true) ?></td>
                                <td><?= numerical($item['gross_weight'], 3, true) ?></td>
                                <td><?= numerical($item['volume']) ?></td>
                                <td><?= numerical($item['price'], 3, true) ?></td>
                                <td><?= numerical($item['price'] * $item['quantity'], 3, true) ?></td>
                                <td class="item-value-column label-item-value"<?= !empty($booking) && $booking['category'] == 'INBOUND' ? ' style="display: none"' : '' ?>>
                                    <?= numerical(get_if_exist($item, 'total_item_value', 0), 3, true) ?>
                                </td>
                                <td>
                                    <input type="hidden" name="goods[<?= $index ?>][goods_name]" id="goods_name" value="<?= $item['goods_name'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][quantity]" id="quantity" value="<?= $item['quantity'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][weight]" id="weight" value="<?= $item['weight'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][gross_weight]" id="gross_weight" value="<?= $item['gross_weight'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][volume]" id="volume" value="<?= $item['volume'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][price]" id="price" value="<?= $item['price'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][description]" id="description" value="<?= $item['description'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_booking_cif_invoice_detail]" id="id_booking_cif_invoice_detail" value="<?= get_if_exist($item, 'id_booking_cif_invoice_detail', get_if_exist($item, 'id')) ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][total_item_value]" id="total_item_value" value="<?= get_if_exist($item, 'total_item_value', 0) ?>">
                                    <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="success" style="font-weight: bold">
                                <td colspan="2">Sub Total</td>
                                <td id="label-total-quantity"></td>
                                <td id="label-total-weight"></td>
                                <td id="label-total-gross-weight"></td>
                                <td id="label-total-volume"></td>
                                <td></td>
                                <td id="label-subtotal-price" style="width: 200px"></td>
                                <td id="label-total-item-value" class="item-value-column"<?= !empty($booking) && $booking['category'] == 'INBOUND' ? ' style="display: none"' : '' ?>></td>
                                <td></td>
                            </tr>
                            <tr class="warning inbound-only">
                                <td colspan="7">
                                    <strong>Discount</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control numeric" name="discount" aria-label="discount"
                                           value="<?= set_value('discount') ?>" placeholder="Discount" id="discount">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="inbound-only">
                                <td colspan="7">
                                    <strong>Freight</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control numeric" name="freight" aria-label="freight"
                                           value="<?= set_value('freight') ?>" placeholder="Freight" id="freight">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="inbound-only">
                                <td colspan="7">
                                    <strong>Insurance</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control numeric" name="insurance" aria-label="insurance"
                                           value="<?= set_value('insurance') ?>" placeholder="Insurance" id="insurance">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="inbound-only">
                                <td colspan="7">
                                    <strong>Handling</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control numeric" name="handling" aria-label="handling"
                                           value="<?= set_value('handling') ?>" placeholder="Handling" id="handling">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="inbound-only">
                                <td colspan="7">
                                    <strong>Other</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control numeric" name="other" aria-label="other"
                                           value="<?= set_value('other') ?>" placeholder="Other" id="other">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="danger inbound-only" style="font-weight: bold">
                                <td colspan="7">Total</td>
                                <td id="label-total-price">0</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Invoice
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('booking_cif_invoice/_modal_goods_input') ?>
<?php $this->load->view('booking_cif_invoice/_modal_goods_stock') ?>
<?php $this->load->view('booking_cif_invoice/_modal_goods_take_stock') ?>

<script src="<?= base_url('assets/app/js/booking-cif-invoice.js') ?>" defer></script>