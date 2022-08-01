<?php if( (!empty($safeConductContainers) || !empty($safeConductGoods)) ): ?>
   
        <?php if( !empty($safeConductContainers) ): ?>
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
                            <?php if(isset($safeConduct)): ?>
                                <th class="text-center">Action</th>
                            <?php endif; ?>
                            <th>No Reference</th>
                            <th>No Container</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Seal</th>
                            <th>Position</th>
                            <th>Is Empty</th>
                            <th>Is Hold</th>
                            <th>Status</th>
                            <th>Danger</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($safeConductContainers as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <?php if($this->uri->segment(3) != 'safe-conduct'): ?>
                                    <?php if(isset($safeConduct)): ?>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
                                                    <?php if($safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                                                        <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                                            <?php if(!empty($safeConduct['security_in_date']) && $container['total_check_out'] <= 0 ): ?>
                                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id-safe-conduct="<?= $container['id_safe_conduct'] ?>" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                                            <?php else: ?>
                                                                <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out/' . $container['id']) ?>">
                                                                    <i class="fa ion-search mr10"></i>View Checklist End
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if( empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $container['total_check_in'] <= 0 ): ?>
                                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id-safe-conduct="<?= $container['id_safe_conduct'] ?>" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                                            <?php else: ?>
                                                                <?php if($container['total_check_in'] > 0 ): ?>
                                                                <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in/' . $container['id']) ?>">
                                                                    <i class="fa ion-search mr10"></i>View Checklist Start
                                                                </a>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                            <?php if( !empty($safeConduct['security_in_date']) && $container['total_check_out'] <= 0 && $container['total_check_in'] <= 0 ): ?>
                                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id-safe-conduct="<?= $container['id_safe_conduct'] ?>" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                                            <?php else: ?>
                                                                <?php if($container['total_check_out'] > 0 ): ?>
                                                                <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out/' . $container['id']) ?>">
                                                                    <i class="fa ion-search mr10"></i>View Checklist End
                                                                </a>
                                                                 <?php endif; ?>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                                            <?php if( empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $container['total_check_in'] <= 0 ): ?>
                                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id-safe-conduct="<?= $container['id_safe_conduct'] ?>" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                                            <?php else: ?>
                                                                <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in/' . $container['id']) ?>">
                                                                    <i class="fa ion-search mr10"></i>View Checklist Start
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if( !empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $container['total_check_out'] <= 0 ): ?>
                                                                <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-container" data-id-safe-conduct="<?= $container['id_safe_conduct'] ?>" data-id="<?= $container['id'] ?>" data-label="<?= $container['no_container'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                                            <?php else: ?>
                                                                <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out/' . $container['id']) ?>">
                                                                    <i class="fa ion-search mr10"></i>View Checklist End
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if(isset($safeConduct) && ($container['total_check_out'] > 0 || $container['total_check_in'] > 0)): ?>
                                    <td class="text-center">
                                        <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                            <div class="btn-group">
                                            <?php if ($container['total_check_in'] > 0): ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist Start
                                            </a>
                                            <?php endif; ?>
                                            <?php if($container['total_check_out'] > 0): ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist End
                                            </a>
                                            <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="btn-group">
                                            <?php if ($container['total_check_in'] > 0): ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist Start
                                            </a>
                                            <?php endif; ?>
                                            <?php if($container['total_check_out'] > 0): ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out/' . $container['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist End
                                            </a>
                                            <?php endif; ?>
                                            </div>
                                         <?php endif; ?>
                                    </td>
                                    <?php else: ?>
                                    <td class="text-center">
                                        -
                                    </td>
                                     <?php endif; ?>
                                <?php endif; ?>
                                <td title="<?= $container['no_booking_reference'] ?>">
                                    <a href="<?= site_url('booking/view/' . if_empty($container['id_booking_reference'], $container['id_booking'])) ?>">
                                        <?= mid_ellipsis(if_empty($container['no_booking_reference'], $container['no_reference'])) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= site_url('container/view/' . $container['id_container']) ?>">
                                        <?= $container['no_container'] ?>
                                    </a>
                                </td>
                                <td><?= $container['type'] ?></td>
                                <td><?= $container['size'] ?></td>
                                <td><?= if_empty($container['seal'], '-') ?></td>
                                <td><?= if_empty($container['position'], '-') ?></td>
                                <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                                    <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                                </td>
                                <td class="<?= $container['is_hold'] ? 'text-danger' :'' ?>">
                                    <?= $container['is_hold'] ? 'Yes' : 'No' ?>
                                </td>
                                <td><?= if_empty($container['status'], 'No Status') ?></td>
                                <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                    <?= if_empty($container['status_danger'], 'No Status') ?>
                                </td>
                                <td><?= if_empty($container['description'], 'No description') ?></td>
                            </tr>
                            <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="11">
                                        <table class="table table-condensed table-bordered no-datatable responsive">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Unit Weight (Kg)</th>
                                                <th>Total Weight (Kg)</th>
                                                <th>Unit Gross (Kg)</th>
                                                <th>Total Gross (Kg)</th>
                                                <th>Unit Length (M)</th>
                                                <th>Unit Width (M)</th>
                                                <th>Unit Height (M)</th>
                                                <th>Unit Volume (M<sup>3</sup>)</th>
                                                <th>Total Volume (M<sup>3</sup>)</th>
                                                <th>Is Hold</th>
                                                <th>Status</th>
                                                <th>Danger</th>
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
                                                        </a><br>
                                                        <small class="text-muted"><?= $item['no_goods'] ?></small>
                                                    </td>
                                                    <td><?= numerical($item['quantity'], 3, true) ?></td>
                                                    <td><?= $item['unit'] ?></td>
                                                    <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                                                    <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                                                    <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                                                    <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                                    <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>"><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                                                    <td><?= if_empty($item['status'], 'No status') ?></td>
                                                    <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>"><?= $item['status_danger'] ?></td>
                                                    <td><?= if_empty($item['description'], 'No description') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($safeConductContainers)): ?>
                            <tr>
                                <td colspan="12" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if(!empty($safeConductGoods)): ?>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Goods</h3>
                <?php if($this->uri->segment(3) != 'safe-conduct'): ?>
                    <div class="btn-group pull-right">
                        <?php if (isset($safeConduct) && AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
                            <?php if($safeConduct['type'] == BookingTypeModel::CATEGORY_INBOUND): ?>
                                <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                    <?php if( !empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $safeConduct['total_check_out'] <= 0 ): ?>
                                        <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                    <?php else: ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist End
                                            </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if( empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $safeConduct['total_check_in'] <= 0 ): ?>
                                        <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                    <?php else: ?>
                                        <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>">
                                            <i class="fa ion-search mr10"></i>View Checklist Start
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if($safeConduct['expedition_type'] == 'INTERNAL'): ?>
                                    <?php if( empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $safeConduct['total_check_in'] <= 0 ): ?>
                                        <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist Start</a>
                                    <?php else: ?>
                                        <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_in_goods/' . $safeConduct['id']) ?>">
                                            <i class="fa ion-search mr10"></i>View Checklist Start
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if( !empty($safeConduct['security_in_date']) && empty($safeConduct['security_out_date']) && $safeConduct['total_check_out'] <= 0 ): ?>
                                        <a href="<?= site_url('security/security-checklist/'. '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-danger btn-goods" data-label="<?= $safeConduct['no_safe_conduct'] ?>" data-browse-photo="<?= $allowBrowse ?>">Checklist End</a>
                                    <?php else: ?>
                                            <a class="btn btn-success" href="<?= site_url('safe-conduct/view_checklist_out_goods/' . $safeConduct['id']) ?>">
                                                <i class="fa ion-search mr10"></i>View Checklist End
                                            </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Reference</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Unit Weight (Kg)</th>
                            <th>Total Weight (Kg)</th>
                            <th>Unit Gross (Kg)</th>
                            <th>Total Gross (Kg)</th>
                            <th>Unit Length (M)</th>
                            <th>Unit Width (M)</th>
                            <th>Unit Height (M)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                            <th>Total Volume (M<sup>3</sup>)</th>
                            <th>Position</th>
                            <th>Is Hold</th>
                            <th>Status</th>
                            <th>Danger</th>
                            <th>Ex Container</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($safeConductGoods as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td title="<?= $item['no_booking_reference'] ?>">
                                    <a href="<?= site_url('booking/view/' . if_empty($item['id_booking_reference'], $item['id_booking'])) ?>">
                                        <?= mid_ellipsis(if_empty($item['no_booking_reference'], $item['no_reference'])) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>">
                                        <?= $item['goods_name'] ?>
                                    </a><br>
                                    <small class="text-muted"><?= $item['no_goods'] ?></small>
                                </td>
                                <td><?= numerical($item['quantity'], 3, true) ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                                <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                                <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                                <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                                <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                                <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                                <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                                <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                                <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                <td><?= if_empty($item['position'], '-') ?></td>
                                <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>">
                                    <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                                </td>
                                <td><?= if_empty($item['status'], 'No status') ?></td>
                                <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                    <?= $item['status_danger'] ?>
                                </td>
                                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                <td><?= if_empty($item['description'], 'No description') ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($safeConductGoods)): ?>
                            <tr>
                                <td colspan="20" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_CHECKLIST_VIEW)): ?>
   <?php $this->load->view('security/_modal_security_checklist') ?>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/safe_conduct.js?v=19') ?>" defer></script>
<?php $this->load->view('security/_modal_notification') ?>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('tally/_modal_select_position') ?>
