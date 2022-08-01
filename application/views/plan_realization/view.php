<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Plan <?= $planRealization['date'] ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $planRealization['date'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Inbound</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($planRealization['total_inbound'], 0) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Outbound</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($planRealization['total_outbound'], 0) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($planRealization['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($planRealization['status'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Analysis</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($planRealization['analysis'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($planRealization['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($planRealization['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Plan & Realization Resources</h3>
            </div>
            <div class="box-body">
                <table class="table table-sm table-hover table-striped table-bordered responsive" data-page-length="10">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Resource</th>
                        <th>Plan</th>
                        <th>Unit</th>
                        <th class="text-center">Last Realization</th>
                        <th class="text-center warning">Outstanding</th>
                        <th class="success text-center">Close Realization</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($resources as $index => $resource): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $resource['resource'] ?></td>
                            <td><?= if_empty($resource['plan'], 0) ?></td>
                            <td><?= if_empty($resource['unit'], '-') ?></td>
                            <td class="text-center"><?= if_empty($resource['realization']) ?></td>
                            <td class="warning text-center"><?= if_empty($resource['outstanding']) ?></td>
                            <td class="success text-center"><?= if_empty($resource['close_realization']) ?></td>
                            <td><?= if_empty($resource['description'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Plan & Realization Inbound</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-sm table-hover table-striped table-bordered table-scroll no-datatable" data-page-length="10">
                    <thead class="no-wrap">
                    <tr>
                        <th rowspan="2" style="width: 30px">No</th>
                        <th rowspan="2" style="width: 30px">Skip</th>
                        <th rowspan="2">Customer Name</th>
                        <th rowspan="2">No Reference</th>
                        <th rowspan="2">Entry Permits</th>
                        <th rowspan="2" style="min-width: 300px">Item Name</th>
                        <th rowspan="2">Location</th>
                        <th rowspan="2">Realization Location</th>
                        <th rowspan="2">SPPB Date</th>
                        <th rowspan="2">Inbound Date</th>
                        <th colspan="3" class="text-center">Party</th>
                        <th colspan="3" class="text-center">Last Unload</th>
                        <th colspan="3" class="warning text-center">Outstanding</th>
                        <th colspan="3" class="success text-center">Close Realization</th>
                        <th colspan="3" class="text-center">Left Total</th>
                        <th rowspan="2" class="text-center">Achievement</th>
                        <th rowspan="2">Remark</th>
                    </tr>
                    <tr class="text-center">
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                        <th class="warning">20'</th>
                        <th class="warning">40'</th>
                        <th class="warning">LCL</th>
                        <th class="success">20'</th>
                        <th class="success">40'</th>
                        <th class="success">LCL</th>
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($inbounds as $index => $inbound): ?>
                        <tr class="<?= $inbound['is_skipped'] ? 'danger' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if($inbound['is_skipped']): ?>
                                    <span class="label label-danger">SKIP</span>
                                <?php else: ?>
                                    <span class="label label-success">NO</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $inbound['customer_name'] ?></td>
                            <td><?= substr($inbound['no_reference'], -6) ?></td>
                            <td>
                                <?php foreach ($inbound['transporter_entry_permits'] as $tep): ?>
                                    <?= $tep['tep_code'] ?><br>
                                <?php endforeach; ?>
                                <?php if (empty($inbound['transporter_entry_permits'])): ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= character_limiter($inbound['item_name'], 70) ?></td>
                            <td><?= if_empty($inbound['unloading_location'], '-') ?></td>
                            <td><?= if_empty($inbound['realization_location'], '-') ?></td>
                            <td><?= if_empty($inbound['sppb_date']) ?></td>
                            <td><?= if_empty($inbound['inbound_date'], '-') ?></td>
                            <td class="text-center"><?= if_empty($inbound['party_20']) ?></td>
                            <td class="text-center"><?= if_empty($inbound['party_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($inbound['party_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= if_empty($inbound['realization_20']) ?></td>
                            <td class="text-center"><?= if_empty($inbound['realization_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($inbound['realization_lcl'], 2, true)) ?></td>
                            <td class="warning text-center"><?= if_empty($inbound['outstanding_20']) ?></td>
                            <td class="warning text-center"><?= if_empty($inbound['outstanding_40']) ?></td>
                            <td class="warning text-center"><?= if_empty(numerical($inbound['outstanding_lcl'], 2, true)) ?></td>
                            <td class="success text-center"><?= if_empty($inbound['close_realization_20']) ?></td>
                            <td class="success text-center"><?= if_empty($inbound['close_realization_40']) ?></td>
                            <td class="success text-center"><?= if_empty(numerical($inbound['close_realization_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= if_empty($inbound['left_20']) ?></td>
                            <td class="text-center"><?= if_empty($inbound['left_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($inbound['left_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= numerical($inbound['achievement'], 2, true) ?>%</td>
                            <td class="text-center"><?= if_empty($inbound['description'], '-') ?></td>
                        </tr>
                        <?php if (!empty($inbound['resources'])): ?>
                            <tr style="font-weight: bold">
                                <td></td>
                                <td></td>
                                <td>Resources</td>
                                <td>Unit</td>
                                <td class="warning">Plan</td>
                                <td class="success">Realization</td>
                                <td colspan="21"></td>
                            </tr>
                            <?php foreach ($inbound['resources'] as $resource): ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><?= $resource['resource'] ?></td>
                                    <td><?= $resource['unit'] ?></td>
                                    <td class="warning"><?= if_empty($resource['plan'], 0) ?></td>
                                    <td class="success"><?= if_empty($resource['close_realization']) ?></td>
                                    <td colspan="21"></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="27">&nbsp;</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <table class="table table-striped table-bordered no-datatable">
                    <tr class="success">
                        <td class="lead"><?= $inboundSummary['target'] ?></td>
                        <td class="lead"><?= $inboundSummary['realization'] ?></td>
                        <td class="lead"><?= numerical($inboundSummary['achievement'], 2, true) ?>%</td>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <th>Realization</th>
                        <th>Achievement</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Plan & Realization Outbound</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-sm table-hover table-striped table-bordered table-scroll no-datatable" data-page-length="10">
                    <thead class="no-wrap">
                    <tr>
                        <th rowspan="2" style="width: 30px">No</th>
                        <th rowspan="2" style="width: 30px">Skip</th>
                        <th rowspan="2">Customer Name</th>
                        <th rowspan="2">No Reference / Ex Inbound</th>
                        <th rowspan="2">Entry Permits</th>
                        <th rowspan="2" style="min-width: 300px">Item Name</th>
                        <th rowspan="2">Location</th>
                        <th rowspan="2">Realization Location</th>
                        <th rowspan="2">Special Equipment</th>
                        <th rowspan="2">SPPB Date</th>
                        <th rowspan="2">Instruction Date</th>
                        <th colspan="3" class="text-center">Party</th>
                        <th colspan="3" class="text-center">Last Load</th>
                        <th colspan="3" class="warning text-center">Outstanding</th>
                        <th colspan="3" class="success text-center">Close Realization</th>
                        <th colspan="3" class="text-center">Left Total</th>
                        <th rowspan="2" class="text-center">Achievement</th>
                        <th rowspan="2">Remark</th>
                    </tr>
                    <tr class="text-center">
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                        <th class="warning">20'</th>
                        <th class="warning">40'</th>
                        <th class="warning">LCL</th>
                        <th class="success">20'</th>
                        <th class="success">40'</th>
                        <th class="success">LCL</th>
                        <th>20'</th>
                        <th>40'</th>
                        <th>LCL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($outbounds as $index => $outbound): ?>
                        <tr class="<?= $outbound['is_skipped'] ? 'danger' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if($outbound['is_skipped']): ?>
                                    <span class="label label-danger">SKIP</span>
                                <?php else: ?>
                                    <span class="label label-success">NO</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $outbound['customer_name'] ?></td>
                            <td>
                                <?= substr($outbound['no_reference'], -6) ?> /
                                <?= substr($outbound['no_reference_inbound'], -6) ?>
                            </td>
                            <td>
                                <?php foreach ($outbound['transporter_entry_permits'] as $tep): ?>
                                    <?= $tep['tep_code'] ?><br>
                                <?php endforeach; ?>
                                <?php if (empty($outbound['transporter_entry_permits'])): ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= character_limiter($outbound['item_name'], 70) ?></td>
                            <td><?= if_empty($outbound['loading_location'], '-') ?></td>
                            <td><?= if_empty($outbound['realization_location'], '-') ?></td>
                            <td><?= str_replace(',', '<br>', if_empty($outbound['special_equipment'], '-')) ?></td>
                            <td><?= if_empty($outbound['sppb_date'], '-') ?></td>
                            <td><?= if_empty($outbound['instruction_date'], '-') ?></td>
                            <td class="text-center"><?= if_empty($outbound['party_20']) ?></td>
                            <td class="text-center"><?= if_empty($outbound['party_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($outbound['party_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= if_empty($outbound['realization_20']) ?></td>
                            <td class="text-center"><?= if_empty($outbound['realization_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($outbound['realization_lcl'], 2, true)) ?></td>
                            <td class="warning text-center"><?= if_empty($outbound['outstanding_20']) ?></td>
                            <td class="warning text-center"><?= if_empty($outbound['outstanding_40']) ?></td>
                            <td class="warning text-center"><?= if_empty(numerical($outbound['outstanding_lcl'], 2, true)) ?></td>
                            <td class="success text-center"><?= if_empty($outbound['close_realization_20']) ?></td>
                            <td class="success text-center"><?= if_empty($outbound['close_realization_40']) ?></td>
                            <td class="success text-center"><?= if_empty(numerical($outbound['close_realization_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= if_empty($outbound['left_20']) ?></td>
                            <td class="text-center"><?= if_empty($outbound['left_40']) ?></td>
                            <td class="text-center"><?= if_empty(numerical($outbound['left_lcl'], 2, true)) ?></td>
                            <td class="text-center"><?= numerical($outbound['achievement'], 2, true) ?>%</td>
                            <td><?= if_empty($outbound['description'], '-') ?></td>
                        </tr>
                        <?php if (!empty($outbound['resources'])): ?>
                            <tr style="font-weight: bold">
                                <td></td>
                                <td></td>
                                <td>Resources</td>
                                <td>Unit</td>
                                <td class="warning">Plan</td>
                                <td class="success">Realization</td>
                                <td colspan="22"></td>
                            </tr>
                            <?php foreach ($outbound['resources'] as $resource): ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><?= $resource['resource'] ?></td>
                                    <td><?= $resource['unit'] ?></td>
                                    <td class="warning"><?= if_empty($resource['plan'], 0) ?></td>
                                    <td class="success"><?= if_empty($resource['close_realization']) ?></td>
                                    <td colspan="22"></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="27">&nbsp;</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <table class="table table-bordered no-datatable">
                    <tr class="success">
                        <td class="lead"><?= $outboundSummary['target'] ?></td>
                        <td class="lead"><?= $outboundSummary['realization'] ?></td>
                        <td class="lead"><?= numerical($outboundSummary['achievement'], 2, true) ?>%</td>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <th>Realization</th>
                        <th>Achievement</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status Histories</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($statusHistories as $status): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $status['status'] ?></td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td><?= format_date($status['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($statusHistories)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('plan-realization/print-plan-realization/' . $planRealization['id']) ?>" class="btn btn-primary pull-right">
            Print
        </a>
    </div>
</div>