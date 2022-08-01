<?php if(!empty($errorMessages)): ?>
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">XML Data Invalid!</h4>
        <p class="mb10">Please review the warning message below, and do correction.</p>
        <ul style="padding-left: 20px">
            <?php foreach ($errorMessages as $errorMessage): ?>
                <li><?= $errorMessage ?></li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <div>
            <button data-clickable="true" data-url="<?= site_url(uri_string(), false) ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-warning">RELOAD</button>
            <button data-clickable="true" data-url="<?= site_url('booking-import/create') ?>" class="btn btn-primary mr20">UPLOAD NEW XML</button>
            Please re-upload or do correction to your data the click reload
        </div>
    </div>
<?php else: ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Preview Import Booking</h3>
        </div>
        <form action="<?= site_url('booking-import/import-save?') . $_SERVER['QUERY_STRING'] ?>" role="form" method="post" id="form-booking-preview">
            <?= _csrf() ?>
            <div class="box-body">

                <?php $this->load->view('template/_alert') ?>

                <?php if($this->config->item('enable_branch_mode')): ?>
                    <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
                <?php else: ?>
                    <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                        <label for="branch">Branch</label>
                        <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required style="width: 100%">
                            <option value=""></option>
                            <?php foreach (get_customer_branch() as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                    <?= $branch['branch'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <p><?= $booking['category'] ?></p>
                            <input type="hidden" name="category" id="category" value="<?= $booking['category'] ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="booking_type">Booking Type</label>
                            <p><?= $booking['booking_type'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer">Customer</label>
                            <p><?= $booking['customer_name'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('supplier') == '' ?: 'has-error'; ?>">
                            <label for="supplier">Supplier<?= empty($supplier['id']) ? ' &nbsp; <span class="text-danger">(CREATE NEW OR SELECT EXISTING)</span>' : '' ?></label>
                            <input type="hidden" name="supplier_name" value="<?= get_if_exist($supplier, 'name') ?>">
                            <input type="hidden" name="supplier_address" value="<?= get_if_exist($supplier, 'address') ?>">
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_SUPPLIER ?>"
                                    name="supplier" id="supplier"
                                    data-placeholder="Select supplier" style="width: 100%">
                                <option value=""></option>
                                <?php if(!empty($supplier)): ?>
                                    <option value="<?= $supplier['id'] ?>" selected>
                                        <?= $supplier['name'] ?> - <?= $supplier['no_person'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <?= form_error('supplier', '<span class="help-block">', '</span>'); ?>
                            <span class="help-block">
                                If you don't find any supplier
                                <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('booking_date') == '' ?: 'has-error'; ?>">
                            <label for="booking_date">Booking Date</label>
                            <input type="text" class="form-control daterangepicker2" id="booking_date" name="booking_date"
                                   placeholder="Booking date" required
                                   value="<?= set_value('booking_date', date('d F Y H:i')) ?>">
                            <?= form_error('booking_date', '<span class="help-block">', '</span>'); ?>
                            <span class="help-block">Date you booking, by default should be today</span>
                        </div>
                    </div>
                </div>
                <div class="form-group <?= form_error('booking_date') == '' ?: 'has-error'; ?>">
                    <label>Related Document</label>
                    <ul class="list-group" id="document-info-wrapper">
                        <li class="list-group-item disabled">
                            No Upload
                            <a href="<?= site_url('upload/view/' . $booking['id_upload']) ?>" target="_blank">
                                <?= $booking['no_upload'] ?>
                            </a>
                        </li>
                        <?php foreach ($uploadDocuments as $document): ?>
                            <li class="list-group-item data">
                                <strong><?= $document['document_type'] ?></strong> : <?= $document['no_document'] ?>
                                <span class="pull-right"><?= $document['total_file'] ?> files</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>


                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group <?= form_error('no_reference') == '' ?: 'has-error'; ?>">
                            <label for="no_reference">No Reference</label>
                            <input type="text" class="form-control" id="no_reference" name="no_reference"
                                   placeholder="Booking no reference" required readonly
                                   value="<?= set_value('no_reference', $booking['no_reference']) ?>">
                            <?= form_error('no_reference', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group <?= form_error('reference_date') == '' ?: 'has-error'; ?>">
                            <label for="reference_date">Reference Date</label>
                            <input type="text" class="form-control datepicker" id="reference_date" name="reference_date"
                                   placeholder="Reference document date" required readonly
                                   value="<?= set_value('reference_date', format_date($booking['reference_date'], 'd F Y')) ?>">
                            <?= form_error('reference_date', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group <?= form_error('vessel') == '' ?: 'has-error'; ?>">
                            <label for="vessel">Vessel</label>
                            <input type="text" class="form-control" id="vessel" name="vessel"
                                   placeholder="Vessel" readonly
                                   value="<?= set_value('vessel', $booking['vessel']) ?>">
                            <?= form_error('vessel', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group <?= form_error('voyage') == '' ?: 'has-error'; ?>">
                            <label for="voyage">Voyage</label>
                            <input type="text" class="form-control" id="voyage" name="voyage"
                                   placeholder="Voyage" readonly
                                   value="<?= set_value('voyage', $booking['voyage']) ?>">
                            <?= form_error('voyage', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group <?= form_error('document_status') == '' ?: 'has-error'; ?>">
                            <label for="document_status">Document Status</label>
                            <select class="form-control select2" name="document_status" id="document_status"
                                    data-placeholder="Booking document status" required style="width: 100%">
                                <option value=""></option>
                                <?php foreach (BookingModel::DOCUMENT_STATUSES as $status): ?>
                                    <option value="<?= $status ?>" <?= set_select('document_status', $status, $status == 'LAIN - LAIN') ?>>
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('document_status', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                    <label for="description">Booking Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Booking description"
                              maxlength="500" rows="2"><?= set_value('description', $booking['description']) ?></textarea>
                    <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                </div>

                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">Extension data</h3>
                    </div>
                    <div class="box-body" id="extension-wrapper">
                        <?php if (empty($extensionFields)): ?>
                            <p class="text-muted">Extension booking fields lies here</p>
                        <?php else: ?>
                            <?= $extensions ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="booking-reference-wrapper" <?= $booking['category'] == 'INBOUND' ? 'style="display: none"' : '' ?>>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Booking Reference</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group <?= form_error('booking_in') == '' ?: 'has-error'; ?>">
                                <label for="booking_in">Booking In</label>
                                <p><?= $bookingIn['no_booking'] ?> (<?= $bookingIn['no_reference'] ?>)</p>
                                <input type="hidden" name="booking_in" value="<?= $bookingIn['id'] ?>">
                                <?= form_error('booking_in', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">Total Weight</h3>
                    </div>
                    <div class="box-body" id="weight-wrapper">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('netto') == '' ?: 'has-error'; ?>">
                                <label for="netto">Total Weight</label>
                                <input type="text" class="form-control numeric" id="netto" name="netto"
                                    placeholder="netto"
                                    value="<?= set_value('netto', numerical($booking['netto'], 3, true)) ?>">
                                <?= form_error('netto', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('bruto') == '' ?: 'has-error'; ?>">
                                <label for="bruto">Total Gross Weight</label>
                                <input type="text" class="form-control numeric" id="bruto" name="bruto"
                                    placeholder="bruto"
                                    value="<?= set_value('bruto', numerical($booking['bruto'], 3, true)) ?>">
                                <?= form_error('bruto', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="input-detail-wrapper" class="tally-editor editor-<?= isset($formId) ? $formId : '1' ?>" data-id="<?= isset($formId) ? $formId : '1' ?>" data-stock-url="<?= site_url('booking-import/get-stock-import?file=' . get_url_param('file')) ?>" data-with-detail="1">
                    <div class="input-editor">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title"><span class="hidden-xs">Data </span>Container</h3><br>
                                <span>Matching <strong>no container</strong> with master</span>
                            </div>
                            <div class="box-body">
                                <div class="table-editor-wrapper">
                                    <div class="table-editor-scroller">
                                        <table class="table table-striped table-bordered responsive no-datatable no-wrap" id="table-container">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Shipping Line</th>
                                                <th style="width:175px;">Container</th>
                                                <th>Size</th>
                                                <th>Type</th>
                                                <th>Seal</th>
                                                <th>Position</th>
                                                <th>Payload (M<sup>3</sup>)</th>
                                                <th class="sticky-col-right">
                                                    Action
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $containers = set_value('containers', $containers); $no = 1; $index = 0 ?>
                                            <?php foreach ($containers as $container): ?>
                                                <tr class="row-header<?= $booking['category'] == 'OUTBOUND' ? ' row-stock' : '' ?><?= empty($container['id_container']) ? ' danger' : '' ?>">
                                                    <td><?= $no++ ?></td>
                                                    <td id="shipping-line-label">
                                                        <?php if(empty($container['id_container']) && empty($container['shipping_line'])): ?>
                                                            <span class="text-danger">CREATE NEW</span>
                                                        <?php else: ?>
                                                            <?= $container['shipping_line'] ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td id="container-label"><?= if_empty($container['no_container'], '-') ?></td>
                                                    <td id="container-size-label"><?= if_empty($container['size'], '-') ?></td>
                                                    <td id="container-type-label"><?= if_empty($container['type'], '-') ?></td>
                                                    <td id="seal-label"><?= if_empty($container['seal'], '-') ?></td>
                                                    <td id="position-label"><?= if_empty($container['position'], '-') ?></td>
                                                    <td id="volume-payload-label">
                                                        <?= isset($container['volume_payload']) ? (numerical($container['volume_payload'], 3, true)) : 0 ?>
                                                        (<?= isset($container['length_payload']) ? numerical($container['length_payload'], 3, true) : 0?>
                                                        x <?= isset($container['width_payload']) ? numerical($container['width_payload'], 3, true) : 0 ?>
                                                        x <?= isset($container['height_payload']) ?numerical($container['height_payload'], 3, true) : 0 ?>)
                                                    </td>
                                                    <td class="sticky-col-right">
                                                        <input type="hidden" name="containers[<?= $index ?>][id_shipping_line]" id="id_shipping_line" value="<?= $container['id_shipping_line'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][shipping_line]" id="shipping_line" value="<?= $container['shipping_line'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][id_container]" id="id_container" value="<?= $container['id_container'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][no_container]" id="no_container" value="<?= $container['no_container'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][size]" id="size" value="<?= $container['size'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][type]" id="type" value="<?= $container['type'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][seal]" id="seal" value="<?= $container['seal'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][position]" id="position" value="<?= $container['position'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][id_position]" id="id_position" value="<?= $container['id_position'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][id_position_blocks]" id="id_position_blocks" value="<?= get_if_exist($container, 'id_position_blocks') ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][is_hold]" id="is_hold" value="<?= $container['is_hold'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][is_empty]" id="is_empty" value="<?= $container['is_empty'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][status]" id="status" value="<?= $container['status'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][status_danger]" id="status_danger" value="<?= $container['status_danger'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][length_payload]" id="length_payload" value="<?= isset($container['length_payload']) ? $container['length_payload'] : 0 ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][width_payload]" id="width_payload" value="<?= isset($container['width_payload']) ? $container['width_payload'] : 0 ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][height_payload]" id="height_payload" value="<?= isset($container['height_payload']) ? $container['height_payload'] : 0 ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][volume_payload]" id="volume_payload" value="<?= isset($container['volume_payload']) ? $container['volume_payload'] : 0 ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][description]" id="description" value="<?= $container['description'] ?>">
                                                        <?php if($booking['category'] == 'INBOUND'): ?>
                                                            <button class="btn btn-sm btn-primary btn-add-detail-from-list" type="button">
                                                                <i class="fa ion-plus"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-primary btn-edit-container" type="button">
                                                                <i class="fa ion-compose"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-success btn-view-container" type="button">
                                                                <i class="fa ion-search"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php $index++ ?>
                                            <?php endforeach; ?>
                                            <?php if(empty($containers)): ?>
                                                <tr class="row-placeholder">
                                                    <td colspan="8">No container data</td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <span class="label label-danger">RED ROWS</span>
                                                    &nbsp; indicate the container has not related any master yet (EDIT TO COMPLETE DATA)
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="box-header">
                            <div class="pull-right">
                                <button class="btn btn-sm btn-danger btn-clear-all-goods" type="button">
                                    CLEAR ALL
                                </button>
                                <button class="btn btn-sm btn-success btn-add-goods" type="button" data-source="BOTH">
                                    <i class="fa ion-plus"></i> ADD
                                </button>
                            </div>
                            <h3 class="box-title"><span class="hidden-xs">Data </span>Goods</h3><br>
                            <span>Matching <strong>good number</strong> with master</span>
                        </div>
                        <div class="box-body">
                            <div class="table-editor-wrapper">
                                <div class="table-editor-scroller">
                                    <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods" data-with-detail="1">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Whey Number</th>
                                            <th>Ex Container</th>
                                            <th>Unit Weight (Kg)</th>
                                            <th>Total Weight (Kg)</th>
                                            <th>Unit Gross (Kg)</th>
                                            <th>Total Gross (Kg)</th>
                                            <th>Unit Volume (M<sup>3</sup>)</th>
                                            <th>Total Volume (M<sup>3</sup>)</th>
                                            <th>Position</th>
                                            <th>Pallet</th>
                                            <th>Is Hold</th>
                                            <th>Status</th>
                                            <th>Danger</th>
                                            <th class="text-right sticky-col-right">
                                                Action
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $goods = set_value('goods', $goods); $no = 1; $index = 0 ?>
                                        <?php foreach ($goods as $item): ?>
                                            <tr class="row-header row-stock<?= empty($item['id_goods']) ? ' danger' : '' ?>" data-id="<?= (floor(rand(100, 999))) . uniqid() ?>">
                                                <td><?= $no++ ?></td>
                                                <td id="goods-label">
                                                    <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>" target="_blank">
                                                        <?= $item['goods_name'] ?>
                                                    </a>
                                                </td>
                                                <td id="quantity-label"><?= numerical($item['quantity'], 3, true) ?></td>
                                                <td id="unit-label"><?= if_empty($item['unit'], '-') ?></td>
                                                <td id="whey-number-label"><?= if_empty($item['whey_number'], '-') ?></td>
                                                <td id="ex-no-container-label"><?= if_empty($item['ex_no_container'], '-') ?></td>
                                                <td id="unit-weight-label"><?= numerical($item['unit_weight'], 3, true) ?></td>
                                                <td id="total-weight-label"><?= numerical($item['unit_weight'] * $item['quantity'], 3, true) ?></td>
                                                <td id="unit-gross-weight-label"><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                                                <td id="total-gross-weight-label"><?= numerical($item['unit_gross_weight'] * $item['quantity'], 3, true) ?></td>
                                                <td id="unit-volume-label">
                                                    <?= numerical($item['unit_volume']) ?>
                                                    (<?= numerical($item['unit_length'], 3, true) ?>
                                                    x <?= numerical($item['unit_width'], 3, true) ?>
                                                    x <?= numerical($item['unit_height'], 3, true) ?>)
                                                </td>
                                                <td id="total-volume-label"><?= numerical($item['unit_volume'] * $item['quantity'], 3, true) ?></td>
                                                <td id="position-label"><?= if_empty($item['position'], '-') ?></td>
                                                <td id="no-pallet-label"><?= if_empty($item['no_pallet'], '-') ?></td>
                                                <td id="is-hold-label"><?= $item['is_hold'] ? 'YES' : 'NO' ?></td>
                                                <td id="status-label"><?= $item['status'] ?></td>
                                                <td id="status-danger-label"><?= $item['status_danger'] ?></td>
                                                <td class="sticky-col-right">
                                                    <input type="hidden" id="goods-data" value="<?= rawurlencode(json_encode($item)) ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][id_goods]" id="id_goods" value="<?= $item['id_goods'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][no_goods]" id="no_goods" value="<?= $item['no_goods'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][parent_no_goods]" id="parent_no_goods" value="">
                                                    <input type="hidden" name="goods[<?= $index ?>][quantity_child_goods]" id="quantity_child_goods" value="">
                                                    <input type="hidden" name="goods[<?= $index ?>][goods_name]" id="goods_name" value="<?= $item['goods_name'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][type_goods]" id="type_goods" value="<?= $item['type_goods'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][no_hs]" id="no_hs" value="<?= $item['no_hs'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][quantity]" id="quantity" value="<?= $item['quantity'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][id_unit]" id="id_unit" value="<?= $item['id_unit'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit]" id="unit" value="<?= $item['unit'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][whey_number]" id="whey_number" value="<?= $item['whey_number'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_weight]" id="unit_weight" value="<?= $item['unit_weight'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_gross_weight]" id="unit_gross_weight" value="<?= $item['unit_gross_weight'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_length]" id="unit_length" value="<?= $item['unit_length'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_width]" id="unit_width" value="<?= $item['unit_width'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_height]" id="unit_height" value="<?= $item['unit_height'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][unit_volume]" id="unit_volume" value="<?= $item['unit_volume'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][no_pallet]" id="no_pallet" value="<?= $item['no_pallet'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][position]" id="position" value="<?= $item['position'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][id_position]" id="id_position" value="<?= $item['id_position'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][id_position_blocks]" id="id_position_blocks" value="<?= get_if_exist($item, 'id_position_blocks') ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][is_hold]" id="is_hold" value="<?= $item['is_hold'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][status]" id="status" value="<?= $item['status'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][status_danger]" id="status_danger" value="<?= $item['status_danger'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][ex_no_container]" id="ex_no_container" value="<?= $item['ex_no_container'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][description]" id="description" value="<?= $item['description'] ?>">
                                                    <input type="hidden" name="goods[<?= $index ?>][id_reference]" id="id_reference" value="<?= $item['id_goods'] ?>">
                                                    <?php if($booking['category'] == 'INBOUND'): ?>
                                                        <button class="btn btn-sm btn-primary btn-add-detail" type="button">
                                                            <i class="fa ion-plus"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                                                            <i class="fa ion-compose"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-success btn-view-goods" type="button">
                                                            <i class="fa ion-search"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php $index++ ?>
                                        <?php endforeach; ?>
                                        <?php if(empty($goods)): ?>
                                            <tr class="row-placeholder">
                                                <td colspan="13">No goods data</td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="13">
                                                <span class="label label-danger">RED ROWS</span>
                                                &nbsp; indicate the item has not related any master yet (EDIT TO COMPLETE DATA)
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="box-footer clearfix">
                <a href="javascript:history.back()" class="btn btn-primary pull-left">
                    Back
                </a>
                <button type="submit" class="btn btn-primary pull-right">
                    Import Now
                </button>
            </div>
        </form>
    </div>

    <?php $this->load->view('booking_import/_modal_container_input') ?>
    <?php $this->load->view('booking_import/_modal_container_view') ?>
    <?php //$this->load->view('booking_import/_modal_goods_input') ?>
    <?php $this->load->view('booking_import/_modal_goods_view') ?>
    <?php $this->load->view('booking_import/_modal_goods_table_list') ?>
    <?php //$this->load->view('booking_import/_modal_goods_parent') ?>
    <?php $this->load->view('tally/_modal_goods_input') ?>
    <?php $this->load->view('tally/_modal_select_position') ?>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-info">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Import Info</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0" id="message-info"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<script src="<?= base_url('assets/app/js/tally_editor.js?v=17') ?>" defer></script>
<script src="<?= base_url('assets/app/js/booking-import.js?v=3') ?>" defer></script>
