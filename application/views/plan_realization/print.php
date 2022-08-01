<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> | Plan Realization</title>
    <style>
        @page { margin: 20px; }

        * {
            margin: 0;
            padding: 0;
        }

        body { font-size: 10px; font-family: sans-serif; margin: 20px; }

        p {
            line-height: 1.4;
        }

        table td, table th {
            padding: 2px 2px;
        }

        .table {
            border: 1px solid #777;
        }
        .table td, .table th {
            border-top: 1px solid #777;
            border-bottom: 1px solid #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #212529;
        }
        table table {
            font-size: 10px;
            margin: -1px 5px 10px;
        }
        .pt-md-0 {
            padding-top: 0;
        }
        .table-warning {
            background-color: #ffe9b8;
        }
        .table-danger {
            background-color: #f8cfce;
        }
        .table-error,
        .table-error td {
            background-color: #ffb3b2 !important;
        }
        .table-success {
            background-color: #daf9b8;
        }
        .table-secondary {
            background-color: #f8f8f8;
        }
        .label {
            display: inline-block;
            padding: .2em .6em .3em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
        }
        .label-danger {
            background-color: #dd4b39 !important;
        }
        .label-success {
            background-color: #00a65a !important;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        hr {
            height:0;
            border: 0;
            border-top: 1px solid #dddddd;
            -webkit-box-sizing:content-box;
            -moz-box-sizing:content-box;
            box-sizing:content-box;
        }
    </style>
</head>
<body>
<div style="margin-bottom: 15px">
    <img src="<?= FCPATH . 'assets/app/img/layout/kop_email.jpg' ?>" style="width: 450px; height: 55px">
</div>

