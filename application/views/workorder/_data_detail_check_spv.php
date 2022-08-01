<?php if(!empty($containers)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Containers</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Container</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Length Payload (M)</th>
                        <th>Width Payload (M)</th>
                        <th>Height Payload (M)</th>
                        <th>Volume Payload (M<sup>3</sup>)</th>
                        <th>Seal</th>
                        <th>Position</th>
                        <th>Is Empty</th>
                        <th>Is Hold</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Overtime</th>
                        <th>Overtime Date</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($containers as $container): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('container/view/' . $container['id_container']) ?>">
                                    <?= $container['no_container'] ?>
                                </a>
                            </td>
                            <td><?= $container['type'] ?></td>
                            <td><?= $container['size'] ?></td>
                            <td><?= numerical($container['length_payload'], 3, true) ?> M</td>
                            <td><?= numerical($container['width_payload'], 3, true) ?> M</td>
                            <td><?= numerical($container['height_payload'], 3, true) ?> M</td>
                            <td><?= numerical($container['volume_payload'], 3, true) ?> M<sup>3</sup></td>
                            <td><?= if_empty($container['seal'], '-') ?></td>
                            <td>
                                <?= if_empty($container['position'], '-') ?>
                                <small class="text-muted" style="display: block">
                                    <?= str_replace(',', ', ', $container['position_blocks']) ?>
                                </small>
                            </td>
                            <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                                <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                            </td>
                            <td class="<?= $container['is_hold'] ? 'text-danger' :'' ?>">
                                <?= $container['is_hold'] ? 'Yes' : 'No' ?>
                            </td>
                            <td><?= if_empty($container['status'], 'No Status') ?></td>
                            <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                <?= $container['status_danger'] ?>
                            </td>
                            <td><?= if_empty($container['overtime_status'], '-') ?></td>
                            <td><?= if_empty(format_date($container['overtime_date'], 'd F Y H:i:s'), '-') ?></td>
                            <td><?= if_empty($container['description'], 'No description') ?></td>
                        </tr>
                        <?php
                        $containerGoodsExist = key_exists('goods', $container) && !empty($container['goods']);
                        $containerContainersExist = key_exists('containers', $container) && !empty($container['containers']);
                        ?>
                        <?php if ($containerGoodsExist || $containerContainersExist): ?>
                            <tr>
                                <td></td>
                                <td colspan="16">
                                    <?php if ($containerContainersExist): ?>
                                        <table class="table table-condensed table-bordered no-datatable responsive">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>No Container</th>
                                                <th>Type</th>
                                                <th>Size</th>
                                                <th>Length Payload (M)</th>
                                                <th>Width Payload (M)</th>
                                                <th>Height Payload (M)</th>
                                                <th>Volume Payload (M<sup>3</sup>)</th>
                                                <th>Seal</th>
                                                <th>Position</th>
                                                <th>Is Empty</th>
                                                <th>Is Hold</th>
                                                <th>Status</th>
                                                <th>Danger</th>
                                                <th>Overtime</th>
                                                <th>Overtime Date</th>
                                                <th>Description</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($container['containers'] as $containerItem): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td>
                                                        <a href="<?= site_url('container/view/' . $containerItem['id_container']) ?>">
                                                            <?= $containerItem['no_container'] ?>
                                                        </a>
                                                    </td>
                                                    <td><?= $containerItem['type'] ?></td>
                                                    <td><?= $containerItem['size'] ?></td>
                                                    <td><?= numerical($containerItem['length_payload'], 3, true) ?> M</td>
                                                    <td><?= numerical($containerItem['width_payload'], 3, true) ?> M</td>
                                                    <td><?= numerical($containerItem['height_payload'], 3, true) ?> M</td>
                                                    <td><?= numerical($containerItem['volume_payload'], 3, true) ?> M<sup>3</sup></td>
                                                    <td><?= $containerItem['seal'] ?></td>
                                                    <td>
                                                        <?= $containerItem['position'] ?>
                                                        <small class="text-muted" style="display: block">
                                                            <?= str_replace(',', ', ', $containerItem['position_blocks']) ?>
                                                        </small>
                                                    </td>
                                                    <td class="<?= $containerItem['is_empty'] ? 'text-danger' :'' ?>"><?= $containerItem['is_empty'] ? 'Empty' : 'Full' ?></td>
                                                    <td class="<?= $containerItem['is_hold'] ? 'text-danger' :'' ?>"><?= $containerItem['is_hold'] ? 'Yes' : 'No' ?></td>
                                                    <td><?= if_empty($containerItem['status'], 'No Status') ?></td>
                                                    <td class="<?= $containerItem['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>"><?= $containerItem['status_danger'] ?></td>
                                                    <td><?= if_empty($containerItem['overtime_status'], '-') ?></td>
                                                    <td><?= if_empty(format_date($containerItem['overtime_date'], 'd F Y H:i:s'), '-') ?></td>
                                                    <td><?= if_empty($containerItem['description'], 'No description') ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    <?php endif ?>

                                    <?php if ($containerGoodsExist): ?>
                                        <table class="table table-condensed table-bordered no-datatable responsive">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Whey Number</th>
                                                <th>Unit Weight (Kg)</th>
                                                <th>Total Weight (Kg)</th>
                                                <th>Unit Gross (Kg)</th>
                                                <th>Total Gross (Kg)</th>
                                                <th>Unit Length (M)</th>
                                                <th>Unit Width (M)</th>
                                                <th>Unit Height (M)</th>
                                                <th>Unit Volume (M<sup>3</sup>)</th>
                                                <th>Total Volume (M<sup>3</sup>)</th>
                                                <th>Position</th>
                                                <th>No Pallet</th>
                                                <th>Is Hold</th>
                                                <th>Status</th>
                                                <th>Danger</th>
                                                <th>Ex Container</th>
                                                <th>Overtime</th>
                                                <th>Overtime Date</th>
                                                <th>Description</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($container['goods'] as $item): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td>
                                                        <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>">
                                                            <?= $item['goods_name'] ?>
                                                        </a></td>
                                                    <td><?= numerical($item['quantity'], 3, true) ?></td>
                                                    <td><?= $item['unit'] ?></td>
                                                    <td><?= if_empty($item['whey_number'], '-') ?></td>
                                                    <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                                                    <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                                    <td>
                                                        <?= if_empty($item['position'], '-') ?>
                                                        <small class="text-muted" style="display: block">
                                                            <?= str_replace(',', ', ', $item['position_blocks']) ?>
                                                        </small>
                                                    </td>
                                                    <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                                    <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>"><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                                                    <td><?= if_empty($item['status'], 'No status') ?></td>
                                                    <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>"><?= $item['status_danger'] ?></td>
                                                    <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                                    <td><?= if_empty($item['overtime_status'], '-') ?></td>
                                                    <td><?= if_empty(format_date($item['overtime_date'], 'd F Y H:i:s'), '-') ?></td>
                                                    <td><?= if_empty($item['description'], 'No description') ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>

                    <?php if (empty($containers)): ?>
                        <tr>
                            <td colspan="11" class="text-center">No data available</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(!empty($goods)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Goods</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable responsive no-wrap">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Goods</th>
                        <th>Quantity</th>
                        <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_CHECKED) { ?>
                            <th>Stock Remaining Tally Check</th>
                        <?php } ?>    
                        <?php if (isset($goods[0]['stock_remaining'])) { ?>
                            <th>Stock Remaining</th>
                        <?php } ?>
                        <th>Unit</th>
                        <th>Whey Number</th>
                        <th>Unit Weight (Kg)</th>
                        <th>Total Weight (Kg)</th>
                        <th>Unit Gross (Kg)</th>
                        <th>Total Gross (Kg)</th>
                        <th>Unit Length (M)</th>
                        <th>Unit Width (M)</th>
                        <th>Unit Height (M)</th>
                        <th>Unit Volume (M<sup>3</sup>)</th>
                        <th>Total Volume (M<sup>3</sup>)</th>
                        <th>Position</th>
                        <th>No Pallet</th>
                        <th>Is Hold</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Ex Container</th>
                        <th>Overtime</th>
                        <th>Overtime Date</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($goods as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>">
                                    <?= $item['goods_name'] ?>
                                </a>
                            </td>
                            <td><?= numerical($item['quantity'], 3, true) ?></td>
                            <?php if ($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_CHECKED) { ?>
                                <?php if(is_null($item['stock_remaining_tally'])): ?>
                                    <td>n/a</td>
                                <?php else: ?>
                                    <td><?= numerical($item['stock_remaining_tally'], 2, true) ?></td>
                                <?php endif; ?>
                            <?php } ?>
                            <?php if (isset($goods[0]['stock_remaining'])) { ?>
                                <td><input type="number" class="form-control" name="remain_<?= $item['id'] ?>" value=""></td>
                            <?php } ?>
                            <td><?= $item['unit'] ?></td>
                            <td><?= if_empty($item['whey_number'], '-') ?></td>
                            <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                            <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                            <td>
                                <?= if_empty($item['position'], '-') ?>
                                <small class="text-muted" style="display: block">
                                    <?= str_replace(',', ', ', $item['position_blocks']) ?>
                                </small>
                            </td>
                            <td><?= if_empty($item['no_pallet'], '-') ?></td>
                            <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>"><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                            <td><?= if_empty($item['status'], 'No description') ?></td>
                            <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>"><?= $item['status_danger'] ?></td>
                            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                            <td><?= if_empty($item['overtime_status'], '-') ?></td>
                            <td><?= if_empty(format_date($item['overtime_date'], 'd F Y H:i:s'), '-') ?></td>
                            <td><?= if_empty($item['description'], 'No description') ?></td>
                        </tr>
                        <?php if (key_exists('goods', $item) && !empty($item['goods'])): ?>
                            <tr>
                                <td></td>
                                <td colspan="21">
                                    <table class="table table-condensed no-datatable">
                                        <thead>
                                        <tr>
                                            <th style="width: 25px">No</th>
                                            <th>Goods</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Whey Number</th>
                                            <th>Unit Weight (Kg)</th>
                                            <th>Total Weight (Kg)</th>
                                            <th>Unit Gross (Kg)</th>
                                            <th>Total Gross (Kg)</th>
                                            <th>Unit Length (M)</th>
                                            <th>Unit Width (M)</th>
                                            <th>Unit Height (M)</th>
                                            <th>Unit Volume (M<sup>3</sup>)</th>
                                            <th>Total Volume (M<sup>3</sup>)</th>
                                            <th>Position</th>
                                            <th>No Pallet</th>
                                            <th>Is Hold</th>
                                            <th>Status</th>
                                            <th>Danger</th>
                                            <th>Ex Container</th>
                                            <th>Overtime</th>
                                            <th>Overtime Date</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $innerNo = 1; ?>
                                        <?php foreach ($item['goods'] as $itemGoods): ?>
                                            <tr>
                                                <td><?= $innerNo++ ?></td>
                                                <td><?= $itemGoods['goods_name'] ?></td>
                                                <td><?= numerical($itemGoods['quantity'], 3, true) ?></td>
                                                <td><?= $itemGoods['unit'] ?></td>
                                                <td><?= if_empty($itemGoods['whey_number'], '-') ?></td>
                                                <td><?= numerical($itemGoods['unit_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($itemGoods['total_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($itemGoods['unit_gross_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($itemGoods['total_gross_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($itemGoods['unit_length'], 3, true) ?> M</td>
                                                <td><?= numerical($itemGoods['unit_width'], 3, true) ?> M</td>
                                                <td><?= numerical($itemGoods['unit_height'], 3, true) ?> M</td>
                                                <td><?= numerical($itemGoods['unit_volume']) ?> M<sup>3</sup></td>
                                                <td><?= numerical($itemGoods['total_volume']) ?> M<sup>3</sup></td>
                                                <td>
                                                    <?= if_empty($itemGoods['position'], '-') ?>
                                                    <small class="text-muted" style="display: block">
                                                        <?= str_replace(',', ', ', $itemGoods['position_blocks']) ?>
                                                    </small>
                                                </td>
                                                <td><?= if_empty($itemGoods['no_pallet'], '-') ?></td>
                                                <td class="<?= $itemGoods['is_hold'] ? 'text-danger' :'' ?>"><?= $itemGoods['is_hold'] ? 'Yes' : 'No' ?></td>
                                                <td><?= if_empty($itemGoods['status'], 'No status') ?></td>
                                                <td class="<?= $itemGoods['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>"><?= $itemGoods['status_danger'] ?></td>
                                                <td><?= if_empty($itemGoods['ex_no_container'], '-') ?></td>
                                                <td><?= if_empty($itemGoods['overtime_status'], '-') ?></td>
                                                <td><?= if_empty(format_date($itemGoods['overtime_date'], 'd F Y H:i:s'), '-') ?></td>
                                                <td><?= if_empty($itemGoods['description'], 'No description') ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif ?>
                        <?php if(isset($this->goodsModel)): ?>
                            <?php
                            $goodsDetail = $this->goodsModel->getById($item['id_goods']);
                            $goodsAssembly = $this->assemblyGoodsModel->getBy(['ref_assembly_goods.id_assembly' => $goodsDetail['id_assembly']]);
                            ?>
                            <?php if ( (!empty($goodsAssembly) && ($handling_type == "STRIPPING")) || (!empty($goodsAssembly) && ($handling_type == "LOAD")) ): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="19">
                                        <table class="table table-condensed no-datatable">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity Assembly</th>
                                                <th>Unit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($goodsAssembly as $goods_assembly): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td><?= $goods_assembly['goods_name'] ?></td>
                                                    <td><?= numerical($goods_assembly['quantity_assembly'], 3,true) ?></td>
                                                    <td><?= $goods_assembly['unit'] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if (empty($goods)): ?>
                        <tr>
                            <td colspan="21" class="text-center">No data available</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(!empty($components)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Job Components</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 30px">No</th>
                    <th>Component</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($components as $component): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $component['handling_component'] ?></td>
                        <td><?= numerical((key_exists('quantity', $component) ? $component['quantity'] : $component['handling_component_qty']), 3, true) ?></td>
                        <td><?= if_empty($component['unit'], '-') ?></td>
                        <td><?= if_empty($component['description'], '-') ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (empty($components)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No data available</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
