<div class="box box-primary">
    <div class="box-header">
        <div class="box-title">Create New Shifting</div>
    </div>
    <form action="<?= site_url('shifting/save') ?>" role="form" class="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="shifting_date">Shifting Date</label>
                <input type="text" class="form-control daterangepicker2"
                       name="shifting_date" id="shifting_date"
                       placeholder="Shifting date" value="<?= set_value('shifting_date') ?>"
                       required>
                <?= form_error('shifting_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" cols="30" rows="2" class="form-control"
                          placeholder="Description shifting" required><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Input Item</h3>
                </div>
                <div class="box-body">
                    <button class="btn btn-sm btn-success btn-add-record-shifting pull-right" type="button">
                        <i class="fa ion-plus"></i>
                    </button>
                    <div style="padding-right: 40px">
                        <!--
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('shifting/ajax_get_container_goods') ?>"
                                data-key-id="id_container_goods" data-key-label="container_goods"
                                data-template="handling-shifting"
                                name="container_goods" id="container_goods"
                                data-placeholder="Select Container or Goods"
                                style="width: 100%;">
                            <option value=""></option>
                        </select>
                        -->
                        <select class="form-control select2"
                                name="container_goods" id="container_goods"
                                data-placeholder="Select Container or Goods"
                                style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id_container_goods'] ?>"
                                    data-last_position="<?= $item['last_position'] ?>"
                                    data-position_blocks="<?= $item['position_blocks'] ?>"
                                    data-id_customer="<?= $item['id_customer'] ?>"
                                    data-id_booking="<?= $item['id_booking'] ?>"
                                    data-container_goods_type="<?= $item['container_goods_type'] ?>"
                                    data-id_container_goods="<?= $item['id_container_goods'] ?>"
                                    data-id_unit="<?= $item['id_unit'] ?>"
                                    data-quantity="<?= $item['quantity'] ?>"
                                    data-tonnage="<?= $item['weight'] ?>"
                                    data-tonnage_gross="<?= $item['gross_weight'] ?>"
                                    data-length="<?= $item['length'] ?>"
                                    data-width="<?= $item['width'] ?>"
                                    data-height="<?= $item['height'] ?>"
                                    data-volume="<?= $item['volume'] ?>"
                                    data-seal="<?= $item['seal'] ?>"
                                    data-no_pallet="<?= $item['no_pallet'] ?>"
                                    data-status="<?= $item['status'] ?>"
                                    data-status_danger="<?= $item['status_danger'] ?>"
                                    data-is_empty="<?= $item['is_empty'] ?>"
                                    data-is_hold="<?= $item['is_hold'] ?>"
                                    data-ex_no_container="<?= $item['ex_no_container'] ?>"
                                    data-description="<?= $item['description'] ?>"
                                    data-whey_number="<?= $item['whey_number'] ?>"
                                >
                                    <?= $item['container_goods'] ?> -
                                    <?= $item['no_reference'] ?> - 
                                    (<?= $item['no_pallet'] ?>) - 
                                    (<?= if_empty($item['whey_number'], '-') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Shifting Record</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable" id="table-shifting">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Container / Goods</th>
                            <th>Last Position</th>
                            <th>New Position</th>
                            <th class="text-center" style="width: 30px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="text-center">No data</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Shifting</button>
        </div>
    </form>
</div>
<?php $this->load->view('shifting/_modal_position_block') ?>
<script id="control-shifting-record-template" type="text/x-custom-template">
    <span class="pull-right">Quantity : {{quantity}}</span><strong>{{container_goods_name}}</strong><br>
    <span class="pull-right">{{customer_name}}</span><span>Ref : {{no_reference}} / {{last_position}}</span>
</script>

<script id="row-shifting-item-template" type="text/x-custom-template">
    <tr class="row-shifting-item">
        <td></td>
        <td>
            <span class="label-container-goods">{{container_goods_name}}</span>
        </td>
        <td>
            <span class="label-last-position">{{last_position}}</span>
        </td>
        <td>
            <div class="input-group">
                <input type="hidden" name="position_blocks[]" id="position_blocks">
                <select class="form-control select2 select2-ajax multi-position"
                        data-url="<?= site_url('position/ajax_get_all') ?>"
                        data-key-id="id" data-key-label="position" data-add-empty-value="NO POSITION"
                        name="new_position[]" id="new_position"
                        data-placeholder="Select position" style="width: 100%" required>
                    <option value=""></option>
                </select>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-edit-block">
                        <i class="ion-compose"></i>
                    </button>
                </span>
            </div>
        </td>
        <td>
            <input type="hidden" value="{{id_customer}}" name="customer[]" id="customer">
            <input type="hidden" value="{{id_booking}}" name="booking[]" id="booking">
            <input type="hidden" value="{{container_goods_type}}" name="container_goods_type[]" id="container_goods_type">
            <input type="hidden" value="{{id_container_goods}}" name="container_goods[]" id="container_goods">
            <input type="hidden" value="{{id_unit}}" name="unit[]" id="unit">
            <input type="hidden" value="{{quantity}}" name="quantity[]" id="quantity">
            <input type="hidden" value="{{tonnage}}" name="tonnage[]" id="tonnage">
            <input type="hidden" value="{{tonnage_gross}}" name="tonnage_gross[]" id="tonnage_gross">
            <input type="hidden" value="{{length}}" name="length[]" id="length">
            <input type="hidden" value="{{width}}" name="width[]" id="width">
            <input type="hidden" value="{{height}}" name="height[]" id="height">
            <input type="hidden" value="{{volume}}" name="volume[]" id="volume">

            <input type="hidden" value="{{seal}}" name="seal[]" id="seal">
            <input type="hidden" value="{{no_pallet}}" name="no_pallet[]" id="no_pallet">
            <input type="hidden" value="{{status}}" name="status[]" id="status">
            <input type="hidden" value="{{status_danger}}" name="status_danger[]" id="status_danger">
            <input type="hidden" value="{{is_empty}}" name="is_empty[]" id="is_empty">
            <input type="hidden" value="{{is_hold}}" name="is_hold[]" id="is_hold">
            <input type="hidden" value="{{no_delivery_order}}" name="no_delivery_order[]" id="no_delivery_order">
            <input type="hidden" value="{{ex_no_container}}" name="ex_no_container[]" id="ex_no_container">
            <input type="hidden" value="{{description}}" name="descriptions[]" id="description">

            <a href="#" class="btn btn-danger btn-delete-shifting-detail">
                <i class="fa ion-trash-a"></i>
            </a>
        </td>
    </tr>
</script>

<script>
    var dateRangePickerSettings = {
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: '<?= (new DateTime())->format('d F Y H:i') ?>',
        locale: {
            format: 'DD MMMM YYYY HH:mm'
        }
    };
</script>

<script src="<?= base_url('assets/app/js/shifting.js?v=7') ?>" defer></script>
