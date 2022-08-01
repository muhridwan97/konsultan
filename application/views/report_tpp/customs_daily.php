<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Customs Daily Report</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report_tpp/_filter_customs_daily', ['hidden' => false]) ?>

        <?php foreach ($reportCustomsDaily as $keyDate => $reportCustoms) : ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Date: <?= format_date($keyDate, 'd F Y') ?></strong>
                </div>
                <div class="panel-body">
                    <h4 class="mt0">Inbound</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-wrap no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Container/Cargo</th>
                                <th>20</th>
                                <th>40</th>
                                <th>45</th>
                                <th>LCL</th>
                                <th>Seal</th>
                                <th>BC Doc (1.5)</th>
                                <th>BC Date (1.5)</th>
                                <th>Quantity and Goods Description</th>
                                <th>Consignee</th>
                                <th>TPS</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reportCustoms['inbound'] as $index => $report): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $report['item_type'] == 'GOODS' ? 'LCL' : if_empty($report['no_container'], '-') ?></td>
                                    <td><?= $report['container_size'] == '20' ? '1' : '' ?></td>
                                    <td><?= $report['container_size'] == '40' ? '1' : '' ?></td>
                                    <td><?= $report['container_size'] == '45' ? '1' : '' ?></td>
                                    <td><?= $report['item_type'] == 'GOODS' ? '1' : '' ?></td>
                                    <td><?= if_empty($report['seal'], '-') ?></td>
                                    <td>
                                        <?= if_empty($report['no_reference'], '-') ?>
                                        <small class="text-danger"><?= $report['booking_type'] == 'TEGAHAN' ? '(TEGAHAN)' : '' ?></small>
                                    </td>
                                    <td><?= format_date($report['reference_date'], 'd F Y') ?></td>
                                    <td><?= if_empty($report['goods_name'], '-') ?></td>
                                    <td><?= if_empty($report['booking_customer_name'], '-') ?></td>
                                    <td><?= if_empty($report['tps_name'], '-') ?></td>
                                    <td><?= format_date($report['completed_at'], 'H:i:s') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($reportCustoms['inbound'])): ?>
                                <tr>
                                    <td colspan="13">No inbound data</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td><?= $reportCustoms['inbound_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['inbound_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['inbound_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['inbound_summary']['total_lcl'] ?></td>
                                <td colspan="7"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <br>

                    <h4 class="mt0">Outbound</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-wrap no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Container/Cargo</th>
                                <th>20</th>
                                <th>40</th>
                                <th>45</th>
                                <th>LCL</th>
                                <th>Seal</th>
                                <th>BC 15</th>
                                <th>BC 15 Date</th>
                                <th>Quantity and Goods Description</th>
                                <th>Consignee</th>
                                <th>TPS</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reportCustoms['outbound'] as $index => $report): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $report['item_type'] == 'GOODS' ? 'LCL' : if_empty($report['no_container'], '-') ?></td>
                                    <td><?= $report['container_size'] == '20' ? '1' : '' ?></td>
                                    <td><?= $report['container_size'] == '40' ? '1' : '' ?></td>
                                    <td><?= $report['container_size'] == '45' ? '1' : '' ?></td>
                                    <td><?= $report['item_type'] == 'GOODS' ? '1' : '' ?></td>
                                    <td><?= if_empty($report['seal'], '-') ?></td>
                                    <td><?= if_empty($report['no_reference'], '-') ?></td>
                                    <td><?= readable_date($report['reference_date'], false, '-') ?></td>
                                    <td><?= if_empty($report['goods_name'], '-') ?></td>
                                    <td><?= if_empty($report['booking_customer_name'], '-') ?></td>
                                    <td><?= if_empty($report['tps_name'], '-') ?></td>
                                    <td><?= format_date($report['completed_at'], 'H:i:s') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($reportCustoms['outbound'])): ?>
                                <tr>
                                    <td colspan="13">No outbound data</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td><?= $reportCustoms['outbound_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['outbound_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['outbound_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['outbound_summary']['total_lcl'] ?></td>
                                <td colspan="7"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <br>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-wrap no-datatable">
                            <thead>
                            <tr>
                                <th>Information</th>
                                <th>20</th>
                                <th>40</th>
                                <th>45</th>
                                <th>LCL</th>
                                <th>Container Sum</th>
                                <th>BC Doc Sum</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Inbound</td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_lcl'] ?></td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_container'] ?></td>
                                <td><?= $reportCustoms['inbound_movement_summary']['total_bcf'] ?></td>
                            </tr>
                            <tr>
                                <td>Outbound</td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_lcl'] ?></td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_container'] ?></td>
                                <td><?= $reportCustoms['outbound_movement_summary']['total_bcf'] ?></td>
                            </tr>
                            <tr class="bg-red">
                                <td>Inbound Hold</td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_lcl'] ?></td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_container'] ?></td>
                                <td><?= $reportCustoms['hold_inbound_movement_summary']['total_bcf'] ?></td>
                            </tr>
                            <tr class="bg-red">
                                <td>Outbound Hold</td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_20'] ?></td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_40'] ?></td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_45'] ?></td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_lcl'] ?></td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_container'] ?></td>
                                <td><?= $reportCustoms['hold_outbound_movement_summary']['total_bcf'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php if(get_url_param('filter_activity') && empty($reportCustomsDaily)): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                No data custom daily available
            </p>
        </div>
    </div>
<?php endif ?>
