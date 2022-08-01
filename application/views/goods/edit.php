<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Item</h3>
    </div>
    <form action="<?= site_url('goods/update/' . $item['id']) ?>" role="form" method="post" class="need-validation" id="form-goods">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name"
                        name="customer" id="customer"
                        data-placeholder="Select customer" required>
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?> - <?= $customer['no_person'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('no_goods') == '' ?: 'has-error'; ?>">
                        <label for="no_goods">Item Code</label>
                        <input type="text" class="form-control" id="no_goods" name="no_goods"
                               placeholder="Enter Item Code"
                               required maxlength="50" value="<?= set_value('no_goods', $item['no_goods']) ?>">
                        <?= form_error('no_goods', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('whey_number') == '' ?: 'has-error'; ?>">
                        <label for="whey_number">Whey Number</label>
                        <input type="text" class="form-control" id="whey_number" name="whey_number"
                               placeholder="Enter whey number"
                               required maxlength="50" value="<?= set_value('whey_number', $item['whey_number']) ?>">
                        <?= form_error('whey_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('no_hs') == '' ?: 'has-error'; ?>">
                        <label for="no_hs">HS Code</label>
                        <input type="text" class="form-control" id="no_hs" name="no_hs"
                               placeholder="Enter HS Code"
                               required maxlength="50" value="<?= set_value('no_hs', $item['no_hs']) ?>">
                        <?= form_error('no_hs', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
			<div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Item Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter Name"
                       required maxlength="100" value="<?= set_value('name', $item['name']) ?>">
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('type_goods') == '' ?: 'has-error'; ?>">
                <label for="type_goods">Type</label>
                <input type="text" class="form-control" id="type_goods" name="type_goods"
                       placeholder="Type of goods"
                       required maxlength="200" value="<?= set_value('type_goods', $item['type_goods']) ?>">
                <?= form_error('type_goods', '<span class="help-block">', '</span>'); ?>
            </div>
			<div class="form-group <?= form_error('shrink_tolerance') == '' ?: 'has-error'; ?>">
                <label for="shrink_tolerance">Shrink Tolerance (%)</label>
                <input type="number" min="0" max="100" step="1" class="form-control" id="shrink_tolerance" name="shrink_tolerance"
                       placeholder="Enter shrink tolerance in percent"
                       value="<?= set_value('shrink_tolerance', $item['shrink_tolerance']) ?>">
                <?= form_error('shrink_tolerance', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary mt10">
                <div class="box-header">
                    <h3 class="box-title">Goods Attribute</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_weight') == '' ?: 'has-error'; ?>">
                                <label for="unit_weight">Unit Weight</label>
                                <input type="text" class="form-control numeric" id="unit_weight" name="unit_weight"
                                       placeholder="Type of weight"
                                       maxlength="10" value="<?= set_value('unit_weight', numerical($item['unit_weight'], 3, true)) ?>">
                                <?= form_error('unit_weight', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_gross_weight') == '' ?: 'has-error'; ?>">
                                <label for="unit_gross_weight">Unit Gross Weight</label>
                                <input type="text" class="form-control numeric" id="unit_gross_weight" name="unit_gross_weight"
                                       placeholder="Type of gross weight"
                                       maxlength="10" value="<?= set_value('unit_gross_weight', numerical($item['unit_gross_weight'], 3, true)) ?>">
                                <?= form_error('unit_gross_weight', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group <?= form_error('unit_volume') == '' ?: 'has-error'; ?>">
                                <label for="unit_volume">Unit Volume</label>
                                <input type="text" class="form-control numeric" id="unit_volume" name="unit_volume"
                                       value="<?= set_value('unit_volume', numerical($item['unit_volume'])) ?>" placeholder="Volume of item" maxlength="50" readonly>
                                <?= form_error('unit_volume', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_length">Unit Length (M)</label>
                                <input type="text" class="form-control numeric" id="unit_length" name="unit_length"
                                       placeholder="Length of item" value="<?= set_value('unit_length', numerical($item['unit_length'], 3, true)) ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_length', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_width">Unit Width (M)</label>
                                <input type="text" class="form-control numeric" id="unit_width" name="unit_width"
                                       placeholder="Width of item" value="<?= set_value('unit_width', numerical($item['unit_width'], 3, true)) ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_width', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_height">Unit Height (M)</label>
                                <input type="text" class="form-control numeric" id="unit_height" name="unit_height"
                                       placeholder="Height of item" value="<?= set_value('unit_height', numerical($item['unit_height'], 3, true)) ?>" maxlength="5" data-default="0">
                                <?= form_error('unit_height', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Item Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Item description"
                          maxlength="500"><?= set_value('description', $item['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if(count($assemblyGoods)): ?>
                <input type="hidden" name="id_assembly" value="<?= $assembly['id'] ?>">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Input Item Assembly Goods</h3>
                    </div>
                    <div class="box-body">
                        <button class="btn btn-sm btn-success btn-add-record-assembly-goods pull-right" type="button">
                            <i class="fa ion-plus"></i>
                        </button>
                        <div style="padding-right: 40px">
                            <select class="form-control select2 select2-ajax select-assembly-goods"
                                    data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                    data-key-id="id" data-key-label="name"
                                    name="id_goods" id="id_goods"
                                    data-placeholder="Select goods">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Assembly Goods Record</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped no-datatable" id="table-assembly-goods">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th class="text-center" style="width: 30px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php foreach($assemblyGoods as $itemGoods): ?>
                                <tr class="row-assembly-goods-item">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?= $itemGoods['goods_name'] ?>
                                        <input type="hidden" value="<?= $itemGoods['assembly_goods'] ?>" name="assembly_goods[]" id="assembly_goods[]">
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control qty-assembly-goods" id="qty_assembly_goods[]" name="qty_assembly_goods[]" placeholder="Enter Quantity Goods" value="<?= set_value('qty_assembly_goods', numerical($itemGoods['quantity_assembly'], 3, true)) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control select2" name="units[]" id="detail_unit"
                                                    data-placeholder="Unit" required style="width: 100%;">
                                                <option value=""></option>
                                                <?php foreach ($units as $unit): ?>
                                                    <option value="<?= $unit['id'] ?>" <?= set_value('units', $itemGoods['id_unit']) == $unit['id'] ? 'selected' : '' ?>>
                                                        <?= $unit['unit'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-delete-assembly-goods-detail">
                                            <i class="ion-trash-b"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Item</button>
        </div>
    </form>
</div>

<script id="row-assembly-goods-item-template" type="text/x-custom-template">
    <tr class="row-assembly-goods-item">
        <td></td>
        <td>
            <span class="label-goods">{{goods_name}}</span>
        </td>
        <td>
            <div class="form-group">
                <input type="text" class="form-control qty-assembly-goods" id="qty_assembly_goods[]" name="qty_assembly_goods[]" placeholder="Enter Quantity Goods" required>
            </div> 
        </td>
        <td>
            <div class="form-group">
                <select class="form-control select2" name="units[]" id="detail_unit"
                        data-placeholder="Unit" required style="width: 100%;">
                    <option value=""></option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['id'] ?>">
                            <?= $unit['unit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </td>
        <td>
            <input type="hidden" value="{{id_goods}}" name="assembly_goods[]" id="assembly_goods[]">
            <a href="#" class="btn btn-danger btn-delete-assembly-goods-detail">
                <i class="fa ion-trash-a"></i>
            </a>
        </td>
    </tr>
</script>

<script src="<?= base_url('assets/app/js/goods.js?v=2') ?>" defer></script>
<script src="<?= base_url('assets/app/js/goods_assembly.js') ?>"></script>