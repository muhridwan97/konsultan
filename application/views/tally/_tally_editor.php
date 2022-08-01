<div class="tally-editor editor-<?= isset($formId) ? $formId : '1' ?>" data-id="<?= isset($formId) ? $formId : '1' ?>" data-stock-url="<?= isset($stockUrl) ? $stockUrl : '' ?>">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><span class="hidden-xs">Data </span>Container</h3>
            <div class="pull-right">
                <input type="hidden" id="first_overtime" value="<?= isset($Overtime['first_overtime']) ? $Overtime['first_overtime'] : false ?>">
                <input type="hidden" id="second_overtime" value="<?= isset($Overtime['second_overtime']) ? $Overtime['second_overtime'] : false ?>">
                <input type="hidden" id="handling_type" value="<?= isset($workOrder['handling_type']) ? $workOrder['handling_type'] : false ?>">
                <input type="hidden" id="multiplier_goods" value="<?= isset($workOrder['multiplier_goods']) ? $workOrder['multiplier_goods'] : false ?>">
                <input type="hidden" id="source_ex_no_container" value="<?= isset($workOrder['source_ex_no_container']) ? $workOrder['source_ex_no_container'] : '' ?>">

                <?php if (isset($allowAddContainer) ? $allowAddContainer : true): ?>
                    <button class="btn btn-sm btn-success btn-add-container" type="button" data-source="<?= isset($inputSource) ? $inputSource : (isset($inputSourceContainer) ? $inputSourceContainer : 'BOTH') ?>">
                        <i class="fa ion-plus"></i> ADD
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="box-body">
            <div class="table-editor-wrapper">
                <div class="table-editor-scroller">
                    <table class="table table-condensed table-striped table-bordered responsive no-datatable no-wrap" id="table-container" data-with-detail="<?= !isset($withDetailContainer) || $withDetailContainer ?>">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th style="width:175px;">Container</th>
                            <th>Seal</th>
                            <th>Position</th>
                            <th>Payload (M<sup>3</sup>)</th>
                            <th>Is Empty</th>
                            <th>Is Hold</th>
                            <th>Status</th>
                            <th >Danger</th>
                            <?php if(isset($workOrderId)): ?>
                                <th>Overtime Status</th>
                                <th>Overtime Date</th>
                            <?php endif; ?>
                            <th class="sticky-col-right" style="width: 80px">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $containers = isset($containers) ? $containers : []; $no = 1; $index = 0 ?>
                        <?php foreach ($containers as $container): ?>
                            <tr class="row-header<?= ((isset($inputSource) && $inputSource == 'STOCK') || (isset($inputSourceContainer) && $inputSourceContainer == 'STOCK')) ? ' row-stock' : '' ?>">
                                <td><?= $no++ ?></td>
                                <td id="container-label">
                                    <a href="<?= site_url('container/view/' . $container['id_container']) ?>" target="_blank">
                                        <?= $container['no_container'] ?> - <?= $container['size'] ?>
                                    </a>
                                </td>
                                <td id="seal-label"><?= if_empty($container['seal'], '-') ?></td>
                                <td id="position-label"><?= if_empty($container['position'], '-') ?></td>
                                <td id="volume-payload-label">
                                    <?= isset($container['volume_payload']) ? (numerical($container['volume_payload'], 3, true)) : 0 ?>
                                    (<?= isset($container['length_payload']) ? numerical($container['length_payload'], 3, true) : 0?>
                                    x <?= isset($container['width_payload']) ? numerical($container['width_payload'], 3, true) : 0 ?>
                                    x <?= isset($container['height_payload']) ?numerical($container['height_payload'], 3, true) : 0 ?>)
                                </td>
                                <td id="is-empty-label"><?= $container['is_empty'] ? 'EMPTY' : 'FULL' ?></td>
                                <td id="is-hold-label"><?= $container['is_hold'] ? 'YES' : 'NO' ?></td>
                                <td id="status-label"><?= $container['status'] ?></td>
                                <td id="status-danger-label"><?= $container['status_danger'] ?></td>
                                <?php if(isset($workOrderId)): ?>
                                <td id="status-overtime-label">
                                    <?= get_if_exist($container, 'overtime_status','-') ?>
                                </td>
                                <td id="date-overtime-label">
                                    <?= isset($container['overtime_date']) ? format_date($container['overtime_date'],'Y-m-d H:i:s') : '-' ?>
                                </td>
                                <?php endif; ?>
                                <td class="sticky-col-right">
                                    <input type="hidden" name="containers[<?= $index ?>][id_reference]" id="id_reference" value="<?= get_if_exist($container, 'id_reference', '') ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][id_booking_reference]" id="id_booking_reference" value="<?= get_if_exist($container, 'id_booking_reference', '') ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][id_container]" id="id_container" value="<?= $container['id_container'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][seal]" id="seal" value="<?= $container['seal'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][id_position]" id="id_position" value="<?= $container['id_position'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][id_position_blocks]" id="id_position_blocks" value="<?= get_if_exist($container, 'id_position_blocks') ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][is_hold]" id="is_hold" value="<?= $container['is_hold'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][is_empty]" id="is_empty" value="<?= $container['is_empty'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][status]" id="status" value="<?= $container['status'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][status_danger]" id="status_danger" value="<?= $container['status_danger'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][description]" id="description" value="<?= $container['description'] ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][length_payload]" id="length_payload" value="<?= isset($container['length_payload']) ? $container['length_payload'] : 0 ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][width_payload]" id="width_payload" value="<?= isset($container['width_payload']) ? $container['width_payload'] : 0 ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][height_payload]" id="height_payload" value="<?= isset($container['height_payload']) ? $container['height_payload'] : 0 ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][volume_payload]" id="volume_payload" value="<?= isset($container['volume_payload']) ? $container['volume_payload'] : 0 ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][no_container_text]" id="no_container_text" value="<?= isset($container['no_container']) ? $container['no_container'] : '-' ?>">
                                    <?php if(isset($workOrderId)): ?>
                                    <input type="hidden" name="containers[<?= $index ?>][overtime_status]" id="overtime_status" value="<?= get_if_exist($container, 'overtime_status','0') ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][workOrderContainerId]" id="workOrderContainerId" value="<?= isset($container['id_work_order']) ? $container['id'] : ''  ?>">
                                    <input type="hidden" name="containers[<?= $index ?>][overtime_date]" id="overtime_date" value="<?= get_if_exist($container, 'overtime_date')  ?>">
                                    <input type="hidden" name='overtime_status_exists' id="overtime_status_exists" value="<?= isset($container['overtime_status']) ? $container['overtime_status'] : 'not_exists'  ?>">
                                    <?php endif; ?>
                                    <?php if(isset($inputSource) && $inputSource == 'STOCK' || isset($inputSourceContainer) && $inputSourceContainer == 'STOCK'): ?>
                                        <?php if(!isset($withDetailContainer) || $withDetailContainer): ?>
                                            <button class="btn btn-sm btn-primary btn-add-detail" type="button">
                                                <i class="fa ion-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if(isset($detailHandling) && (isset($bookingStocks) && !$bookingStocks) && (isset($getBookingById) && $getBookingById['category'] == "OUTBOUND")): ?>
                                            <button class="btn btn-sm btn-primary btn-edit-container" type="button">
                                                <i class="fa ion-compose"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger btn-remove-container" type="button">
                                            <i class="ion-trash-b"></i>
                                        </button>
                                    <?php else: ?>
                                        <?php if(!isset($withDetailContainer) || $withDetailContainer): ?>
                                            <button class="btn btn-sm btn-primary btn-add-detail" type="button">
                                                <i class="fa ion-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-primary btn-edit-container" type="button">
                                            <i class="fa ion-compose"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                                <tr class="row-detail">
                                    <td></td>
                                    <td colspan="11">
                                        <table class="table table-condensed table-bordered responsive no-datatable">
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
                                                <?php if(isset($workOrderId)): ?>
                                                    <th>Overtime Status</th>
                                                    <th>Overtime Date</th>
                                                <?php endif; ?>
                                                <th class="sticky-col-right">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; $innerIndex = 0 ?>
                                            <?php foreach ($container['goods'] as $item): ?>
                                                <tr class="row-goods<?= isset($inputSource) && $inputSource == 'STOCK' ? ' row-stock' : '' ?>">
                                                    <td><?= $innerNo++ ?></td>
                                                    <td id="goods-label">
                                                        <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>" target="_blank">
                                                            <?= $item['goods_name'] ?>
                                                        </a>
                                                    </td>
                                                    <?php if(isset($handling_type)){ 
                                                        if ($handling_type == "STRIPPING") { 
                                                            if (isset($container['is_work_order'])) {
                                                                if (!$container['is_work_order']) { 
                                                                    $item['quantity']='';?>
                                                                    <td id="quantity-label"></td>
                                                                <?php } 
                                                                else { ?>
                                                                    <td id="quantity-label"><?= numerical($item['quantity'], 3, true) ?></td>
                                                                <?php }
                                                            }else{ ?>
                                                            <td id="quantity-label"><?= numerical($item['quantity'], 3, true) ?></td>
                                                            <?php } ?>
                                                    <?php } else { ?>
                                                        <td id="quantity-label"><?= numerical($item['quantity'], 3, true) ?></td>
                                                    <?php }
                                                        }else{ ?>
                                                        <td id="quantity-label"><?= numerical($item['quantity'], 3, true) ?></td>
                                                    <?php } ?>
                                                    <td id="unit-label"><?= if_empty($item['unit'], '-') ?></td>
                                                    <td id="whey-number-label"><?= if_empty($item['whey_number'], '-') ?></td>
                                                    <td id="ex-no-container-label"><?= if_empty($item['ex_no_container'], '-') ?></td>
                                                    <td id="unit-weight-label"><?= numerical($item['unit_weight'], 3, true) ?></td>
                                                    <td id="total-weight-label"><?= numerical($item['total_weight'], 3, true) ?></td>
                                                    <td id="unit-gross-weight-label"><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                                                    <td id="total-gross-weight-label"><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                                                    <td id="unit-volume-label">
                                                        <?= numerical($item['unit_volume']) ?>
                                                        (<?= numerical($item['unit_length'], 3, true) ?>
                                                        x<?= numerical($item['unit_width'], 3, true) ?>
                                                        x<?= numerical($item['unit_height'], 3, true) ?>)
                                                    </td>
                                                    <td id="total-volume-label"><?= numerical($item['total_volume']) ?></td>
                                                    <td id="position-label"><?= if_empty($item['position'], '-') ?></td>
                                                    <td id="no-pallet-label"><?= if_empty($item['no_pallet'], '-') ?></td>
                                                    <td id="is-hold-label"><?= $item['is_hold'] ? 'YES' : 'NO' ?></td>
                                                    <td id="status-label"><?= $item['status'] ?></td>
                                                    <td id="status-danger-label"><?= $item['status_danger'] ?></td>
                                                    <?php if(isset($workOrderId)): ?>
                                                        <td id="status-overtime-label">
                                                            <?= get_if_exist($item, 'overtime_status', '-') ?>
                                                        </td>
                                                        <td id="date-overtime-label">
                                                            <?= isset($item['overtime_date']) ? format_date($item['overtime_date'],'Y-m-d H:i:s') : '-' ?>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="sticky-col-right">
                                                        <input type="hidden" id="goods-data" value="<?= rawurlencode(json_encode($item)) ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_booking_reference]" id="id_booking_reference" value="<?= $item['id_booking_reference'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_goods]" id="id_goods" value="<?= $item['id_goods'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][quantity]" id="quantity" value="<?= $item['quantity'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_unit]" id="id_unit" value="<?= $item['id_unit'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][whey_number]" id="whey_number" value="<?= $item['whey_number'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_weight]" id="unit_weight" value="<?= $item['unit_weight'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_gross_weight]" id="unit_gross_weight" value="<?= $item['unit_gross_weight'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_length]" id="unit_length" value="<?= $item['unit_length'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_width]" id="unit_width" value="<?= $item['unit_width'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_height]" id="unit_height" value="<?= $item['unit_height'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][unit_volume]" id="unit_volume" value="<?= $item['unit_volume'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][no_pallet]" id="no_pallet" value="<?= $item['no_pallet'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_position]" id="id_position" value="<?= $item['id_position'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_position_blocks]" id="id_position_blocks" value="<?= get_if_exist($item, 'id_position_blocks') ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][is_hold]" id="is_hold" value="<?= $item['is_hold'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][status]" id="status" value="<?= $item['status'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][status_danger]" id="status_danger" value="<?= $item['status_danger'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][ex_no_container]" id="ex_no_container" value="<?= $item['ex_no_container'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][description]" id="description" value="<?= $item['description'] ?>">
                                                        <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][id_reference]" id="id_reference" value="<?= ($JobPage ?? '') == 'edit_job' ? $item['no_pallet'] : get_if_exist($item, 'id', get_if_exist($item, 'id_work_order_goods')) ?>">
                                                        <?php if(isset($workOrderId)): ?>
                                                            <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][overtime_status]" id="overtime_status" value="<?= get_if_exist($item, 'overtime_status','0') ?>">
                                                            <input type="hidden" name="containers[<?= $index ?>][goods][<?= $innerIndex ?>][overtime_date]" id="overtime_date" value="<?= get_if_exist($item, 'overtime_date', '')  ?>">
                                                            <input type="hidden" name='containers[<?= $index ?>][goods][<?= $innerIndex ?>][workOrderGoodsId]' id="workOrderGoodsId" value="<?= isset($item['id_work_order']) ? $item['id'] : ''  ?>">
                                                            <input type="hidden" name='containers[<?= $index ?>][goods][<?= $innerIndex ?>][temp_photos]' id="temp_photos" value="">
                                                            <input type="hidden" name='containers[<?= $index ?>][goods][<?= $innerIndex ?>][temp_photo_descriptions]' id="temp_photo_descriptions" value="">
                                                            <input type="hidden" name='overtime_status_exists' id="overtime_status_exists" value="<?= isset($item['overtime_status']) ? $item['overtime_status'] : 'not_exists'  ?>">
                                                        <?php endif; ?>
                                                        <?php if(isset($inputSource) && $inputSource == 'STOCK'): ?>
                                                            <?php if(isset($detailHandling) && (isset($bookingStocks) && !$bookingStocks) && (isset($getBookingById) && $getBookingById['category'] == "OUTBOUND")): ?>
                                                                <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                                                                    <i class="fa ion-compose"></i>
                                                                </button>
                                                            <?php endif?>
                                                            <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                                                                <i class="ion-trash-b"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                                                                <i class="fa ion-compose"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if(isset($workOrderId)): ?>
                                                            <button class="btn btn-sm btn-info btn-photo-goods" type="button">
                                                                <i class="fa ion-image"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php $innerIndex++ ?>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <button class="btn btn-sm btn-primary btn-block btn-add-detail visible-xs" type="button">
                                            ADD ITEM GOODS
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php $index++ ?>
                        <?php endforeach; ?>
                        <?php if(empty($containers)): ?>
                            <tr class="row-placeholder">
                                <td colspan="12">No container data</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if(!isset($allowIn) && ( (isset($handling_type) && $handling_type != "STRIPPING") || !isset($handling_type))): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><span class="hidden-xs">Data </span>Goods</h3>
            <div class="pull-right">
                <button class="btn btn-sm btn-success btn-add-goods" type="button" data-source="<?= isset($inputSource) ? $inputSource : 'BOTH' ?>">
                    <i class="fa ion-plus"></i> ADD
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-editor-wrapper">
                <div class="table-editor-scroller">
                    <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods" data-with-detail="<?= isset($withDetailGoods) ? $withDetailGoods : false ?>">
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
                            <?php if(isset($workOrderId)): ?>
                            <th>Overtime Status</th>
                            <th>Overtime Date</th>
                            <?php endif; ?>
                            <th class="sticky-col-right">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $goods = isset($goods) ? $goods : []; $no = 1; $index = 0 ?>
                        <?php foreach ($goods as $item): ?>
                            <tr class="row-header<?= isset($inputSource) && $inputSource == 'STOCK' ? ' row-stock' : '' ?>">
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
                                <td id="total-weight-label"><?= numerical($item['total_weight'], 3, true) ?></td>
                                <td id="unit-gross-weight-label"><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                                <td id="total-gross-weight-label"><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                                <td id="unit-volume-label">
                                    <?= numerical($item['unit_volume']) ?>
                                    (<?= numerical($item['unit_length'], 3, true) ?>
                                    x <?= numerical($item['unit_width'], 3, true) ?>
                                    x <?= numerical($item['unit_height'], 3, true) ?>)
                                </td>
                                <td id="total-volume-label"><?= numerical($item['total_volume']) ?></td>
                                <td id="position-label"><?= if_empty($item['position'], '-') ?></td>
                                <td id="no-pallet-label"><?= if_empty($item['no_pallet'], '-') ?></td>
                                <td id="is-hold-label"><?= $item['is_hold'] ? 'YES' : 'NO' ?></td>
                                <td id="status-label"><?= $item['status'] ?></td>
                                <td id="status-danger-label"><?= $item['status_danger'] ?></td>
                                <?php if(isset($workOrderId)): ?>
                                <td id="status-overtime-label">
                                    <?= get_if_exist($item, 'overtime_status', '-') ?>
                                </td>
                                <td id="date-overtime-label">
                                    <?= isset($item['overtime_date']) ? format_date($item['overtime_date'],'Y-m-d H:i:s') : '-' ?>
                                </td>
                                <?php endif; ?>
                                <td class="sticky-col-right">
                                    <input type="hidden" id="goods-data" value="<?= rawurlencode(json_encode($item)) ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_booking_reference]" id="id_booking_reference" value="<?= $item['id_booking_reference'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_goods]" id="id_goods" value="<?= $item['id_goods'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][quantity]" id="quantity" value="<?= $item['quantity'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_unit]" id="id_unit" value="<?= $item['id_unit'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][whey_number]" id="whey_number" value="<?= $item['whey_number'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_weight]" id="unit_weight" value="<?= $item['unit_weight'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_gross_weight]" id="unit_gross_weight" value="<?= $item['unit_gross_weight'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_length]" id="unit_length" value="<?= $item['unit_length'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_width]" id="unit_width" value="<?= $item['unit_width'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_height]" id="unit_height" value="<?= $item['unit_height'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][unit_volume]" id="unit_volume" value="<?= $item['unit_volume'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_position]" id="id_position" value="<?= $item['id_position'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_position_blocks]" id="id_position_blocks" value="<?= get_if_exist($item, 'id_position_blocks') ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][no_pallet]" id="no_pallet" value="<?= $item['no_pallet'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][is_hold]" id="is_hold" value="<?= $item['is_hold'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][status]" id="status" value="<?= $item['status'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][status_danger]" id="status_danger" value="<?= $item['status_danger'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][ex_no_container]" id="ex_no_container" value="<?= $item['ex_no_container'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][description]" id="description" value="<?= $item['description'] ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][id_reference]" id="id_reference" value="<?= ($JobPage ?? '') == 'edit_job' ? $item['no_pallet'] : get_if_exist($item, 'id', get_if_exist($item, 'id_work_order_goods')) ?>">
                                    <?php if(isset($workOrderId)): ?>
                                    <input type="hidden" name="goods[<?= $index ?>][overtime_status]" id="overtime_status" value="<?= get_if_exist($item, 'overtime_status','0') ?>">
                                    <input type="hidden" name="goods[<?= $index ?>][overtime_date]" id="overtime_date" value="<?= get_if_exist($item, 'overtime_date','')  ?>">
                                    <input type="hidden" name='goods[<?= $index ?>][workOrderGoodsId]' id="workOrderGoodsId" value="<?= isset($item['id_work_order']) ? $item['id'] : ''  ?>">
                                    <input type="hidden" name='goods[<?= $index ?>][temp_photos]' id="temp_photos" value="">
                                    <input type="hidden" name='goods[<?= $index ?>][temp_photo_descriptions]' id="temp_photo_descriptions" value="">
                                    <input type="hidden" name='overtime_status_exists' id="overtime_status_exists" value="<?= isset($item['overtime_status']) ? $item['overtime_status'] : 'not_exists'  ?>">
                                    <?php endif; ?>
                                    <?php if(isset($inputSource) && $inputSource == 'STOCK'): ?>
                                        <?php if(isset($detailHandling) && (isset($bookingStocks) && !$bookingStocks) && (isset($getBookingById) && $getBookingById['category'] == "OUTBOUND")): ?>
                                        <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                                            <i class="fa ion-compose"></i>
                                        </button>
                                        <?php endif?>
                                        <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                                            <i class="ion-trash-b"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary btn-edit-goods" type="button">
                                            <i class="fa ion-compose"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if(isset($workOrderId)): ?>
                                        <button class="btn btn-sm btn-info btn-photo-goods" type="button">
                                            <i class="fa ion-image"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $index++ ?>
                        <?php endforeach; ?>
                        <?php if(empty($goods)): ?>
                            <tr class="row-placeholder">
                                <td colspan="19">No goods data</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>

<script src="<?= base_url('assets/app/js/tally_editor.js?v=28') ?>" data-editor="<?= isset($formId) ? $formId : '1' ?>" defer></script>