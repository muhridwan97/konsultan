<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Plan & Realization Resource</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?export=1" class="btn btn-success">
                Export
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-sm table-hover table-striped table-bordered no-wrap" data-page-length="10">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Resources</th>
                <th>Plan</th>
                <th>Unit</th>
                <th>Realization</th>
                <th>Unit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($resources as $index => $resource): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $resource['resource'] ?></td>
                    <td><?= $resource['plan'] ?></td>
                    <td><?= $resource['unit'] ?></td>
                    <td><?= $resource['realization'] ?></td>
                    <td><?= $resource['unit'] ?></td>
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
    <div class="box-body">
        <table class="table table-sm table-hover table-striped table-bordered no-wrap table-scroll" data-page-length="10">
            <thead>
            <tr>
                <th rowspan="2" style="width: 30px">No</th>
                <th rowspan="2">Customer Name</th>
                <th rowspan="2">No Reference</th>
                <th rowspan="2">Item Name</th>
                <th rowspan="2">Location</th>
                <th rowspan="2">Special Equipment</th>
                <th rowspan="2">SPPB Date</th>
                <th rowspan="2">Inbound Date</th>
                <th colspan="3">Party</th>
                <th colspan="3">Realization</th>
                <th colspan="3">Left</th>
                <th rowspan="2">Achievement</th>
                <th rowspan="2">Remark</th>
            </tr>
            <tr>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inbounds as $index => $inbound): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $inbound['customer_name'] ?></td>
                    <td><?= substr($inbound['no_reference'], -6) ?></td>
                    <td><?= $inbound['item_name'] ?></td>
                    <td><?= if_empty($inbound['unloading_location'], '-') ?></td>
                    <td><?= str_replace(',', '<br>', if_empty($inbound['special_equipment'], '-')) ?></td>
                    <td><?= if_empty($inbound['sppb_date']) ?></td>
                    <td><?= if_empty($inbound['inbound_date'], '-') ?></td>
                    <td><?= if_empty($inbound['party_20']) ?></td>
                    <td><?= if_empty($inbound['party_40']) ?></td>
                    <td><?= if_empty(numerical($inbound['party_lcl'], 2, true)) ?></td>
                    <td><?= if_empty($inbound['realization_20']) ?></td>
                    <td><?= if_empty($inbound['realization_40']) ?></td>
                    <td><?= if_empty(numerical($inbound['realization_lcl'], 2, true)) ?></td>
                    <td><?= if_empty($inbound['left_20']) ?></td>
                    <td><?= if_empty($inbound['left_40']) ?></td>
                    <td><?= if_empty(numerical($inbound['left_lcl'], 2, true)) ?></td>
                    <td><?= numerical($inbound['achievement'], 2, true) ?>%</td>
                    <td></td>
                </tr>
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
    <div class="box-body">
        <table class="table table-sm table-hover table-striped table-bordered no-wrap table-scroll" data-page-length="10">
            <thead>
            <tr>
                <th rowspan="2" style="width: 30px">No</th>
                <th rowspan="2">Customer Name</th>
                <th rowspan="2">No Reference / Ex Inbound</th>
                <th rowspan="2">Item Name</th>
                <th rowspan="2">Location</th>
                <th rowspan="2">Special Equipment</th>
                <th rowspan="2">SPPB Date</th>
                <th rowspan="2">Instruction Date</th>
                <th colspan="3">Party</th>
                <th colspan="3">Realization</th>
                <th colspan="3">Left</th>
                <th rowspan="2">Achievement</th>
                <th rowspan="2">Remark</th>
            </tr>
            <tr>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
                <th>20'</th>
                <th>40'</th>
                <th>LCL</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($outbounds as $index => $outbound): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $outbound['customer_name'] ?></td>
                    <td>
                        <?= substr($outbound['no_reference'], -6) ?> /
                        <?= substr($outbound['no_reference_inbound'], -6) ?>
                    </td>
                    <td><?= $outbound['item_name'] ?></td>
                    <td><?= if_empty($outbound['loading_location'], '-') ?></td>
                    <td><?= str_replace(',', '<br>', if_empty($outbound['special_equipment'], '-')) ?></td>
                    <td><?= if_empty($outbound['sppb_date'], '-') ?></td>
                    <td><?= if_empty($outbound['instruction_date'], '-') ?></td>
                    <td><?= if_empty($outbound['party_20']) ?></td>
                    <td><?= if_empty($outbound['party_40']) ?></td>
                    <td><?= if_empty(numerical($outbound['party_lcl'], 2, true)) ?></td>
                    <td><?= if_empty($outbound['realization_20']) ?></td>
                    <td><?= if_empty($outbound['realization_40']) ?></td>
                    <td><?= if_empty(numerical($outbound['realization_lcl'], 2, true)) ?></td>
                    <td><?= if_empty($outbound['left_20']) ?></td>
                    <td><?= if_empty($outbound['left_40']) ?></td>
                    <td><?= if_empty(numerical($outbound['left_lcl'], 2, true)) ?></td>
                    <td><?= numerical($outbound['achievement'], 2, true) ?>%</td>
                    <td></td>
                </tr>
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
    <div class="box-header with-border">
        <h3 class="box-title">Statistic Summary</h3>
    </div>
    <div class="box-body">
        <table class="table table-sm table-hover table-striped table-bordered no-wrap" data-page-length="10">
            <thead>
            <tr>
                <th>LBR/TEUS</th>
                <th>LBR/LCL</th>
                <th>OPS/TEUS</th>
                <th>OPS/LCL</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= numerical($planRealization['lbr_teus'], 2, true) ?></td>
                <td><?= numerical($planRealization['lbr_lcl'], 2, true) ?></td>
                <td><?= numerical($planRealization['ops_lcl'], 2, true) ?></td>
                <td><?= numerical($planRealization['ops_teus'], 2, true) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>