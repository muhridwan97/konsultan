<div class="mb20">
    <p class="lead mb0">
        <strong>HANDLING CHARGE COMPONENTS</strong>
    </p>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <p class="mb10"><strong>CUSTOMER INFO</strong></p>
        <hr class="mt0">

        <div class="row mb10">
            <div class="col-md-4">
                <p class="mb0">No Customer:</p>
                <strong><?= $customer['no_person'] ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Customer:</p>
                <strong><?= $customer['name'] ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Address:</p>
                <strong><?= if_empty($customer['address'], '-') ?></strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <p class="mb0">Contact:</p>
                <strong><?= if_empty($customer['contact'], '-') ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Email:</p>
                <strong><?= if_empty($customer['email'], '-') ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Tax Number:</p>
                <strong><?= if_empty($customer['tax_number'], '-') ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <p class="mb10"><strong>HANDLING INFO</strong></p>
        <hr class="mt0">

        <div class="row mb20">
            <div class="col-md-4">
                <p class="mb0">No Handling:</p>
                <strong><?= $handling['no_handling'] ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Handling Plan:</p>
                <strong><?= readable_date($handling['handling_date'], false) ?></strong>
            </div>
            <div class="col-md-4">
                <p class="mb0">Created At:</p>
                <strong><?= readable_date($handling['created_at']) ?></strong>
            </div>
        </div>

        <?php if (!empty($handlingContainers)): ?>
            <table class="table table-bordered table-striped table-condensed no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Container</th>
                    <th>Type</th>
                    <th>Size</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($handlingContainers as $container): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['type'] ?></td>
                        <td><?= $container['size'] ?></td>
                    </tr>
                    <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                        <tr>
                            <td></td>
                            <td colspan="3">
                                <table class="table table-condensed no-datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px">No</th>
                                        <th>Goods</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Tonnage (Kg)</th>
                                        <th>Volume (M<sup>3</sup>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $innerNo = 1; ?>
                                    <?php foreach ($container['goods'] as $item): ?>
                                        <tr>
                                            <td><?= $innerNo++ ?></td>
                                            <td><?= $item['name'] ?></td>
                                            <td><?= numerical($item['quantity']) ?></td>
                                            <td><?= $item['unit'] ?></td>
                                            <td><?= numerical($item['tonnage']) ?></td>
                                            <td><?= numerical($item['volume']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($handlingGoods)): ?>
            <table class="table table-bordered table-striped table-condensed no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Goods</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Tonnage (Kg)</th>
                    <th>Volume (M<sup>3</sup>)</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($handlingGoods as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $item['goods_name'] ?></td>
                        <td><?= numerical($item['quantity']) ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td><?= numerical($item['tonnage']) ?></td>
                        <td><?= numerical($item['volume']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="mb10"><strong>PLAN COMPONENT</strong></p>
        <hr class="mt0">

        <?php if (!empty($handling['components'])): ?>
            <table class="table table-bordered table-striped table-condensed no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Component</th>
                    <th>Quantity</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($handling['components'] as $component): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $component['handling_component'] ?></td>
                        <td><?= numerical($component['quantity']) ?></td>
                        <td><?= if_empty($component['description'], '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<?php foreach ($workOrders as $workOrder): ?>
    <?php if($workOrder['status'] == 'COMPLETED'): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <p class="mb10"><strong>JOB INFO</strong></p>
                <hr class="mt0">

                <div class="row mb20">
                    <div class="col-md-4">
                        <p class="mb0">No Job:</p>
                        <strong><?= $workOrder['no_work_order'] ?></strong>
                    </div>
                    <div class="col-md-4">
                        <p class="mb0">Gate In:</p>
                        <strong><?= readable_date($workOrder['gate_in_date']) ?></strong>
                    </div>
                    <div class="col-md-4">
                        <p class="mb0">Gate Out:</p>
                        <strong><?= readable_date($workOrder['gate_out_date']) ?></strong>
                    </div>
                </div>

                <?php if (!empty($workOrder['containers'])): ?>
                    <table class="table table-bordered table-striped table-condensed no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Container</th>
                            <th>Type</th>
                            <th>Size</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($workOrder['containers'] as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $container['no_container'] ?></td>
                                <td><?= $container['type'] ?></td>
                                <td><?= $container['size'] ?></td>
                            </tr>
                            <?php
                            $containerGoodsExist = key_exists('goods', $container) && !empty($container['goods']);
                            $containerContainersExist = key_exists('containers', $container) && !empty($container['containers']);
                            ?>
                            <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="3">
                                        <?php if ($containerContainersExist): ?>
                                            <table class="table table-condensed no-datatable">
                                                <thead>
                                                <tr>
                                                    <th style="width: 25px">No</th>
                                                    <th>No Container</th>
                                                    <th>Type</th>
                                                    <th>Size</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $innerNo = 1; ?>
                                                <?php foreach ($container['containers'] as $containerItem): ?>
                                                    <tr>
                                                        <td><?= $innerNo++ ?></td>
                                                        <td><?= $containerItem['no_container'] ?></td>
                                                        <td><?= $containerItem['type'] ?></td>
                                                        <td><?= $containerItem['size'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>

                                        <?php if ($containerGoodsExist): ?>

                                            <table class="table table-condensed no-datatable">
                                                <thead>
                                                <tr>
                                                    <th style="width: 25px">No</th>
                                                    <th>Goods</th>
                                                    <th>Quantity</th>
                                                    <th>Unit</th>
                                                    <th>Tonnage (Kg)</th>
                                                    <th>Volume (M<sup>3</sup>)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $innerNo = 1; ?>
                                                <?php foreach ($container['goods'] as $item): ?>
                                                    <tr>
                                                        <td><?= $innerNo++ ?></td>
                                                        <td><?= $item['name'] ?></td>
                                                        <td><?= numerical($item['quantity']) ?></td>
                                                        <td><?= $item['unit'] ?></td>
                                                        <td><?= numerical($item['tonnage']) ?></td>
                                                        <td><?= numerical($item['volume']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($workOrder['goods'])): ?>
                    <table class="table table-bordered table-striped table-condensed no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Tonnage (Kg)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($workOrder['goods'] as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= numerical($item['quantity']) ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td><?= numerical($item['tonnage']) ?></td>
                                <td><?= numerical($item['volume']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <p class="mb10"><strong>REALIZATION COMPONENT</strong></p>
                <hr class="mt0">

                <?php if (!empty($workOrder['components'])): ?>
                    <table class="table table-bordered table-striped table-condensed no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Component</th>
                            <th>Quantity</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($workOrder['components'] as $component): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $component['handling_component'] ?></td>
                                <td><?= numerical($component['quantity']) ?></td>
                                <td><?= if_empty($component['description'], '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php $this->load->view('handling/_handling_charge') ?>