<h4 style="margin-bottom: 5px">I. PLAN & REALIZATION RESOURCES</h4>
<table class="table pull-left" style="width: 40%">
    <thead style="background-color: #92d050">
    <tr>
        <th style="width: 30px" class="text-center">No</th>
        <th>Resource</th>
        <th class="text-center">Plan</th>
        <th class="text-center">Unit</th>
        <th class="text-center table-warning">Last Realization</th>
        <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
            <th class="text-center table-danger">Close Realization</th>
        <?php endif; ?>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($resources as $index => $resource): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $resource['resource'] ?></td>
            <td class="text-center table-success"><?= if_empty($resource['plan'], 0) ?></td>
            <td class="text-center table-success"><?= if_empty($resource['unit'], '-') ?></td>
            <td class="text-center table-warning"><?= if_empty($resource['realization'], 0) ?></td>
            <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
                <td class="text-center table-danger"><?= if_empty($resource['close_realization'], '-') ?></td>
            <?php endif; ?>
            <td><?= if_empty($resource['description'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table class="text-center pull-right" style="width: 40%; margin-top: 15px">
    <thead>
    <tr style="background-color: #ff0066; color: #ffff00">
        <th>LBR/TEUS</th>
        <th>LBR/LCL</th>
        <th>OPS/TEUS</th>
        <th>OPS/LCL</th>
    </tr>
    </thead>
    <tbody>
    <tr style="color: #002060; font-size: 18px; background-color: #ffe6ef">
        <td><?= numerical($planRealization['lbr_teus'], 2, true) ?></td>
        <td><?= numerical($planRealization['lbr_lcl'], 2, true) ?></td>
        <td><?= numerical($planRealization['ops_lcl'], 2, true) ?></td>
        <td><?= numerical($planRealization['ops_teus'], 2, true) ?></td>
    </tr>
    </tbody>
</table>

<div class="clearfix"></div>
<br>

<h4 style="margin-bottom: 5px">II. PLAN DAN REALIZATION INBOUND</h4>
<table class="table" style="margin-bottom: 10px">
    <thead style="background-color: #92d050">
    <tr>
        <th rowspan="2" style="width: 30px" class="text-center">No</th>
        <th rowspan="2" style="width: 30px" class="text-center">Skip</th>
        <th rowspan="2">Customer Name</th>
        <th rowspan="2">No Reference</th>
        <th rowspan="2">TEP</th>
        <th rowspan="2">Item Name</th>
        <th rowspan="2">Location</th>
        <th rowspan="2">Realization Location</th>
        <th rowspan="2">SPPB Date</th>
        <th rowspan="2">Inbound Date</th>
        <th colspan="3" class="text-center">Party</th>
        <th colspan="3" class="text-center">Last Unload</th>
        <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
            <th colspan="3" class="table-warning text-center">Outstanding</th>
            <th colspan="3" class="table-danger text-center">Close Realization</th>
        <?php endif; ?>
        <th colspan="3" class="text-center" style="background-color: #fbe5d6">Left Total</th>
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
        <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
            <th class="table-warning">20'</th>
            <th class="table-warning">40'</th>
            <th class="table-warning">LCL</th>
            <th class="table-danger">20'</th>
            <th class="table-danger">40'</th>
            <th class="table-danger">LCL</th>
        <?php endif; ?>
        <th style="background-color: #fbe5d6">20'</th>
        <th style="background-color: #fbe5d6">40'</th>
        <th style="background-color: #fbe5d6">LCL</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($inbounds as $index => $inbound): ?>
        <tr class="<?= $inbound['is_skipped'] ? 'table-error' : '' ?>">
            <td class="text-center"><?= $index + 1 ?></td>
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
            <td><small><?= character_limiter($inbound['item_name'], 70) ?></small></td>
            <td><?= if_empty($inbound['unloading_location'], '-') ?></td>
            <td><?= if_empty($inbound['realization_location'], '-') ?></td>
            <td><?= if_empty($inbound['sppb_date']) ?></td>
            <td><?= if_empty($inbound['inbound_date'], '-') ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($inbound['party_20']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($inbound['party_40']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty(numerical($inbound['party_lcl'], 2, true)) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($inbound['realization_20']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($inbound['realization_40']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty(numerical($inbound['realization_lcl'], 2, true)) ?></td>
            <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
                <td class="table-warning text-center"><?= if_empty($inbound['outstanding_20']) ?></td>
                <td class="table-warning text-center"><?= if_empty($inbound['outstanding_40']) ?></td>
                <td class="table-warning text-center"><?= if_empty(numerical($inbound['outstanding_lcl'], 2, true)) ?></td>
                <td class="table-danger text-center"><?= if_empty($inbound['close_realization_20']) ?></td>
                <td class="table-danger text-center"><?= if_empty($inbound['close_realization_40']) ?></td>
                <td class="table-danger text-center"><?= if_empty(numerical($inbound['close_realization_lcl'], 2, true)) ?></td>
            <?php endif; ?>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty($inbound['left_20']) ?></td>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty($inbound['left_40']) ?></td>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty(numerical($inbound['left_lcl'], 2, true)) ?></td>
            <td class="text-center"><?= numerical($inbound['achievement'], 2, true) ?>%</td>
            <td><?= if_empty($inbound['description'], '-') ?></td>
        </tr>
        <?php if (!empty($inbound['resources'])): ?>
            <tr style="font-weight: bold">
                <td></td>
                <td></td>
                <td>Resources</td>
                <td>Unit</td>
                <td class="table-warning">Plan</td>
                <td class="table-danger">Realization</td>
                <td colspan="21"></td>
            </tr>
            <?php foreach ($inbound['resources'] as $resource): ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?= $resource['resource'] ?></td>
                    <td><?= $resource['unit'] ?></td>
                    <td class="table-warning"><?= if_empty($resource['plan'], 0) ?></td>
                    <td class="table-danger"><?= if_empty($resource['close_realization']) ?></td>
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

<table class="table text-center" style="width: 40%; margin-bottom: 20px; color: #ff0000">
    <tr class="success" style="font-size: 14px">
        <td class="lead"><?= $inboundSummary['target'] ?></td>
        <td class="lead"><?= $inboundSummary['realization'] ?></td>
        <td class="lead"><?= numerical($inboundSummary['achievement'], 2, true) ?>%</td>
    </tr>
    <tr style="background-color: #ffff00">
        <th>Target</th>
        <th>Realization</th>
        <th>Achievement</th>
    </tr>
</table>

<h4 style="margin-bottom: 5px">III. PLAN DAN REALIZATION OUTBOUND</h4>
<table class="table" style="margin-bottom: 10px">
    <thead style="background-color: #92d050">
    <tr>
        <th rowspan="2" style="width: 30px" class="text-center">No</th>
        <th rowspan="2" style="width: 30px" class="text-center">Skip</th>
        <th rowspan="2">Customer Name</th>
        <th rowspan="2">No Reference / Ex Inbound</th>
        <th rowspan="2">TEP</th>
        <th rowspan="2">Item Name</th>
        <th rowspan="2">Location</th>
        <th rowspan="2">Realization Location</th>
        <th rowspan="2">SPPB Date</th>
        <th rowspan="2">Instruction Date</th>
        <th colspan="3" class="text-center">Party</th>
        <th colspan="3" class="text-center">Last Load</th>
        <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
            <th colspan="3" class="table-warning text-center">Outstanding</th>
            <th colspan="3" class="table-danger text-center">Close Realization</th>
        <?php endif; ?>
        <th colspan="3" class="text-center" style="background-color: #fbe5d6">Left Total</th>
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
        <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
            <th class="table-warning">20'</th>
            <th class="table-warning">40'</th>
            <th class="table-warning">LCL</th>
            <th class="table-danger">20'</th>
            <th class="table-danger">40'</th>
            <th class="table-danger">LCL</th>
        <?php endif; ?>
        <th style="background-color: #fbe5d6">20'</th>
        <th style="background-color: #fbe5d6">40'</th>
        <th style="background-color: #fbe5d6">LCL</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($outbounds as $index => $outbound): ?>
        <tr class="<?= $outbound['is_skipped'] ? 'table-error' : '' ?>">
            <td class="text-center"><?= $index + 1 ?></td>
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
            <td><small><?= character_limiter($outbound['item_name'], 70) ?></small></td>
            <td><?= if_empty($outbound['loading_location'], '-') ?></td>
            <td><?= if_empty($outbound['realization_location'], '-') ?></td>
            <td><?= if_empty($outbound['sppb_date'], '-') ?></td>
            <td><?= if_empty($outbound['instruction_date'], '-') ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($outbound['party_20']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($outbound['party_40']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty(numerical($outbound['party_lcl'], 2, true)) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($outbound['realization_20']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty($outbound['realization_40']) ?></td>
            <td class="text-center" style="background-color: #daf9b8"><?= if_empty(numerical($outbound['realization_lcl'], 2, true)) ?></td>
            <?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
                <td class="table-warning text-center"><?= if_empty($outbound['outstanding_20']) ?></td>
                <td class="table-warning text-center"><?= if_empty($outbound['outstanding_40']) ?></td>
                <td class="table-warning text-center"><?= if_empty(numerical($outbound['outstanding_lcl'], 2, true)) ?></td>
                <td class="table-danger text-center"><?= if_empty($outbound['close_realization_20']) ?></td>
                <td class="table-danger text-center"><?= if_empty($outbound['close_realization_40']) ?></td>
                <td class="table-danger text-center"><?= if_empty(numerical($outbound['close_realization_lcl'], 2, true)) ?></td>
            <?php endif; ?>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty($outbound['left_20']) ?></td>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty($outbound['left_40']) ?></td>
            <td class="text-center" style="background-color: #fbe5d6"><?= if_empty(numerical($outbound['left_lcl'], 2, true)) ?></td>
            <td class="text-center"><?= numerical($outbound['achievement'], 2, true) ?>%</td>
            <td><?= if_empty($outbound['description'], '-') ?></td>
        </tr>
        <?php if (!empty($outbound['resources'])): ?>
            <tr style="font-weight: bold">
                <td></td>
                <td></td>
                <td>Resources</td>
                <td>Unit</td>
                <td class="table-warning">Plan</td>
                <td class="table-danger">Realization</td>
                <td colspan="21"></td>
            </tr>
            <?php foreach ($outbound['resources'] as $resource): ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?= $resource['resource'] ?></td>
                    <td><?= $resource['unit'] ?></td>
                    <td class="table-warning"><?= if_empty($resource['plan'], 0) ?></td>
                    <td class="table-danger"><?= if_empty($resource['close_realization']) ?></td>
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

<table class="table text-center" style="width: 40%; color: #ff0000">
    <tr class="success" style="font-size: 14px">
        <td class="lead"><?= $outboundSummary['target'] ?></td>
        <td class="lead"><?= $outboundSummary['realization'] ?></td>
        <td class="lead"><?= numerical($outboundSummary['achievement'], 2, true) ?>%</td>
    </tr>
    <tr style="background-color: #ffff00">
        <th>Target</th>
        <th>Realization</th>
        <th>Achievement</th>
    </tr>
</table>

<?php if(!isset($withoutCloseRealization) || !$withoutCloseRealization): ?>
    <br>
    <br>

    <table class="table">
        <thead style="background-color: #002060; color: #ffffff">
        <tr>
            <th>Analysis</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <?= if_empty($planRealization['analysis'], '-') ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>

