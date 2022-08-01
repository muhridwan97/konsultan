<div class="box box-primary">
    <div class="box-header with-border row-workorder" data-no="<?= $workOrder['no_work_order'] ?>">
        <h3 class="box-title">View Job History</h3>
    </div>
    <div class="box-body">
        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Job</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['no_work_order'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Handling</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('handling/view/' . $workOrder['id_handling']) ?>">
                                    <?= $workOrder['no_handling'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Handling Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['handling_type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $workOrder['id_booking']) ?>">
                                    <?= $workOrder['no_booking'] ?>
                                </a> (<?= $workOrder['no_reference'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Queue</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrder['queue'] == 0 ? 'Auto Job (No queue)' : $workOrder['queue'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $dataLabel = [
                                    WorkOrderModel::STATUS_QUEUED => 'danger',
                                    WorkOrderModel::STATUS_TAKEN => 'warning',
                                    WorkOrderModel::STATUS_COMPLETED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_PENDING => 'default',
                                    WorkOrderModel::STATUS_VALIDATION_ON_REVIEW => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_VALIDATED => 'primary',
                                    WorkOrderModel::STATUS_VALIDATION_APPROVED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_CHECKED => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED => 'primary',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED => 'success',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN => 'warning',
                                    WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED => 'info',
                                ];
                                ?>
                                <span class="label label-<?= $dataLabel[$workOrder['status']] ?> mr10">
                                    <?php if(empty($workOrder['gate_in_date'])): ?>
                                        NEED GATE IN
                                    <?php else: ?>
                                        <?= $workOrder['status'] ?>
                                    <?php endif ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Attachment</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($workOrder['attachment'])): ?>
                                    No attachment
                                <?php else: ?>
                                    <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                        Download Attachment
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Safe Conduct</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(!empty($workOrder['no_safe_conduct'])): ?>
                                    <a href="<?= site_url('safe-conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                        <?= $workOrder['no_safe_conduct'] ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Transporter</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (!empty($workOrder['id_vehicle'])) { ?>
                                    <?= 'INTERNAL TCI' ?>
                                <?php } else if (!empty($workOrder['id_transporter_entry_permit'])) { ?>
                                    <?= 'EXTERNAL' ?>
                                <?php } else  { ?>
                                    <?= '-' ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Plate Number</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['no_police'],values($workOrder['no_plate_take'].' (TAKE)','-')) ?>
                                <?php if (!empty($workOrder['vehicle_type'])) { ?>
                                    (<?= values($workOrder['vehicle_type'],'-') ?>)
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shipping Route</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['shipping_route'],'-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Armada</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['jenis_armada'],'-') ?>
                                <?php if (!empty($workOrder['armada_type'])) { ?>
                                    (<?= values($workOrder['armada_type'],'-') ?>)
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Armada Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= values($workOrder['armada_description'],'-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Gate In</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['gate_in_date']) ? '-' : readable_date($workOrder['gate_in_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Gate Out</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['gate_out_date']) ? '-' : readable_date($workOrder['gate_out_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ST Gate</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['service_time'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Taken At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['taken_at']) ? '-' : readable_date($workOrder['taken_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Completed At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= empty($workOrder['completed_at']) ? '-' : readable_date($workOrder['completed_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Completed By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['completed_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ST Tally</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['service_time_tally'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Taken By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrder['tally_name'], 'Not taken yet') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($workOrder['created_at']) ?>
                                <?= if_empty($workOrder['creator_name'], '', ' by (', ')') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($workOrder['updated_at']) ?>
                                <?= if_empty($workOrder['updater_name'], '', ' by (', ')') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
                                        <td colspan="22">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (empty($goods)): ?>
                                <tr>
                                    <td colspan="21" class="text-center">No data available</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
    </div>
</div>